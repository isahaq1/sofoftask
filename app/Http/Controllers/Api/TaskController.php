<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class TaskController extends ApiBaseController
{
    public function index(Request $request)
    {
        try {
            $query = Task::query();
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->has('due_date')) {
                $query->whereDate('due_date', $request->due_date);
            }

            // Apply sorting
            if ($request->has('sort')) {
                $sortField = $request->sort;
                $direction = 'asc';

                if (str_starts_with($sortField, '-')) {
                    $direction = 'desc';
                    $sortField = substr($sortField, 1);
                }

                if (in_array($sortField, ['due_date', 'created_at'])) {
                    $query->orderBy($sortField, $direction);
                }
            }

            // Get tasks with pagination
            $tasks = $query
                ->with(['user', 'assignedUsers'])
                ->paginate(10)
                ->withQueryString();

            $tasksdata = [
                'data' => TaskResource::collection($tasks->items()),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ];

            return $this->sendSuccess(
                data: $tasksdata,
                message: 'Tasks Fetched Successfully ',
                status: true,
                statusCode: 200
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203
            );
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = Task::create(array_merge(
                $request->validated(),
                ['user_id' => Auth::id()]
            ));

            return $this->sendSuccess(
                data: $task,
                message: 'Task Successfully Submited',
                status: true,
                statusCode: 201
            );
        } catch (ValidationException $ex) {
            return $this->sendError(
                errors: $ex->errors(),
                message: 'Validation failed',
                code: 422
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203
            );
        }
    }

    public function show(Task $task)
    {
        try {
            $task->load(['user', 'assignedUsers']);
            return $this->sendSuccess(
                data: new TaskResource($task),
                message: 'Tasks Details Fetch Successfully',
                status: true,
                statusCode: 200
            );
            return response()->json($task);
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203
            );
        }
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $task->update($request->validated());
            return $this->sendSuccess(
                data: new TaskResource($task),
                message: 'Tasks Successfully Updated',
                status: true,
                statusCode: 201
            );
        } catch (Exception $ex) {
            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203
            );
        }
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function assign(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = User::findOrFail($validated['user_id']);
            $task->assignedUsers()->sync([$user->id]);

            return $this->sendSuccess(
                data: [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                message: 'User successfully assigned to the task',
                status: true,
                statusCode: 200
            );
        } catch (\Illuminate\Validation\ValidationException $ex) {
            return $this->sendError(
                errors: $ex->errors(),
                message: 'Validation failed',
                code: 422
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
