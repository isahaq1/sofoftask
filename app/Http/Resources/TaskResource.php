<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date->format('Y-m-d'),  // Format the date without time
            'status' => $this->status,
            'priority' => $this->priority,
            'user_id' => $this->user_id,
            'created_by' => data_get($this, 'user.name'),
            'assigned_users' => $this->assignedUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
