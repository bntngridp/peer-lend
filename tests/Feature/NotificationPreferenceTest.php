<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'notifuser@lendflow.com',
        ]);

        $this->user->profile()->create([
            'full_name' => 'Notification User',
            'phone'     => '081299998888',
        ]);
    }

    /**
     * Test a user can update notification preferences.
     */
    public function test_user_can_update_notification_preferences(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('profile.notifications.update'), [
                'security_email'   => '1',
                'security_push'    => '1',
                'financial_email'  => '1',
                'financial_push'   => '0',
                'investment_email' => '1',
                'investment_push'  => '1',
            ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'notifications']));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $settings = $this->user->profile->notification_settings;

        $this->assertTrue($settings['security_email']);
        $this->assertTrue($settings['security_push']);
        $this->assertTrue($settings['financial_email']);
        $this->assertFalse($settings['financial_push']);
        $this->assertTrue($settings['investment_email']);
        $this->assertTrue($settings['investment_push']);
    }
}
