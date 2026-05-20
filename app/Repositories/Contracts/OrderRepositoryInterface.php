<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryInterface
{
    public function getAllForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAll(int $perPage = 15, ?string $status = null): LengthAwarePaginator;

    public function findById(int $id): ?Model;

    public function findByOrderNumber(string $orderNumber): ?Model;

    public function create(array $data): Model;

    public function updateStatus(int $id, string $status): Model;

    public function updatePayment(int $id, array $data): Model;
}
