<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        // 1. Summary Metrics
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $overdueProjects = Project::overdue()->count();

        $averageProgress = (int) round(Project::avg('progress') ?? 0);
        $totalBudget = Project::sum('budget');
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $taskCompletionRate = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        // 2. Recent Projects
        $recentProjects = Project::with(['manager', 'tasks'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Projects Needing Attention (overdue, due in 7 days, or having overdue tasks)
        $needingAttentionProjects = Project::with(['manager', 'tasks'])
            ->where('status', '!=', 'completed')
            ->where(function ($q) use ($today) {
                $q->where('deadline', '<', $today)
                    ->orWhere('status', 'overdue')
                    ->orWhereBetween('deadline', [$today, $today->copy()->addDays(7)])
                    ->orWhereHas('tasks', function ($t) use ($today) {
                        $t->where('status', '!=', 'done')
                            ->where('due_date', '<', $today);
                    });
            })
            ->take(5)
            ->get();

        // 4. Chart 1: Project Progress (Bar Chart) - 6 active projects
        $progressProjects = Project::where('status', '!=', 'completed')
            ->orderByDesc('progress')
            ->take(6)
            ->get(['name', 'progress', 'code']);

        $barChartLabels = $progressProjects->map(fn ($p) => mb_strimwidth($p->name, 0, 22, '...'))->values();
        $barChartData = $progressProjects->pluck('progress')->values();

        // 5. Chart 2: Status Breakdown (Donut Chart)
        $statusCounts = [
            'Belum Dimulai' => Project::where('status', 'not_started')->count(),
            'Berjalan' => Project::where('status', 'in_progress')->count(),
            'Selesai' => Project::where('status', 'completed')->count(),
            'Terlambat' => Project::overdue()->count(),
            'Ditunda' => Project::where('status', 'on_hold')->count(),
        ];

        // 6. Chart 3: Monthly Trend (Line Chart - last 6 months created vs completed)
        $monthlyLabels = [];
        $monthlyCreated = [];
        $monthlyCompleted = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->translatedFormat('M Y');
            $monthlyLabels[] = $monthKey;

            $monthlyCreated[] = Project::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $monthlyCompleted[] = Project::where('status', 'completed')
                ->whereYear('completed_at', $month->year)
                ->whereMonth('completed_at', $month->month)
                ->count();
        }

        // Recent Activities
        $recentActivities = Activity::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'overdueProjects',
            'averageProgress',
            'totalBudget',
            'taskCompletionRate',
            'recentProjects',
            'needingAttentionProjects',
            'barChartLabels',
            'barChartData',
            'statusCounts',
            'monthlyLabels',
            'monthlyCreated',
            'monthlyCompleted',
            'recentActivities'
        ));
    }
}
