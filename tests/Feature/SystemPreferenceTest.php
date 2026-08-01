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
}
