<?php

namespace Tests\Feature;

use App\Models\KYC;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementAndStatusToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'borrower'], ['guard_name' => 'web']);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::where('name', 'admin')->first());
        return $admin;
    }

    private function createBorrower(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', 'borrower')->first());
        Profile::create(['user_id' => $user->id, 'full_name' => 'Test Borrower', 'phone' => '0812345678']);
        KYC::create(['user_id' => $user->id, 'nik' => '320' . rand(1000000000000, 9999999999999), 'status' => 'approved']);
        return $user;
    }

    public function test_admin_can_access_user_profile_page(): void
    {
        $admin = $this->createAdmin();
        $targetUser = $this->createBorrower();

        $response = $this->actingAs($admin)->get(route('admin.users.show', $targetUser));

        $response->assertStatus(200);
        $response->assertSee('Test Borrower');
        $response->assertSee($targetUser->email);
        $response->assertSee('Account Active');
    }

    public function test_admin_can_suspend_and_reactivate_user(): void
    {
        $admin = $this->createAdmin();
        $targetUser = $this->createBorrower();

        // Suspend user
        $response = $this->actingAs($admin)->post(route('admin.users.toggleStatus', $targetUser));
        $response->assertRedirect();
        $this->assertFalse($targetUser->fresh()->is_active);

        // Reactivate user
        $response2 = $this->actingAs($admin)->post(route('admin.users.toggleStatus', $targetUser));
        $response2->assertRedirect();
        $this->assertTrue($targetUser->fresh()->is_active);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.users.toggleStatus', $admin));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_non_admin_cannot_toggle_user_status(): void
    {
        $borrower = $this->createBorrower();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($borrower)->post(route('admin.users.toggleStatus', $targetUser));
        $response->assertStatus(403);
    }
}
