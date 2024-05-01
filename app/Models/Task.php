<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'assignee',
        'user_id',
        'parent_task_id ', // which task it depends on
    ];

    public $timestamps = true;

    public function dependencies(): HasMany {
        return $this->hasMany(__CLASS__, 'parent_task_id', 'id');
    }

    public function parenttask(): BelongsTo {
        return $this->belongsTo(__CLASS__, 'parent_task_id', 'id');
    }

    public function withdependencies(): self {
        return $this->with('dependencies')->first();
    }
}
