<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    /**
     * Ambil produk aktif untuk publik, opsional filter by game_id.
     */
    public function getAllActive(int $perPage = 15, ?int $gameId = null): LengthAwarePaginator
    {
        return $this->productRepository->getAllActive($perPage, $gameId);
    }

    /**
     * Ambil semua produk untuk admin.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getAll($perPage);
    }

    /**
     * Detail produk by slug.
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->productRepository->findBySlug($slug);
    }

    /**
     * Detail produk by ID (admin).
     */
    public function findById(int $id): ?Model
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Buat produk baru.
     */
    public function store(array $data): Model
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return $this->productRepository->create($data);
    }

    /**
     * Update produk by ID.
     */
    public function update(int $id, array $data): Model
    {
        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->productRepository->update($id, $data);
    }

    /**
     * Hapus produk by ID.
     */
    public function destroy(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
