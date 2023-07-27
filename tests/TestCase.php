<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public function setUp(): void
    {
        parent::setUp();
        $path = database_path().'/database.sqlite';
        if (! file_exists($path) )
            File::put($path,'');


    }
    public function tearDown(): void
    {
        File::delete(database_path().'/database.sqlite');
        parent::tearDown();
    }
}
