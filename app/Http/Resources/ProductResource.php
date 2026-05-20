<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'game_id'       => $this->game_id,
            'game'          => new GameResource($this->whenLoaded('game')),
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'price'         => (float) $this->price,
            'price_display' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'stock'         => $this->stock,
            'provider'      => $this->provider,
            'provider_code' => $this->provider_code,
            'type'          => $this->type,
            'status'        => $this->status,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
