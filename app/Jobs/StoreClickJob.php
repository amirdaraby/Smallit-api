<?php

namespace App\Jobs;

use App\Models\Click;
use App\Models\ShortUrl;
use App\Traits\UserAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UserAgent;

    private ?string $uid;
    private ?string $userAgent;
    private ShortUrl $shortUrl;

    public function __construct(?string $userAgent, ?string $uid, ShortUrl $shortUrl)
    {
        $this->userAgent = $userAgent;
        $this->uid = $uid;
        $this->shortUrl = $shortUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Click::create(
            [
                "uid" => $this->uid,
                "short_url_id" => $this->shortUrl->id,
                "browser" => $this->getBrowser($this->userAgent),
                "platform" => $this->getOs($this->userAgent),
                "user_agent" => $this->userAgent,
            ]
        );
    }
}
