<?php

namespace Tests\Feature;

use App\Models\FeeConfiguration;
use App\Models\InterestRate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFinancialAndRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        (new \Database\Seeders\DatabaseSeeder())->run();

        $this->adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();
        $this->regularUser = User::whereHas('roles', fn($q) => $q->where('name', 'borrower'))->first();
    }

    // ─── Financial Configuration Tests ─────────────────────────────────────────

    public function test_admin_can_access_financials_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.financials.index'));
        $response->assertStatus(200);
        $response->assertSee('Financial Configuration');
    }

    public function test_non_admin_cannot_access_financials_page(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.financials.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_interest_rates(): void
    {
        $payload = [
            'grade_a_min' => 4.00,
            'grade_a_max' => 7.00,
            'grade_b_min' => 8.00,
            'grade_b_max' => 12.00,
            'grade_c_min' => 13.00,
            'grade_c_max' => 17.00,
            'grade_d_min' => 18.00,
            'grade_d_max' => 25.00,
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.financials.updateRates'), $payload);
        $response->assertRedirect(route('admin.financials.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('interest_rates', [
            'risk_grade' => 'A',
            'min_rate'   => 4.00,
            'max_rate'   => 7.00,
        ]);
        $this->assertDatabaseHas('interest_rates', [
            'risk_grade' => 'D',
            'min_rate'   => 18.00,
            'max_rate'   => 25.00,
        ]);
    }

    public function test_admin_can_update_fee_schedule(): void
    {
        $payload = [
            'origination_fee' => 2.50,
            'service_fee'     => 10000,
            'penalty_rate'    => 0.25,
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.financials.updateFees'), $payload);
        $response->assertRedirect(route('admin.financials.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fee_configurations', [
            'type'  => 'origination_fee',
            'value' => 2.50,
        ]);
        $this->assertDatabaseHas('fee_configurations', [
            'type'  => 'service_fee',
            'value' => 10000,
        ]);
        $this->assertDatabaseHas('fee_configurations', [
            'type'  => 'penalty_rate',
            'value' => 0.25,
        ]);
    }

    public function test_admin_can_update_currency_settings(): void
    {
        $payload = [
            'currency_idr'  => '1',
            'currency_usdt' => '1',
            // eth and btc unchecked
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.financials.updateCurrencies'), $payload);
        $response->assertRedirect(route('admin.financials.index'));

        $this->assertEquals('1', Setting::getVal('currency_idr_enabled'));
        $this->assertEquals('1', Setting::getVal('currency_usdt_enabled'));
        $this->assertEquals('0', Setting::getVal('currency_eth_enabled'));
        $this->assertEquals('0', Setting::getVal('currency_btc_enabled'));
    }

    // ─── Role Management Tests ────────────────────────────────────────────────

    public function test_admin_can_access_roles_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Role Management');
    }

    public function test_admin_can_create_new_role(): void
    {
        $perm = Permission::first();

        $payload = [
            'name'        => 'auditor_officer',
            'description' => 'Platform financial auditor',
            'permissions' => [$perm->id],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.roles.store'), $payload);
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name'        => 'auditor_officer',
            'description' => 'Platform financial auditor',
        ]);

        $createdRole = Role::where('name', 'auditor_officer')->first();
        $this->assertTrue($createdRole->permissions->contains($perm->id));
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $role = Role::where('name', 'customer_service')->first();
        $perms = Permission::take(3)->pluck('id')->toArray();

        $response = $this->actingAs($this->adminUser)->post(route('admin.roles.updatePermissions', $role), [
            'permissions' => $perms,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(count($perms), $role->fresh()->permissions()->count());
    }

    public function test_admin_cannot_delete_system_roles(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $response = $this->actingAs($this->adminUser)->delete(route('admin.roles.destroy', $adminRole));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_admin_can_delete_custom_role(): void
    {
        $customRole = Role::create([
            'name'        => 'temp_testing_role',
            'description' => 'Temporary role',
            'guard_name'  => 'web',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.roles.destroy', $customRole));
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['name' => 'temp_testing_role']);
    }
}
