<?php

namespace App\Http\Controllers\API\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\StoreGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(protected GameService $gameService) {}

    /**
     * GET /api/admin/games
     * Daftar semua game (termasuk inactive).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $games   = $this->gameService->getAll($perPage);

        return ApiResponse::paginated($games, 'Daftar game berhasil diambil.');
    }

    /**
     * POST /api/admin/games
     * Buat game baru.
     */
    public function store(StoreGameRequest $request): JsonResponse
    {
        try {
            $game = $this->gameService->store($request->validated());

            return ApiResponse::created(new GameResource($game), 'Game berhasil ditambahkan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan game: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/admin/games/{game}
     * Detail game by ID.
     */
    public function show(int $game): JsonResponse
    {
        $gameModel = $this->gameService->findById($game);

        if (! $gameModel) {
            return ApiResponse::notFound('Game tidak ditemukan.');
        }

        return ApiResponse::success(new GameResource($gameModel), 'Detail game berhasil diambil.');
    }

    /**
     * PUT /api/admin/games/{game}
     * Update game by ID.
     */
    public function update(UpdateGameRequest $request, int $game): JsonResponse
    {
        try {
            $updated = $this->gameService->update($game, $request->validated());

            return ApiResponse::success(new GameResource($updated), 'Game berhasil diperbarui.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::notFound('Game tidak ditemukan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui game: ' . $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/admin/games/{game}
     * Hapus game by ID.
     */
    public function destroy(int $game): JsonResponse
    {
        try {
            $this->gameService->destroy($game);

            return ApiResponse::success(null, 'Game berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::notFound('Game tidak ditemukan.');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus game: ' . $e->getMessage(), 500);
        }
    }
}
