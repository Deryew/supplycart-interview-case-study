<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getProductsForUser(User $user, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return Product::query()
            ->with(['brand', 'category'])
            ->select('products.*')
            ->leftJoin('user_prices', function ($join) use ($user) {
                $join->on('products.id', '=', 'user_prices.product_id')
                    ->where('user_prices.user_id', '=', $user->id);
            })
            ->addSelect(DB::raw('COALESCE(user_prices.price, products.price) as effective_price'))
            ->where('products.is_active', true)
            ->when($filters['brand_id'] ?? null, fn ($q, $brandId) => $q->where('products.brand_id', $brandId))
            ->when($filters['category_id'] ?? null, fn ($q, $categoryId) => $q->where('products.category_id', $categoryId))
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where('products.name', 'like', "%{$search}%"))
            ->orderBy('products.name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
