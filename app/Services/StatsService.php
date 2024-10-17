<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Post;

class StatsService
{
    /**
     * Get statistics and cache them.
     */
    public function getStats()
    {
        return Cache::rememberForever('cachedStats', function () {
            return [
                'total_users' => User::count(),
                'total_posts' => Post::count(),
                'users_with_no_posts' => User::whereDoesntHave('posts')->count(),
            ];
        });
    }

    /**
     * Invalidate the cached statistics.
     */
    public function clearCachedStats()
    {
        Cache::forget('cachedStats');
    }

    /**
     * Set global settings for stats (Optional: if needed in config).
     */
    public function setGlobalStats()
    {
        $stats = $this->getStats();
        config()->set('cachedStats', $stats);
    }
}
