<?php

namespace Tests\Feature\http\controllers\api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorizedApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        

    }

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
