<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getAllWithPagination(array $filters = [], ?string $sortField = null, string $direction = 'asc'): LengthAwarePaginator;
    public function create(array $data): Task;
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
    public function assignUser(Task $task, int $userId): bool;
}
