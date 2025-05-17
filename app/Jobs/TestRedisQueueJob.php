<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestRedisQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            Log::info('✅ TestRedisQueueJob executed successfully from Redis!');
        } catch (\Exception $e) {
            Log::error('❌ TestRedisQueueJob failed: ' . $e->getMessage());
            throw $e;
        }
    }

}
