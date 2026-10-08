<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ProjectNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'priority', 'assigned_to', 'project_id', 'due_date']);
        $viewMode = $request->get('view', 'kanban'); // 'list' or 'kanban'

        $query = Task::with(['project', 'assignee'])->filter($filters);

        // Sorting
        $sort = $request->get('sort', 'due_date');
        $direction = $request->get('direction', 'asc');
        $validSorts = ['title', 'due_date', 'priority', 'status', 'created_at'];

        if (in_array($sort, $validSorts, true)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('due_date', 'asc');
        }

        if ($viewMode === 'kanban') {
            $tasks = $query->get();
            $tasksByStatus = [
                'todo' => $tasks->where('status', 'todo')->values(),
                'in_progress' => $tasks->where('status', 'in_progress')->values(),
                'review' => $tasks->where('status', 'review')->values(),
                'done' => $tasks->where('status', 'done')->values(),
            ];
            $paginatedTasks = null;
        } else {
            $paginatedTasks = $query->paginate(15)->withQueryString();
            $tasksByStatus = [];
        }

        $projects = Project::orderBy('name')->get(['id', 'name', 'code']);
        $users = User::orderBy('name')->get();

        $counts = [
            'total' => Task::count(),
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        return view('tasks.index', compact('tasksByStatus', 'paginatedTasks', 'projects', 'users', 'filters', 'viewMode', 'counts'));
    }

    public function store(TaskStoreRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', [Task::class, $project]);

        $validated = $request->validated();
        $validated['project_id'] = $project->id;

        if ($validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);
        $project->recalculateProgress();

        // Notify assigned user if different from author
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $assignee = User::find($task->assigned_to);
            $assignee?->notify(new ProjectNotification(
                title: 'Penugasan Task Baru',
                message: auth()->user()->name.' menugaskan task "'.$task->title.'" pada proyek '.$project->name,
                type: 'info',
                url: route('projects.show', ['project' => $project->id, 'tab' => 'tasks']),
                icon: 'check-square'
            ));
        }

        Activity::log(
            auth()->id(),
            $project->id,
            'created',
            'task',
            $task->id,
            auth()->user()->name.' menambahkan task "'.$task->title.'" pada proyek '.$project->name
        );

        return back()->with('success', 'Task "'.$task->title.'" berhasil ditambahkan.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:todo,in_progress,review,done'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        $oldStatus = $task->status;
        if ($validated['status'] === 'done' && ! $task->completed_at) {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $task->project->recalculateProgress();

        $desc = auth()->user()->name.' memperbarui task "'.$task->title.'"';
        if ($oldStatus !== $task->status) {
            $desc = auth()->user()->name.' mengubah status task "'.$task->title.'" menjadi '.$task->status_label;
        }

        Activity::log(
            auth()->id(),
            $task->project_id,
            'updated',
            'task',
            $task->id,
            $desc
        );

        return back()->with('success', 'Task berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,review,done'],
        ]);

        $task->status = $validated['status'];
        if ($validated['status'] === 'done') {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        $task->save();
        $task->project->recalculateProgress();

        Activity::log(
            auth()->id(),
            $task->project_id,
            'status_changed',
            'task',
            $task->id,
            auth()->user()->name.' mengubah status task "'.$task->title.'" menjadi '.$task->status_label
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diubah.',
                'task' => $task,
                'project_progress' => $task->project->progress,
            ]);
        }

        return back()->with('success', 'Status task berhasil diubah.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $project = $task->project;
        $title = $task->title;
        $task->delete();
        $project->recalculateProgress();

        Activity::log(
            auth()->id(),
            $project->id,
            'deleted',
            'task',
            null,
            auth()->user()->name.' menghapus task "'.$title.'"'
        );

        return back()->with('success', 'Task "'.$title.'" berhasil dihapus.');
    }
}
