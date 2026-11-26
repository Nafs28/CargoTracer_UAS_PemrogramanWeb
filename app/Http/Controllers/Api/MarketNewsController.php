<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MarketNewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketNewsController extends Controller
{
    public function __construct(protected MarketNewsService $marketNewsService)
    {
    }

    /**
     * Fetch global market & maritime trade news.
     */
    public function index(Request $request): JsonResponse
    {
        $topic = (string) $request->get('topic', 'global_market');
        $limit = min(20, max(1, (int) $request->get('limit', 12)));

        $data = $this->marketNewsService->getNews($topic, $limit);

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
