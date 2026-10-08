<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'client',
        'manager_id',
        'start_date',
        'deadline',
        'budget',
        'priority',
        'status',
        'progress',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
            'completed_at' => 'datetime',
            'budget' => 'decimal:2',
            'progress' => 'integer',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('order')->orderBy('target_date');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class)->latest();
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', '!=', 'completed')
            ->where(function ($q) {
                $q->where('deadline', '<', Carbon::today())
                    ->orWhere('status', 'overdue');
            });
    }

    public function scopeNeedingAttention(Builder $query): Builder
    {
        return $query->where('status', '!=', 'completed')
            ->where(function ($q) {
                $q->where('deadline', '<', Carbon::today())
                    ->orWhere('status', 'overdue')
                    ->orWhereBetween('deadline', [Carbon::today(), Carbon::today()->addDays(7)])
                    ->orWhereHas('tasks', function ($taskQuery) {
                        $taskQuery->where('status', '!=', 'done')
                            ->where('due_date', '<', Carbon::today());
                    });
            });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['manager_id'])) {
            $query->where('manager_id', $filters['manager_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('deadline', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('deadline', '<=', $filters['date_to']);
        }

        return $query;
    }

    // Accessors & helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Belum Dimulai',
            'in_progress' => 'Sedang Berjalan',
            'on_hold' => 'Ditunda',
            'completed' => 'Selesai',
            'overdue' => 'Terlambat',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis',
            default => ucfirst($this->priority),
        };
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp '.number_format($this->budget, 0, ',', '.');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }

        return $this->deadline->isPast() || $this->status === 'overdue';
    }

    public function getDaysRemainingAttribute(): int
    {
        return (int) Carbon::today()->diffInDays($this->deadline, false);
    }

    public function recalculateProgress(): void
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks > 0) {
            $completedTasks = $this->tasks()->where('status', 'done')->count();
            $this->progress = (int) round(($completedTasks / $totalTasks) * 100);
            if ($this->progress === 100 && $this->status !== 'completed') {
                $this->status = 'completed';
                $this->completed_at = now();
            }
            $this->save();
        }
    }
}
