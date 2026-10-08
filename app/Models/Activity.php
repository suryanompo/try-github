<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'action',
        'object_type',
        'object_id',
        'description',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function log(
        ?int $userId,
        ?int $projectId,
        string $action,
        string $objectType,
        ?int $objectId,
        string $description,
        ?array $properties = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'project_id' => $projectId,
            'action' => $action,
            'object_type' => $objectType,
            'object_id' => $objectId,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
