<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

        return response()->json(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create(array_merge(
            $request->validated(),
            ['user_id' => Auth::id()]
        ));
        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        $task->load(['user', 'assignedUsers']);
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        // $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);
        $task->assignedUsers()->sync([$user->id]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }
}
