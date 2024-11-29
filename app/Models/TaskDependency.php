<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'dependency_id'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function dependency()
    {
        return $this->belongsTo(Task::class, 'dependency_id');
    }
}
