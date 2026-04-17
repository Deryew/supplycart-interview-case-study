<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $effectivePrice = $this->effective_price ?? $this->price;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'effectivePrice' => (int) $effectivePrice,
            'formattedPrice' => 'RM ' . number_format($effectivePrice / 100, 2),
            'originalFormattedPrice' => $effectivePrice !== $this->price
                ? 'RM ' . number_format($this->price / 100, 2)
                : null,
            'hasDiscount' => (int) $effectivePrice !== $this->price,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'imageUrl' => $this->image_url,
            'stock' => $this->stock,
            'isActive' => $this->is_active,
        ];
    }
}
