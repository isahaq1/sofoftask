<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_assigned_to_user(): void
    {
        // Create a task owner and an assignee
        $owner = User::factory()->create();

        $assignee = User::factory()->create();

        // Create a task
        $task = Task::factory()->create([
            'user_id' => $owner->id
        ]);

        // Assign the task
        $task->assignedUsers()->sync([$assignee->id]);

        // Assert the task is assigned correctly
        $this->assertTrue($task->assignedUsers->contains($assignee));
        $this->assertEquals(1, $task->assignedUsers->count());
    }

    public function test_task_can_be_reassigned_to_different_user(): void
    {
        // Create users
        $owner = User::factory()->create();
        $firstAssignee = User::factory()->create();
        $secondAssignee = User::factory()->create();

        // Create and assign task to first user
        $task = Task::factory()->create([
            'user_id' => $owner->id
        ]);
        $task->assignedUsers()->sync([$firstAssignee->id]);

        // Reassign to second user
        $task->assignedUsers()->sync([$secondAssignee->id]);

        // Assert the task is reassigned correctly
        $this->assertTrue($task->assignedUsers->contains($secondAssignee));
        $this->assertFalse($task->assignedUsers->contains($firstAssignee));
        $this->assertEquals(1, $task->assignedUsers->count());
    }

    public function test_task_assignment_preserves_ownership(): void
    {
        // Create users
        $owner = User::factory()->create();
        $assignee = User::factory()->create();

        // Create task
        $task = Task::factory()->create([
            'user_id' => $owner->id
        ]);

        // Assign task
        $task->assignedUsers()->sync([$assignee->id]);

        // Assert ownership remains unchanged
        $this->assertEquals($owner->id, $task->user_id);
        $this->assertTrue($task->assignedUsers->contains($assignee));
    }
}
