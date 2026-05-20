<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(protected GameService $gameService) {}

    /**
     * GET /api/games
     * Ambil daftar semua game aktif (public).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $games   = $this->gameService->getAllActive($perPage);

        return ApiResponse::paginated($games, 'Daftar game berhasil diambil.');
    }

    /**
     * GET /api/games/{slug}
     * Detail game by slug beserta produk aktif (public).
     */
    public function show(string $slug): JsonResponse
    {
        $game = $this->gameService->findBySlug($slug);

        if (! $game) {
            return ApiResponse::notFound('Game tidak ditemukan.');
        }

        return ApiResponse::success(new GameResource($game), 'Detail game berhasil diambil.');
    }
}
