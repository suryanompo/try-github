<?php

namespace App\Http\Controllers;

use App\Http\Requests\MilestoneStoreRequest;
use App\Models\Activity;
use App\Models\Milestone;
use App\Models\Project;
use App\Notifications\ProjectNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MilestoneController extends Controller
{
    public function index(Request $request): View
    {
        $projectId = $request->get('project_id');
        $query = Milestone::with('project');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $milestones = $query->orderBy('target_date', 'asc')->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name', 'code']);

        return view('milestones.index', compact('milestones', 'projects', 'projectId'));
    }

    public function store(MilestoneStoreRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', [Milestone::class, $project]);

        $validated = $request->validated();
        $validated['project_id'] = $project->id;
        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $highestOrder = $project->milestones()->max('order') ?? 0;
        $validated['order'] = $highestOrder + 1;

        $milestone = Milestone::create($validated);

        Activity::log(
            auth()->id(),
            $project->id,
            'created',
            'milestone',
            $milestone->id,
            auth()->user()->name.' menambahkan milestone baru "'.$milestone->name.'"'
        );

        return back()->with('success', 'Milestone "'.$milestone->name.'" berhasil ditambahkan.');
    }

    public function update(Request $request, Milestone $milestone): RedirectResponse
    {
        Gate::authorize('update', $milestone);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        if ($validated['status'] === 'completed' && ! $milestone->completed_at) {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $milestone->update($validated);

        Activity::log(
            auth()->id(),
            $milestone->project_id,
            'updated',
            'milestone',
            $milestone->id,
            auth()->user()->name.' memperbarui milestone "'.$milestone->name.'"'
        );

        return back()->with('success', 'Milestone berhasil diperbarui.');
    }

    public function toggle(Milestone $milestone): RedirectResponse
    {
        Gate::authorize('update', $milestone);

        if ($milestone->status === 'completed') {
            $milestone->status = 'in_progress';
            $milestone->completed_at = null;
            $msg = 'Milestone ditandai sebagai sedang berjalan.';
        } else {
            $milestone->status = 'completed';
            $milestone->completed_at = now();
            $msg = 'Milestone "'.$milestone->name.'" berhasil diselesaikan!';

            // Notify project manager if someone else completed it
            if ($milestone->project->manager_id && $milestone->project->manager_id !== auth()->id()) {
                $milestone->project->manager?->notify(new ProjectNotification(
                    title: 'Milestone Diselesaikan',
                    message: auth()->user()->name.' menyelesaikan milestone "'.$milestone->name.'" pada proyek '.$milestone->project->name,
                    type: 'success',
                    url: route('projects.show', ['project' => $milestone->project_id, 'tab' => 'milestones']),
                    icon: 'flag'
                ));
            }
        }

        $milestone->save();

        Activity::log(
            auth()->id(),
            $milestone->project_id,
            'completed',
            'milestone',
            $milestone->id,
            auth()->user()->name.' '.($milestone->status === 'completed' ? 'menyelesaikan' : 'mengubah status').' milestone "'.$milestone->name.'"'
        );

        return back()->with('success', $msg);
    }

    public function destroy(Milestone $milestone): RedirectResponse
    {
        Gate::authorize('delete', $milestone);

        $name = $milestone->name;
        $projectId = $milestone->project_id;
        $milestone->delete();

        Activity::log(
            auth()->id(),
            $projectId,
            'deleted',
            'milestone',
            null,
            auth()->user()->name.' menghapus milestone "'.$name.'"'
        );

        return back()->with('success', 'Milestone "'.$name.'" berhasil dihapus.');
    }
}
