<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2024-12-31',
            'status' => 'Todo',
            'priority' => 'High',
        ];

        $response = $this
            ->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response
            ->assertStatus(201)
            ->assertJsonFragment($taskData);
    }

    public function test_user_can_list_tasks(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->getJson('/api/tasks');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'due_date',
                        'status',
                        'priority',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_user_can_filter_tasks(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->getJson('/api/tasks?status=Todo&priority=High');

        $response->assertStatus(200);
    }

    public function test_user_can_sort_tasks(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->getJson('/api/tasks?sort=-due_date');

        $response->assertStatus(200);
    }

    public function test_user_can_update_task(): void
    {
        $updateData = ['title' => 'Updated Title'];

        $response = $this
            ->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}", $updateData);

        $response
            ->assertStatus(200)
            ->assertJsonFragment($updateData);
    }

    public function test_user_can_delete_task(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->deleteJson("/api/tasks/{$this->task->id}");
        // Debug response content since we're getting 404

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $this->task->id]);
    }
}
