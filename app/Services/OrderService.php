<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(protected OrderRepositoryInterface $orderRepository) {}

    /**
     * Buat order baru untuk user atau guest.
     * Validasi produk aktif, hitung total, dan simpan ke DB.
     */
    public function createOrder(?int $userId, array $data): Model
    {
        // 1. Ambil produk dengan relasi game
        $product = Product::with('game')
            ->where('id', $data['product_id'])
            ->where('status', 'active')
            ->first();

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => ['Produk tidak ditemukan atau tidak aktif.'],
            ]);
        }

        $quantity = $data['quantity'] ?? 1;
        $price    = (float) $product->price;
        $total    = $price * $quantity;

        // 2. Buat order
        return DB::transaction(function () use ($userId, $data, $product, $quantity, $price, $total) {
            return $this->orderRepository->create([
                'user_id'        => $userId,
                'guest_email'    => $data['guest_email'] ?? null,
                'guest_phone'    => $data['guest_phone'] ?? null,
                'product_id'     => $product->id,
                'target_id'      => $data['target_id'],
                'quantity'       => $quantity,
                'price'          => $price,
                'total'          => $total,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'notes'          => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Riwayat order user yang sedang login.
     */
    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getAllForUser($userId, $perPage);
    }

    /**
     * Detail order — hanya bisa diakses oleh pemilik atau admin.
     * Jika guest order, siapapun yang tahu link-nya bisa mengakses.
     */
    public function getOrderDetail(int $orderId, ?int $userId, bool $isAdmin = false): Model
    {
        $order = $this->orderRepository->findById($orderId);

        if (! $order) {
            abort(404, 'Order tidak ditemukan.');
        }

        // Jika order milik user terdaftar
        if ($order->user_id !== null) {
            if (! $isAdmin && $order->user_id !== $userId) {
                abort(403, 'Akses ditolak.');
            }
        }

        return $order;
    }

    /**
     * Batalkan order (hanya jika masih pending).
     */
    public function cancelOrder(int $orderId, ?int $userId): Model
    {
        $order = $this->orderRepository->findById($orderId);

        if (! $order) {
            abort(404, 'Order tidak ditemukan.');
        }

        // Jika order milik user terdaftar, pastikan yang membatalkan adalah pemiliknya
        if ($order->user_id !== null) {
            if ($order->user_id !== $userId) {
                abort(403, 'Akses ditolak.');
            }
        }

        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Order sudah tidak bisa dibatalkan.'],
            ]);
        }

        return $this->orderRepository->updateStatus($orderId, 'cancelled');
    }

    // ── Admin methods ─────────────────────────────────────────────────

    /**
     * Ambil semua order untuk admin, opsional filter by status.
     */
    public function getAllOrders(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->orderRepository->getAll($perPage, $status);
    }

    /**
     * Update status order secara manual (oleh admin).
     */
    public function adminUpdateStatus(int $orderId, string $status): Model
    {
        $order = $this->orderRepository->findById($orderId);

        if (! $order) {
            abort(404, 'Order tidak ditemukan.');
        }

        return $this->orderRepository->updateStatus($orderId, $status);
    }
}
