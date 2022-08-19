<?php

namespace App\Jobs;

use App\Http\Controllers\Api\BaseController;
use App\Models\ShortUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShortUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url; // ** url ID **
    public $count;
    public $user; // ** user ID **

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $count, $user)
    {
        $this->url   = (int)$url;
        $this->count = (int)$count;
        $this->user  = (int)$user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $insertData = [];
        for ($i = 0; $i < $this->count; $i++) {
            $insertData [$i] = [
                "short_url" => Str::random(5),
                "url_id" => $this->url,
                "user_id" => $this->user
            ];
        }

        $insertData = collect($insertData);

        $chunks = $insertData->chunk(10000);

        foreach ($chunks->toArray() as $chunk) {
            ShortUrl::insert($chunk); // todo only this remaining.
//            sleep(4);
        }

    }
}
