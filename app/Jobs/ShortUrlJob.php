<?php

namespace App\Jobs;

use App\Http\Controllers\Api\BaseController;
use App\Models\ShortUrl;
use App\Models\ShortUrlMaxId;
use App\Models\UserJobs;
use Faker\Provider\Base;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function Symfony\Component\Translation\t;

class ShortUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $url; // ** url ID **
    public $count; // requested short urls (int)
    public $user; // ** user ID **
    public $userJob;  // ** job ID **


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $count, $user, UserJobs $userJob)
    {
        $this->url     = (int)$url;
        $this->count   = (int)$count;
        $this->user    = (int)$user;
        $this->userJob = $userJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
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

            sleep(0.3);
        }


        dump("created 2d array");

        $insertData = collect($insertData);


        $chunks = $insertData->chunk(10000);
        dump("data chunked");


        foreach ($chunks->toArray() as $chunk) {
            sleep(0.7);
            ShortUrl::insert($chunk);
            dump("inserting");
        }
        dump("mission completed !");

        UserJobs::query()->find($this->userJob->id)
            ->update(['status' => 'created']);
    }

}
