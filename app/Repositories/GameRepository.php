<?php

namespace App\Repositories;

use App\Models\Game;
use App\Repositories\Contracts\GameRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class GameRepository implements GameRepositoryInterface
{
    public function __construct(protected Game $model) {}

    /**
     * Ambil semua game aktif (untuk publik).
     */
    public function getAllActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->active()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Ambil semua game (untuk admin, termasuk inactive).
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Cari game by slug dengan relasi active products.
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->model
            ->with(['activeProducts'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Cari game by ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Buat game baru.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update game by ID.
     */
    public function update(int $id, array $data): Model
    {
        $game = $this->model->findOrFail($id);
        $game->update($data);

        return $game->fresh();
    }

    /**
     * Hapus game by ID.
     */
    public function delete(int $id): bool
    {
        $game = $this->model->findOrFail($id);

        return $game->delete();
    }
}
