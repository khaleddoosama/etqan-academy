<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Load .env.testing file
        if (file_exists(__DIR__ . '/../.env.testing')) {
            $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../', '.env.testing');
            $dotenv->load();
        }

        // Clear any cached config to ensure testing config is used
        if (app()->environment('testing')) {
            Artisan::call('config:clear');
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
