<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminClientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'user' => new AdminUserResource($this->user),
        ];
    }
}
