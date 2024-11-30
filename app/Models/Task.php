<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'status', 'priority', 
        'project_id', 'parent_task_id', 'assigned_user_id', 'start_date', 'end_date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function history()
    {
        return $this->hasMany(TaskHistory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($task) {
            $task->subtasks()->delete();
            $task->dependencies()->detach();
            TaskDependency::where('dependency_id', $task->id)->delete();
        });
    }

    public function hasCircularDependency($dependencyId)
    {
        $visited = [];

        $stack = [$this];

        while (!empty($stack)) {
            $currentTask = array_pop($stack);

            if ($currentTask->id == $dependencyId) {
                return true;
            }

            $visited[$currentTask->id] = true;

            foreach ($currentTask->dependencies as $dependency) {
                if (!isset($visited[$dependency->id])) {
                    $stack[] = $dependency;
                }
            }
        }

        return false;
    }

    protected function checkDependenciesCompleted($task)
    {
        // If the task has no dependencies, consider it as completed.
        if (!$task->dependencies || $task->dependencies->isEmpty()) {
            return true;
        }

        // Check if all dependencies are marked as "completed".
        foreach ($task->dependencies as $dependency) {
            if ($dependency->status !== 'completed') {
                return false; // At least one dependency is not completed.
            }
        }

        return true; // All dependencies are completed.
    }



}
