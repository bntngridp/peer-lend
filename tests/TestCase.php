<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Disable CSRF token verification for all feature tests.
     * Test HTTP clients don't send CSRF tokens; bypassing this middleware
     * is the standard Laravel testing approach.
     *
     * Laravel 11+ uses PreventRequestForgery as the actual CSRF middleware.
     * We also include the legacy class names for compatibility.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Disable CSRF for the current test request pipeline
        $this->withoutMiddleware([
            PreventRequestForgery::class,
            VerifyCsrfToken::class,
            ValidateCsrfToken::class,
        ]);
    }
}

