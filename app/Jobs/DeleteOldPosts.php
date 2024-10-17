<?php

namespace App\Jobs;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteOldPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $posts = Post::onlyTrashed()
            ->where('deleted_at', '<=', Carbon::now()->subDays(30))
            ->get();

        if ($posts->isEmpty()) {
            Log::info('No posts to delete');
        } else {
            foreach ($posts as $post) {
                Log::info('Deleting Post ID: ' . $post->id);
                $post->forceDelete();
            }
        }
    }
}
