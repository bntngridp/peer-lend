<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationOTPTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Role::firstOrCreate(['name' => 'borrower']);
        \App\Models\Role::firstOrCreate(['name' => 'lender']);
    }

    public function test_registration_form_can_be_rendered(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Nomor telepon akan diverifikasi via kode OTP 6 digit.');
    }

    public function test_registration_initiates_otp_session(): void
    {
        $response = $this->post(route('register'), [
            'full_name'             => 'Test User OTP',
            'email'                 => 'otpuser@example.com',
            'country_code'          => '+62',
            'phone'                 => '81575971998',
            'role'                  => 'borrower',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('register.otp'));
        $response->assertSessionHas('registration_pending_data');
        $response->assertSessionHas('registration_otp');

        $pendingData = session('registration_pending_data');
        $this->assertSame('+6281575971998', $pendingData['phone']);
    }

    public function test_otp_form_redirects_if_no_pending_session(): void
    {
        $response = $this->get(route('register.otp'));
        $response->assertRedirect(route('register'));
        $response->assertSessionHas('error');
    }

    public function test_otp_verification_creates_user_and_profile(): void
    {
        // 1. Initiate registration
        $this->post(route('register'), [
            'full_name'             => 'Pengguna Valid OTP',
            'email'                 => 'validotp@example.com',
            'country_code'          => '+62',
            'phone'                 => '81575971998',
            'role'                  => 'borrower',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $otpCode = session('registration_otp');
        $this->assertNotEmpty($otpCode);

        // 2. Submit valid OTP
        $response = $this->post(route('register.otp.verify'), [
            'otp' => $otpCode,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // 3. Verify user created in DB
        $user = User::where('email', 'validotp@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Pengguna Valid OTP', $user->profile->full_name);
        $this->assertSame('+6281575971998', $user->profile->phone);

        // 4. Verify second registration attempt with same phone fails validation
        $failResponse = $this->post(route('register'), [
            'full_name'             => 'Pengguna B (Imposter)',
            'email'                 => 'imposter@example.com',
            'country_code'          => '+62',
            'phone'                 => '81575971998',
            'role'                  => 'borrower',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $failResponse->assertSessionHasErrors('phone');
    }

    public function test_otp_verification_rejects_incorrect_otp(): void
    {
        $this->post(route('register'), [
            'full_name'             => 'Test Incorrect OTP',
            'email'                 => 'wrongotp@example.com',
            'country_code'          => '+62',
            'phone'                 => '81299998888',
            'role'                  => 'lender',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response = $this->post(route('register.otp.verify'), [
            'otp' => '000000',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertDatabaseMissing('users', ['email' => 'wrongotp@example.com']);
    }
}
