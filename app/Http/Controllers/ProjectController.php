<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Activity;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'priority', 'manager_id', 'date_from', 'date_to', 'filter_preset']);
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $viewMode = $request->get('view', 'table'); // 'table' or 'grid'

        $query = Project::with(['manager', 'tasks', 'milestones'])
            ->filter($filters);

        // Quick preset filter from sidebar if requested
        if ($request->get('preset') === 'active') {
            $query->where('status', 'in_progress');
        } elseif ($request->get('preset') === 'completed') {
            $query->where('status', 'completed');
        } elseif ($request->get('preset') === 'overdue') {
            $query->overdue();
        }

        $validSorts = ['name', 'code', 'deadline', 'progress', 'created_at', 'budget'];
        if (in_array($sort, $validSorts, true)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $projects = $query->paginate(9)->withQueryString();

        $managers = User::whereIn('role', ['admin', 'project_manager'])->orderBy('name')->get();

        // Project counts for filter tabs
        $counts = [
            'all' => Project::count(),
            'active' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'overdue' => Project::overdue()->count(),
        ];

        return view('projects.index', compact('projects', 'managers', 'filters', 'sort', 'direction', 'viewMode', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        $managers = User::whereIn('role', ['admin', 'project_manager'])->orderBy('name')->get();

        // Auto-generate next project code candidate
        $lastId = Project::max('id') ?? 0;
        $suggestedCode = 'PRJ-'.date('Y').'-'.str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);

        return view('projects.create', compact('managers', 'suggestedCode'));
    }

    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if ($validated['status'] === 'completed' && empty($validated['completed_at'])) {
            $validated['completed_at'] = now();
        }

        $project = Project::create($validated);

        Activity::log(
            auth()->id(),
            $project->id,
            'created',
            'project',
            $project->id,
            auth()->user()->name.' membuat proyek baru "'.$project->name.'"'
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek "'.$project->name.'" berhasil ditambahkan.');
    }

    public function show(Request $request, Project $project): View
    {
        $project->load([
            'manager',
            'tasks.assignee',
            'milestones',
            'activities.user',
            'files.user',
        ]);

        $activeTab = $request->get('tab', 'overview');
        $taskView = $request->get('task_view', 'list'); // 'list' or 'kanban'

        $allUsers = User::orderBy('name')->get();

        // Calculate stats for this project
        $totalTasks = $project->tasks->count();
        $doneTasks = $project->tasks->where('status', 'done')->count();
        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();
        $todoTasks = $project->tasks->where('status', 'todo')->count();
        $reviewTasks = $project->tasks->where('status', 'review')->count();

        $milestonesTotal = $project->milestones->count();
        $milestonesCompleted = $project->milestones->where('status', 'completed')->count();

        return view('projects.show', compact(
            'project',
            'activeTab',
            'taskView',
            'allUsers',
            'totalTasks',
            'doneTasks',
            'inProgressTasks',
            'todoTasks',
            'reviewTasks',
            'milestonesTotal',
            'milestonesCompleted'
        ));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        $managers = User::whereIn('role', ['admin', 'project_manager'])->orderBy('name')->get();

        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $validated = $request->validated();
        if ($validated['status'] === 'completed' && ! $project->completed_at) {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $oldStatus = $project->status;
        $project->update($validated);

        $description = auth()->user()->name.' memperbarui data proyek "'.$project->name.'"';
        if ($oldStatus !== $project->status) {
            $description = auth()->user()->name.' mengubah status proyek "'.$project->name.'" menjadi '.$project->status_label;
        }

        Activity::log(
            auth()->id(),
            $project->id,
            'updated',
            'project',
            $project->id,
            $description
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Data proyek berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $projectName = $project->name;
        $projectId = $project->id;

        $project->delete();

        Activity::log(
            auth()->id(),
            null,
            'deleted',
            'project',
            $projectId,
            auth()->user()->name.' menghapus proyek "'.$projectName.'"'
        );

        return redirect()->route('projects.index')
            ->with('success', 'Proyek "'.$projectName.'" telah berhasil dihapus.');
    }

    public function uploadFile(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'file', 'max:20480'], // max 20MB
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'document.required' => 'Pilih file dokumen yang ingin diunggah.',
            'document.max' => 'Ukuran file tidak boleh melebihi 20MB.',
        ]);

        $file = $request->file('document');
        $fileName = $request->filled('name') ? $request->input('name') : $file->getClientOriginalName();
        $path = $file->store('project-files/'.$project->id, 'public');

        $projectFile = ProjectFile::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'name' => $fileName,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
        ]);

        Activity::log(
            auth()->id(),
            $project->id,
            'created',
            'file',
            $projectFile->id,
            auth()->user()->name.' mengunggah dokumen "'.$fileName.'" ke proyek '.$project->name
        );

        return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'files'])
            ->with('success', 'Dokumen berhasil diunggah.');
    }

    public function deleteFile(Project $project, ProjectFile $file): RedirectResponse
    {
        if ($file->project_id !== $project->id) {
            abort(403);
        }

        Storage::disk('public')->delete($file->file_path);
        $fileName = $file->name;
        $file->delete();

        Activity::log(
            auth()->id(),
            $project->id,
            'deleted',
            'file',
            null,
            auth()->user()->name.' menghapus dokumen "'.$fileName.'" dari proyek '.$project->name
        );

        return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'files'])
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
