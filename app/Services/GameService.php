<?php

namespace App\Services;

use App\Repositories\Contracts\GameRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GameService
{
    public function __construct(protected GameRepositoryInterface $gameRepository) {}

    /**
     * Ambil daftar game aktif untuk publik.
     */
    public function getAllActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->gameRepository->getAllActive($perPage);
    }

    /**
     * Ambil semua game untuk admin panel.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->gameRepository->getAll($perPage);
    }

    /**
     * Detail game by slug (publik).
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->gameRepository->findBySlug($slug);
    }

    /**
     * Cari game by ID (admin).
     */
    public function findById(int $id): ?Model
    {
        return $this->gameRepository->findById($id);
    }

    /**
     * Buat game baru.
     */
    public function store(array $data): Model
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return $this->gameRepository->create($data);
    }

    /**
     * Update game by ID.
     */
    public function update(int $id, array $data): Model
    {
        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->gameRepository->update($id, $data);
    }

    /**
     * Hapus game by ID.
     */
    public function destroy(int $id): bool
    {
        return $this->gameRepository->delete($id);
    }
}
