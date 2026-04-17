<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->product_id,
            'productName' => $this->product_name,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unit_price,
            'totalPrice' => $this->total_price,
            'formattedUnitPrice' => 'RM ' . number_format($this->unit_price / 100, 2),
            'formattedTotalPrice' => 'RM ' . number_format($this->total_price / 100, 2),
        ];
    }
}
