<?php

namespace App\Jobs;

use App\Http\Controllers\Api\AgentController;
use App\Models\Click;
use App\Traits\UserAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UserAgent;

    private $userAgent, $uid, $shortUrlId;
    public function __construct($userAgent, $uid, $shortUrlId)
    {
        $this->userAgent = $userAgent;
        $this->uid = $uid;
        $this->shortUrlId = $shortUrlId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Click::create(
            [
                "uid" => $this->uid,
                "shorturl_id" => $this->shortUrlId,
                "browser" => $this->getBrowser($this->userAgent),
                "platform" => $this->getOs($this->userAgent),
                "user_agent" => $this->userAgent,
            ]
        );

    }
}
