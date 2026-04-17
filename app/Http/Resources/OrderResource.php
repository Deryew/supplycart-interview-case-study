<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderNumber' => $this->order_number,
            'totalAmount' => $this->total_amount,
            'formattedTotal' => 'RM ' . number_format($this->total_amount / 100, 2),
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'createdAt' => $this->created_at->toISOString(),
            'formattedDate' => $this->created_at->format('d M Y, h:i A'),
        ];
    }
}
