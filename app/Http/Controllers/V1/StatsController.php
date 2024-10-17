<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Services\StatsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function stats()
    {
        $this->clearStatsCache();
        $stats = $this->statsService->getStats();
        return response()->json($stats);
    }

    // Call this when you need to clear the cache
    public function clearStatsCache()
    {
        $this->statsService->clearCachedStats();
    }
}
