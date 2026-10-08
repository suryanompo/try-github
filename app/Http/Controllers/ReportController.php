<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view-reports');

        $filters = $request->only(['status', 'priority', 'manager_id', 'date_from', 'date_to']);

        $query = Project::with(['manager', 'tasks', 'milestones'])->filter($filters);
        $projects = $query->get();

        // Key Metrics
        $totalProjects = $projects->count();
        $totalBudget = $projects->sum('budget');
        $averageProgress = $totalProjects > 0 ? (int) round($projects->avg('progress')) : 0;
        $overdueCount = $projects->filter(fn ($p) => $p->is_overdue)->count();

        // Tasks calculation across filtered projects
        $projectIds = $projects->pluck('id');
        $totalTasks = Task::whereIn('project_id', $projectIds)->count();
        $completedTasks = Task::whereIn('project_id', $projectIds)->where('status', 'done')->count();
        $taskCompletionRate = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        // Breakdown by status
        $statusBreakdown = [
            'Belum Dimulai' => $projects->where('status', 'not_started')->count(),
            'Sedang Berjalan' => $projects->where('status', 'in_progress')->count(),
            'Selesai' => $projects->where('status', 'completed')->count(),
            'Terlambat' => $projects->where('status', 'overdue')->count(),
            'Ditunda' => $projects->where('status', 'on_hold')->count(),
        ];

        // Breakdown by priority
        $priorityBreakdown = [
            'Rendah' => $projects->where('priority', 'low')->count(),
            'Sedang' => $projects->where('priority', 'medium')->count(),
            'Tinggi' => $projects->where('priority', 'high')->count(),
            'Kritis' => $projects->where('priority', 'critical')->count(),
        ];

        // PIC breakdown
        $picPerformance = $projects->groupBy('manager_id')->map(function ($items, $managerId) {
            $manager = User::find($managerId);

            return [
                'name' => $manager ? $manager->name : 'Tanpa PIC',
                'department' => $manager ? $manager->department : '-',
                'total_projects' => $items->count(),
                'avg_progress' => (int) round($items->avg('progress')),
                'completed' => $items->where('status', 'completed')->count(),
                'overdue' => $items->filter(fn ($p) => $p->is_overdue)->count(),
            ];
        })->values();

        $managers = User::whereIn('role', ['admin', 'project_manager'])->orderBy('name')->get();

        return view('reports.index', compact(
            'projects',
            'totalProjects',
            'totalBudget',
            'averageProgress',
            'overdueCount',
            'totalTasks',
            'completedTasks',
            'taskCompletionRate',
            'statusBreakdown',
            'priorityBreakdown',
            'picPerformance',
            'managers',
            'filters'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        Gate::authorize('view-reports');

        $filters = $request->only(['status', 'priority', 'manager_id', 'date_from', 'date_to']);
        $projects = Project::with('manager')->filter($filters)->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan_proyek_'.date('Y-m-d_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            fputcsv($file, [
                'Kode Proyek',
                'Nama Proyek',
                'Instansi / Klien',
                'Penanggung Jawab (PIC)',
                'Prioritas',
                'Status',
                'Progress (%)',
                'Anggaran (Rp)',
                'Tanggal Mulai',
                'Deadline',
            ]);

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->code,
                    $project->name,
                    $project->client ?? '-',
                    $project->manager?->name ?? 'Belum Ditentukan',
                    $project->priority_label,
                    $project->status_label,
                    $project->progress.'%',
                    $project->budget,
                    $project->start_date?->format('d/m/Y') ?? '-',
                    $project->deadline?->format('d/m/Y') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
