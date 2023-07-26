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

    public $url, $count, $user, $userJob;

    public function __construct($url, $count, $user, UserJobs $userJob)
    {
        $this->url     = (int)$url;
        $this->count   = (int)$count;
        $this->user    = (int)$user;
        $this->userJob = $userJob;
    }

    public function handle()
    {

        $maxId = ShortUrl::query()->max("id");
        if ($maxId == null)
            $maxId = 99999;


        for ($i = 0; $i < $this->count; $i++) {

            $insertData [$i] = [
                'user_id'   => $this->user,
                'url_id'    => $this->url,
                'short_url' => BaseController::generateUrl(++$maxId)
            ];

        }

        $insertData = collect($insertData);


        $chunks = $insertData->chunk(10000);

        foreach ($chunks->toArray() as $chunk) {
            ShortUrl::insert($chunk);
        }

        $this->userJob->update([
           'status' => 'created'
        ]);

    }


}
