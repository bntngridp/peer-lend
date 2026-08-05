<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemPreferenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'sysuser@lendflow.com',
        ]);

        $this->user->profile()->create([
            'full_name' => 'System User',
            'phone'     => '081377776666',
            'system_preferences' => [
                'color_theme'               => 'light',
                'data_density'              => 'comfortable',
                'public_profile'            => true,
                'data_sharing'              => false,
                'third_party_integrations'  => true,
            ]
        ]);
    }

    /**
     * Test a user can update system preferences.
     */
    public function test_user_can_update_system_preferences(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('profile.system.update'), [
                'color_theme'               => 'dark',
                'data_density'              => 'compact',
                'public_profile'            => '1',
                'data_sharing'              => '1',
                'third_party_integrations' => '0',
            ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'system']));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $settings = $this->user->profile->system_preferences;

        $this->assertEquals('dark', $settings['color_theme']);
        $this->assertEquals('compact', $settings['data_density']);
        $this->assertTrue($settings['public_profile']);
        $this->assertTrue($settings['data_sharing']);
        $this->assertFalse($settings['third_party_integrations']);
    }

    /**
     * Test a user can uncheck all privacy controls.
     */
    public function test_user_can_uncheck_privacy_controls(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('profile.system.update'), [
                'color_theme'  => 'light',
                'data_density' => 'comfortable',
            ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'system']));

        $this->user->refresh();
        $settings = $this->user->profile->system_preferences;

        $this->assertFalse($settings['public_profile']);
        $this->assertFalse($settings['data_sharing']);
        $this->assertFalse($settings['third_party_integrations']);
    }

    /**
     * Test validation fails for invalid color theme or data density.
     */
    public function test_system_preferences_validation_fails_for_invalid_input(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('profile.system.update'), [
                'color_theme'  => 'invalid_theme',
                'data_density' => 'invalid_density',
            ]);

        $response->assertSessionHasErrors(['color_theme', 'data_density']);
    }

    /**
     * Test AJAX theme toggle endpoint.
     */
    public function test_user_can_toggle_theme_via_ajax(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('theme.toggle'), [
                'theme' => 'dark',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'theme'  => 'dark',
        ]);

        $this->user->refresh();
        $this->assertEquals('dark', $this->user->profile->system_preferences['color_theme']);
    }

    /**
     * Test profile page renders system preferences and privacy controls tab properly.
     */
    public function test_privacy_controls_render_correctly_on_profile_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit', ['tab' => 'system']));

        $response->assertOk();
        $response->assertSee(__('Privacy & Data Controls'));
        $response->assertSee(__('Public Profile Visibility'));
        $response->assertSee(__('Data Sharing for Analytics'));
        $response->assertSee(__('Third-Party Integrations'));
    }

    /**
     * Test Arabic translations for Privacy & Data Controls.
     */
    public function test_privacy_and_data_controls_arabic_translations(): void
    {
        app()->setLocale('ar');

        $this->assertSame('ضوابط الخصوصية والبيانات', __('Privacy & Data Controls'));
        $this->assertSame('رؤية الملف الشخصي العام', __('Public Profile Visibility'));
        $this->assertSame('مشاركة البيانات للتحليلات', __('Data Sharing for Analytics'));
        $this->assertSame('تكاملات الطرف الثالث', __('Third-Party Integrations'));
    }
}
