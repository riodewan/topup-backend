<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * GET /api/orders
     * Riwayat order user yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $orders  = $this->orderService->getUserOrders($request->user()->id, $perPage);

        return ApiResponse::paginated($orders, OrderResource::class, 'Riwayat order berhasil diambil.');
    }

    /**
     * POST /api/orders
     * Buat order baru (bisa login atau guest).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->user('sanctum')?->id,
            $request->validated()
        );

        return ApiResponse::success(new OrderResource($order), 'Order berhasil dibuat.', 201);
    }

    /**
     * GET /api/orders/{id}
     * Detail order (milik user terdaftar atau guest order).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getOrderDetail($id, $request->user('sanctum')?->id);

        return ApiResponse::success(new OrderResource($order), 'Detail order berhasil diambil.');
    }

    /**
     * DELETE /api/orders/{id}
     * Batalkan order (hanya jika masih pending).
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->cancelOrder($id, $request->user('sanctum')?->id);

        return ApiResponse::success(new OrderResource($order), 'Order berhasil dibatalkan.');
    }
}
