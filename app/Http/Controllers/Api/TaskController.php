<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class TaskController extends ApiBaseController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        try {
            $filters = array_filter([
                'status' => $request->get('status'),
                'priority' => $request->get('priority'),
                'due_date' => $request->get('due_date'),
            ]);

            $sortField = null;
            $direction = 'asc';
            if ($request->has('sort')) {
                $sortField = $request->sort;
                if (str_starts_with($sortField, '-')) {
                    $direction = 'desc';
                    $sortField = substr($sortField, 1);
                }
            }

            $tasks = $this->taskService->getAllTasks($filters, $sortField, $direction);

            return $this->sendSuccess(
                data: [
                    'data' => TaskResource::collection($tasks->items()),
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total()
                ],
                message: 'Tasks Fetched Successfully',
                statusCode: 200
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong',
                code: 500
            );
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = $this->taskService->createTask(
                $request->validated(),
                Auth::id()
            );

            return $this->sendSuccess(
                data: new TaskResource($task),
                message: 'Task Successfully Created',
                statusCode: 201
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong',
                code: 500
            );
        }
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $this->taskService->updateTask($task, $request->validated());

            return $this->sendSuccess(
                data: new TaskResource($task),
                message: 'Task Successfully Updated',
                statusCode: 200
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong',
                code: 500
            );
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);
            return response()->json(null, 204);
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong',
                code: 500
            );
        }
    }

    public function assign(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $rtask = $this->taskService->assignTaskToUser($task, $validated['user_id']);

            if ($rtask == false) {
                return $this->sendSuccess(
                    data: new TaskResource($task),
                    message: 'User already assigned to the task',
                    statusCode: 200
                );
            }

            return $this->sendSuccess(
                data: new TaskResource($task),
                message: 'User successfully assigned to the task',
                statusCode: 200
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong',
                code: 500
            );
        }
    }
}
