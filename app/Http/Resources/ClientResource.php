<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
