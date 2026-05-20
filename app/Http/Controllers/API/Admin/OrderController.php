<?php

namespace App\Http\Controllers\API\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * GET /api/admin/orders
     * Semua order, opsional filter by status.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $status  = $request->query('status');
        $orders  = $this->orderService->getAllOrders($perPage, $status);

        return ApiResponse::paginated($orders, OrderResource::class, 'Daftar order berhasil diambil.');
    }

    /**
     * GET /api/admin/orders/{id}
     * Detail order by ID.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getOrderDetail($id, $request->user()->id, isAdmin: true);

        return ApiResponse::success(new OrderResource($order), 'Detail order berhasil diambil.');
    }

    /**
     * PATCH /api/admin/orders/{id}/status
     * Update status order secara manual.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->adminUpdateStatus($id, $request->validated('status'));

        return ApiResponse::success(new OrderResource($order), 'Status order berhasil diperbarui.');
    }
}
