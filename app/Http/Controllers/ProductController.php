<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function index(ProductFilterRequest $request): Response
    {
        $products = $this->productService->getProductsForUser(
            $request->user(),
            $request->validated(),
        );

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['brand_id', 'category_id', 'search']),
        ]);
    }
}
