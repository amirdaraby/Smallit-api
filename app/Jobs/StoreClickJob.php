<?php

namespace App\Jobs;

use App\Http\Controllers\Api\AgentController;
use App\Models\Click;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
                "browser" => AgentController::getBrowser($this->userAgent),
                "platform" => AgentController::getOs($this->userAgent),
                "useragent" => $this->userAgent,
            ]
        );

    }
}