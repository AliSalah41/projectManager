<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'status', 'updated_by'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
