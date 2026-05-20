<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{
    public function getAllActive(int $perPage = 15, ?int $gameId = null): LengthAwarePaginator;

    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Model;

    public function findById(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;
}
