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
            'notification_settings' => [
                'security_email'   => true,
                'security_push'    => true,
                'financial_email'  => true,
                'financial_push'   => true,
                'investment_email' => true,
                'investment_push'  => false,
            ]
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

    /**
     * Test a user can uncheck all notification preferences.
     */
    public function test_user_can_uncheck_all_notification_preferences(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('profile.notifications.update'), []);

        $response->assertRedirect(route('profile.edit', ['tab' => 'notifications']));

        $this->user->refresh();
        $settings = $this->user->profile->notification_settings;

        $this->assertFalse($settings['security_email']);
        $this->assertFalse($settings['security_push']);
        $this->assertFalse($settings['financial_email']);
        $this->assertFalse($settings['financial_push']);
        $this->assertFalse($settings['investment_email']);
        $this->assertFalse($settings['investment_push']);
    }

    /**
     * Test notification preferences render correctly on profile page.
     */
    public function test_notification_preferences_render_correctly_on_profile_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit', ['tab' => 'notifications']));

        $response->assertOk();
        $response->assertSee(__('Security Alerts'));
        $response->assertSee(__('Financial Activity'));
        $response->assertSee(__('Unrecognized Logins & Password Changes'));
        $response->assertSee(__('Loan Approvals & Updates'));
        $response->assertSee(__('Investment Milestones & Returns'));
    }

    /**
     * Test Arabic translations for Notification Preferences.
     */
    public function test_notification_preferences_arabic_translations(): void
    {
        app()->setLocale('ar');

        $this->assertSame('تفضيلات الإشعارات', __('Notification Preferences'));
        $this->assertSame('تنبيهات الأمان', __('Security Alerts'));
        $this->assertSame('النشاط المالي', __('Financial Activity'));
        $this->assertSame('تسجيلات الدخول غير المعترف بها وتغييرات كلمة المرور', __('Unrecognized Logins & Password Changes'));
        $this->assertSame('الموافقة على القروض والتحديثات', __('Loan Approvals & Updates'));
        $this->assertSame('محطات الاستثمار والعوائد', __('Investment Milestones & Returns'));
    }
}
