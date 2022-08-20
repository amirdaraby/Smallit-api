<?php

namespace App\Jobs;

use App\Http\Controllers\Api\BaseController;
use App\Models\ShortUrl;
use App\Models\ShortUrlMaxId;
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
//        sleep(5);

        $shortUrlView = ShortUrlMaxId::query()->first();

        if ($shortUrlView->max_id == null)
            $shortUrlView->max_id = 0;


        for ($i = 0; $i < $this->count; $i++) {
//            dump($i);
            $insertData [$i] = [
                'user_id'   => $this->user,
                'url_id'    => $this->url,
                'short_url' => BaseController::generateUrl(++$shortUrlView->max_id)
            ];
            dump($shortUrlView->max_id);
            sleep(0.3);
        }
        dump("created 2d array");

        $insertData = collect($insertData);

//        dump($insertData);
        $chunks = $insertData->chunk(10000);
        dump("data chunked");

        dump($chunks->toArray());
        foreach ($chunks->toArray() as $chunk) {
            sleep(2);
            ShortUrl::insert($chunk);
            dump("inserting");
        }
        dump("mission completed !");

    }
}
