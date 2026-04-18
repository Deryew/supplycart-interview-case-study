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
            'items' => $this->relationLoaded('orderItems')
                ? $this->orderItems->map(fn ($item) => [
                    'id' => $item->id,
                    'productId' => $item->product_id,
                    'productName' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unitPrice' => $item->unit_price,
                    'totalPrice' => $item->total_price,
                    'formattedUnitPrice' => 'RM ' . number_format($item->unit_price / 100, 2),
                    'formattedTotalPrice' => 'RM ' . number_format($item->total_price / 100, 2),
                ])
                : [],
            'createdAt' => $this->created_at->toISOString(),
            'formattedDate' => $this->created_at->format('d M Y, h:i A'),
        ];
    }
}
