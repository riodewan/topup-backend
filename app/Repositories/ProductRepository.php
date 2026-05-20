<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $model) {}

    /**
     * Ambil semua produk aktif (untuk publik), opsional filter by game.
     */
    public function getAllActive(int $perPage = 15, ?int $gameId = null): LengthAwarePaginator
    {
        return $this->model
            ->with('game')
            ->active()
            ->when($gameId, fn ($q) => $q->where('game_id', $gameId))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Ambil semua produk (untuk admin).
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('game')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Cari produk by slug.
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->model
            ->with('game')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Cari produk by ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->model->with('game')->find($id);
    }

    /**
     * Buat produk baru.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update produk by ID.
     */
    public function update(int $id, array $data): Model
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);

        return $product->fresh(['game']);
    }

    /**
     * Hapus produk by ID.
     */
    public function delete(int $id): bool
    {
        $product = $this->model->findOrFail($id);

        return $product->delete();
    }
}
