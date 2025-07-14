<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Task extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'title',
        'description',
        'status',
        'is_completed',
        'priority',
        'due_date',
        'user_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
        'priority' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active tasks
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where('is_completed', false);
    }

    // Scope for tasks by priority
    public function scopeByPriority(Builder $query) : Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    // Scope for tasks by user
    public function scopeForUser(Builder $query, $userId) : Builder
    {
        return $query->where('user_id', $userId);
    }


    // Automatically set user_id when creating a task
    protected static function booted(): void
    {
        // Automatically set user_id to authenticated user when creating a task
        static::creating(function ($task) {
            if (auth()->check() && !$task->user_id) {
                $task->user_id = auth()->id();
            }
        });
    }

    // Accessor for priority label
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            0 => 'low',
            1 => 'medium',
            2 => 'high',
            default => 'unknown',
        };
    }

}
