<?php

namespace App\Http\Controllers\API\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * GET /api/admin/products
     * Daftar semua produk (termasuk inactive).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage  = $request->integer('per_page', 15);
        $products = $this->productService->getAll($perPage);

        return ApiResponse::paginated($products, 'Daftar produk berhasil diambil.');
    }

    /**
     * POST /api/admin/products
     * Tambah produk baru.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->store($request->validated());

            return ApiResponse::created(new ProductResource($product->load('game')), 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan produk: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/admin/products/{product}
     * Detail produk by ID.
     */
    public function show(int $product): JsonResponse
    {
        $productModel = $this->productService->findById($product);

        if (! $productModel) {
            return ApiResponse::notFound('Produk tidak ditemukan.');
        }

        return ApiResponse::success(new ProductResource($productModel), 'Detail produk berhasil diambil.');
    }

    /**
     * PUT /api/admin/products/{product}
     * Update produk.
     */
    public function update(UpdateProductRequest $request, int $product): JsonResponse
    {
        try {
            $updated = $this->productService->update($product, $request->validated());

            return ApiResponse::success(new ProductResource($updated), 'Produk berhasil diperbarui.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::notFound('Produk tidak ditemukan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui produk: ' . $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/admin/products/{product}
     * Hapus produk.
     */
    public function destroy(int $product): JsonResponse
    {
        try {
            $this->productService->destroy($product);

            return ApiResponse::success(null, 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::notFound('Produk tidak ditemukan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus produk: ' . $e->getMessage(), 500);
        }
    }
}
