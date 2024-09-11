<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeSpaceResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'address' => $this->address,
            'duration' => $this->duration,
            'price' => $this->price,
            'thumbnail' => $this->thumbnail,
            'about' => $this->about,
            'rating_avg' => $this->ratings_avg_rate,
            'rating_count' => $this->ratings_count,
            'city' => new CityResource($this->whenLoaded('city')), // kalo new ambil satu data seperti first()
            'photos' => OfficeSpacePhotoResource::collection($this->whenLoaded('photos')), // kalo collection ambil all data seperti get()
            'benefits' => OfficeSpaceBenefitResource::collection($this->whenLoaded('benefits')),
            'sales' => SalesResource::collection($this->whenLoaded('sales')),
            'features' => FeatureResource::collection($this->whenLoaded('features')),
        ];
    }
}
