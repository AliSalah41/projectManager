<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskDependency>
 */
class TaskDependencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => \App\Models\Task::factory(), // Create a new task
            'dependency_id' => \App\Models\Task::factory(), // Create another new task as a dependency
        ];
    }
}
