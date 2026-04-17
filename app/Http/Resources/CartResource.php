<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('cartItems');

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($items),
            'itemCount' => $this->cartItems->sum('quantity'),
            'subtotal' => $this->cartItems->sum(fn ($item) => $item->quantity * $item->product->price),
            'formattedSubtotal' => 'RM ' . number_format(
                $this->cartItems->sum(fn ($item) => $item->quantity * $item->product->price) / 100,
                2,
            ),
        ];
    }
}
