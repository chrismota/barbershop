<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminSchedulingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'client_id' => $this->client_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
