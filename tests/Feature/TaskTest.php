<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    public function test_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'priority' => 1,
            'project_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 1,
            'start_date' => '2024-11-01',
            'end_date' => '2024-11-10',
        ];

        $response = $this->postJson('/api/task.store', $taskData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Task successfully created',
                    'status' => true
                ]);
    }

    public function test_task_cannot_move_to_in_progress_or_completed_unless_all_dependencies_are_completed()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::create([
            'name' => 'Test Task with Dependency',
            'description' => 'A task with dependencies',
            'status' => 'pending',
            'priority' => 1,
            'project_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 1,
            'start_date' => '2024-11-01',
            'end_date' => '2024-11-10',
        ]);

        $dependencyTask = Task::create([
            'name' => 'Dependency Task',
            'status' => 'pending',
            'priority' => 1,
            'project_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 1,
            'start_date' => '2024-11-01',
            'end_date' => '2024-11-10',
        ]);

        $task->dependencies()->attach($dependencyTask);

        $response = $this->putJson("/api/task.update/{$task->id}", ['status' => 'in progress']);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Task cannot move to "in progress" or "completed" until all dependencies are completed.',
                    'status' => false
                ]);
    }
    public function test_task_with_subtasks_cannot_be_completed_unless_all_subtasks_are_completed()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $parentTask = Task::create([
            'name' => 'Parent Task',
            'status' => 'in progress',
            'project_id' => 1,
            'assigned_user_id' => 1,
            'start_date' => '2024-11-01',
            'end_date' => '2024-11-10',
        ]);

        $subTask = Task::create([
            'name' => 'Sub Task',
            'status' => 'pending',
            'parent_task_id' => $parentTask->id,
            'project_id' => 1,
            'assigned_user_id' => 1,
            'start_date' => '2024-11-01',
            'end_date' => '2024-11-10',
        ]);

        $response = $this->putJson("/api/task.update/{$parentTask->id}", ['status' => 'completed']);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Task cannot be marked as "completed" until all subtasks are marked as "completed."',
                    'status' => false
                ]);
    }

    public function test_deleting_task_removes_dependencies_and_subtasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::create([
            'name' => 'Task with Dependencies and Subtasks',
            'status' => 'pending',
            'project_id' => 1,
            'assigned_user_id' => 1,
        ]);

        $dependencyTask = Task::create([
            'name' => 'Dependency Task',
            'status' => 'completed',
            'project_id' => 1,
            'assigned_user_id' => 1,
        ]);
        $task->dependencies()->attach($dependencyTask);

        $subTask = Task::create([
            'name' => 'Subtask Task',
            'status' => 'completed',
            'parent_task_id' => $task->id,
            'project_id' => 1,
            'assigned_user_id' => 1,
        ]);

        $response = $this->deleteJson("/api/task.delete/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task and its related data successfully deleted.',
                'status' => true
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('task_dependencies', ['task_id' => $task->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $subTask->id]);
    }

    public function test_task_circular_dependency_is_not_allowed()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskA = Task::create([
            'name' => 'Task A',
            'status' => 'pending',
            'project_id' => 1,
            'assigned_user_id' => 1,
        ]);
    
        $taskB = Task::create([
            'name' => 'Task B',
            'status' => 'pending',
            'project_id' => 1,
            'assigned_user_id' => 1,
        ]);
    
        
        $taskA->dependencies()->attach($taskB);
        $taskB->dependencies()->attach($taskA);
    
        $response = $this->putJson("/api/task.update/{$taskA->id}", ['status' => 'in progress']);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Task cannot move to "in progress" or "completed" until all dependencies are completed.',
                    'status' => false
                ]);
    }
    


}
