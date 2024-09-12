<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'recipient' => $this->recipient,
            'bank_name' => $this->bank_name,
            'bank_number' => $this->bank_number,
            'image' => $this->image,
        ];
    }
}
