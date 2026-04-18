<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return [
            'id' => $this->id,
            'items' => $this->cartItems->map(fn ($item) => [
                'id' => $item->id,
                'productId' => $item->product_id,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'effectivePrice' => $item->product->price,
                    'imageUrl' => $item->product->image_url,
                    'stock' => $item->product->stock,
                    'brand' => $item->product->brand ? ['name' => $item->product->brand->name] : null,
                    'category' => $item->product->category ? ['name' => $item->product->category->name] : null,
                ],
            ]),
            'itemCount' => $this->cartItems->sum('quantity'),
            'subtotal' => $subtotal,
            'formattedSubtotal' => 'RM ' . number_format($subtotal / 100, 2),
        ];
    }
}
