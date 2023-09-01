<?php

namespace Tests\Unit\Utils;

use Tests\TestCase;
use App\Utils\Response;


/**
 * @covers \App\Utils\Response
 */
class ResponseTest extends TestCase
{

    public function test_success_response_helper_status_converted_to_text(): void
    {
        $response = Response::success([]);
        $this->assertSame("OK", $response->statusText());

    }

    public function test_success_response_helper_status_text_in_response(): void
    {
        $response = Response::success([])->getOriginalContent();
        $this->assertSame("success", $response["status"]);
    }


    public function test_success_response_helper_data(): void
    {
        $response = Response::success(["short_url" => "abcdefg"])->getOriginalContent();
        $this->assertSame("abcdefg", $response["data"]["short_url"]);
    }

    public function test_error_response_helper_status(): void
    {
        $response = Response::error(status: 500);
        $this->assertSame(500, $response->getStatusCode());
    }

    public function test_error_response_helper_status_text_in_response(): void
    {
        $response = Response::error()->getOriginalContent();
        $this->assertSame("error", $response["status"]);
    }

}