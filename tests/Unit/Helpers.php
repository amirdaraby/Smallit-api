<?php

namespace Tests\Unit;

use App\Utils\Response;
use Tests\TestCase;

/**
 * @codeCoverageIgnore
 */
class Helpers extends TestCase
{

    public function test_success_response_helper_status_code(): void
    {
        $response = Response::success([]);
        $this->assertSame(200, $response->getStatusCode());
    }

}
