<?php

namespace App\Jobs;


use App\Models\Batch;
use App\Models\ShortUrl;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShortUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $url, $amount, $user, $batch;

    public function __construct($url, $amount, $user, Batch $batch)
    {
        $this->url = (int)$url;
        $this->amount = (int)$amount;
        $this->user = (int)$user;
        $this->batch = $batch;
    }

    public function handle()
    {

        $maxId = ShortUrl::query()->max("id") ?? 99999;

        DB::transaction(function () use ($maxId) {

            for ($i = 0; $i < $this->amount; $i++) {
                $insertData [$i] = [
                    'user_id' => $this->user,
                    'url_id' => $this->url,
                    'batch_id' => $this->batch->id,
                    'short_url' => generateShortUrl(++$maxId)
                ];
            }
            $chunks = array_chunk($insertData, 10000);

            foreach ($chunks as $chunk) {
                ShortUrl::query()->insert($chunk);
            }

            $this->batch->update([
                'status' => 'success'
            ]);

            Cache::tags("user_{$this->user}_urls")->flush();

        });

    }

    public function failed($exception = null)
    {
        $this->batch->update([
            'status' => 'failed'
        ]);
    }

}
