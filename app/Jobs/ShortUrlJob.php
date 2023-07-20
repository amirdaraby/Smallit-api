<?php

namespace App\Jobs;

use App\Http\Controllers\Api\BaseController;
use App\Models\ShortUrl;
use App\Models\ShortUrlMaxId;
use App\Models\UserJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ShortUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $url; // ** url ID **
    public $count; // requested short urls (int)
    public $user; // ** user ID **
    public $userJob;  // ** job ID **

    public function __construct($url, $count, $user, UserJobs $userJob)
    {
        $this->url     = (int)$url;
        $this->count   = (int)$count;
        $this->user    = (int)$user;
        $this->userJob = $userJob;
    }

    public function handle()
    {

        $shortUrlView = ShortUrlMaxId::first();
        if ($shortUrlView->max_id == null)
            $shortUrlView->max_id = 99999;


        for ($i = 0; $i < $this->count; $i++) {

            $insertData [$i] = [
                'user_id'   => $this->user,
                'url_id'    => $this->url,
                'short_url' => BaseController::generateUrl(++$shortUrlView->max_id)
            ];

        }

        $insertData = collect($insertData);


        $chunks = $insertData->chunk(10000);

        foreach ($chunks->toArray() as $chunk) {
            ShortUrl::insert($chunk);
        }

        UserJobs::query()->find($this->userJob->id)
            ->update(['status' => 'created']);
    }


}
