<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in progress', 'completed']),
            'priority' => $this->faker->numberBetween(1, 5),
            'project_id' => \App\Models\Project::factory(), // Create a related project
            'parent_task_id' => null, // Set to null by default for top-level tasks
            'assigned_user_id' => \App\Models\User::factory(), // Assign a user
            'start_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 weeks'),
        ];
    }
}
