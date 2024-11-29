<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Project::factory(10)->create()->each(function ($project) {
            // Create 5 tasks per project
            Task::factory(5)->create(['project_id' => $project->id])->each(function ($task) {
                // Create 2 subtasks per task
                Task::factory(2)->create(['parent_task_id' => $task->id, 'project_id' => $task->project_id]);
            });
        });

        // Create task dependencies
        TaskDependency::factory(10)->create();
    }
}
