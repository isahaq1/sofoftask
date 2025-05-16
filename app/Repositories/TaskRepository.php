<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllWithPagination(array $filters = [], ?string $sortField = null, string $direction = 'asc'): LengthAwarePaginator
    {
        $query = Task::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        // Apply sorting
        if ($sortField && in_array($sortField, ['due_date', 'created_at'])) {
            $query->orderBy($sortField, $direction);
        }

        return $query->with(['user', 'assignedUsers'])->paginate(10)->withQueryString();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->forceDelete();
    }

    public function assignUser(Task $task, int $userId): bool
    {
        if (!$task->assignedUsers()->where('user_id', $userId)->exists()) {
            return (bool) $task->assignedUsers()->attach($userId);
        }
        return false;
    }
}
