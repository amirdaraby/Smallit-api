<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_generate_short_url_helper_returns_valid_short_url(): void
    {
        $this->assertSame("2s", generateShortUrl(100));
    }

    public function test_success_response_helper_status_code(): void
    {
        $response = responseSuccess([]);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_success_response_helper_status_converted_to_text(): void
    {
        $response = responseSuccess([]);
        $this->assertSame("OK", $response->statusText());

    }

    public function test_success_response_helper_status_text_in_response(): void
    {
        $response = responseSuccess([])->getOriginalContent();
        $this->assertSame("success", $response["status"]);
    }


    public function test_success_response_helper_data(): void
    {
        $response = responseSuccess(["short_url" => "abcdefg"])->getOriginalContent();
        $this->assertSame("abcdefg", $response["data"]["short_url"]);
    }

    public function test_error_response_helper_status(): void
    {
        $response = responseError(status: 500);
        $this->assertSame(500, $response->getStatusCode());
    }

    public function test_error_response_helper_status_text_in_response(): void
    {
        $response = responseError()->getOriginalContent();
        $this->assertSame("error", $response["status"]);
    }

}