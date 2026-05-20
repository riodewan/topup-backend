<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'order_number'   => $this->order_number,
            'user'           => new UserResource($this->whenLoaded('user')),
            'guest_email'    => $this->guest_email,
            'guest_phone'    => $this->guest_phone,
            'product'        => new ProductResource($this->whenLoaded('product')),
            'target_id'      => $this->target_id,
            'quantity'       => $this->quantity,
            'price'          => (float) $this->price,
            'total'          => (float) $this->total,
            'total_display'  => 'Rp ' . number_format($this->total, 0, ',', '.'),
            'status'         => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_url'    => $this->payment_url,
            'provider_ref'   => $this->provider_ref,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
