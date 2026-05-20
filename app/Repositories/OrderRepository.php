<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(protected Order $model) {}

    /**
     * Order milik user tertentu (untuk halaman riwayat user).
     */
    public function getAllForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['product.game'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Semua order (untuk admin), opsional filter by status.
     */
    public function getAll(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'product.game'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Detail order by ID (eager load relasi lengkap).
     */
    public function findById(int $id): ?Model
    {
        return $this->model
            ->with(['user', 'product.game'])
            ->find($id);
    }

    /**
     * Cari order by order_number (untuk webhook).
     */
    public function findByOrderNumber(string $orderNumber): ?Model
    {
        return $this->model
            ->with(['user', 'product.game'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Buat order baru.
     */
    public function create(array $data): Model
    {
        $order = $this->model->create($data);

        return $order->load(['user', 'product.game']);
    }

    /**
     * Update status order (pending → processing → success/failed/cancelled).
     */
    public function updateStatus(int $id, string $status): Model
    {
        $order = $this->model->findOrFail($id);
        $order->update(['status' => $status]);

        return $order->fresh(['user', 'product.game']);
    }

    /**
     * Update data payment (token, url, status, method).
     */
    public function updatePayment(int $id, array $data): Model
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);

        return $order->fresh(['user', 'product.game']);
    }
}
