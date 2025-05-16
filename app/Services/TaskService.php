<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasks(array $filters = [], ?string $sortField = null, string $direction = 'asc'): LengthAwarePaginator
    {
        return $this->taskRepository->getAllWithPagination($filters, $sortField, $direction);
    }

    public function createTask(array $data, int $userId): Task
    {
        $data['user_id'] = $userId;
        return $this->taskRepository->create($data);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $this->taskRepository->update($task, $data);
    }

    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function assignTaskToUser(Task $task, int $userId): bool
    {
        return $this->taskRepository->assignUser($task, $userId);
    }

    public function changeTaskStatus(Task $task, string $status): Task
    {
        $this->taskRepository->update($task, ['status' => $status]);
        return $task->fresh();
    }
}
