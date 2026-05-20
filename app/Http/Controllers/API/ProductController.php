<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * GET /api/products
     * Ambil daftar produk aktif (public). Opsional filter ?game_id=X
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $gameId  = $request->integer('game_id') ?: null;

        $products = $this->productService->getAllActive($perPage, $gameId);

        return ApiResponse::paginated($products, 'Daftar produk berhasil diambil.');
    }
}
