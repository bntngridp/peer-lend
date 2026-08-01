<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_set(): void
    {
        config(['app.locale' => 'id']);
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertEquals('id', app()->getLocale());
    }

    public function test_can_switch_language_to_english(): void
    {
        $response = $this->get(route('lang.switch', 'en'));
        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));

        $this->get('/')->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_can_switch_language_to_spanish(): void
    {
        $response = $this->get(route('lang.switch', 'es'));
        $response->assertRedirect();
        $this->assertEquals('es', session('locale'));

        $this->get('/')->assertStatus(200);
        $this->assertEquals('es', app()->getLocale());
    }

    public function test_can_switch_language_to_arabic(): void
    {
        $response = $this->get(route('lang.switch', 'ar'));
        $response->assertRedirect();
        $this->assertEquals('ar', session('locale'));

        $this->get('/')->assertStatus(200);
        $this->assertEquals('ar', app()->getLocale());
    }

    public function test_invalid_language_code_is_ignored(): void
    {
        $response = $this->get(route('lang.switch', 'invalid_lang'));
        $response->assertRedirect();
        $this->assertNull(session('locale'));
    }
}
