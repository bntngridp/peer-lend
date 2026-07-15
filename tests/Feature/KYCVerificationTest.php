<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KYCVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles & Currencies
        $this->artisan('db:seed');
    }

    /**
     * Test the complete KYC flow from submission to approval.
     */
    public function test_user_can_submit_kyc_and_admin_can_approve(): void
    {
        // Use local disk fake for private document storage
        Storage::fake('local');
        Storage::fake('public');

        // ─── 1. Create a Borrower User ────────────────────────────────
        $borrower = User::factory()->create();
        $borrowerRole = Role::where('name', 'borrower')->first();
        $borrower->roles()->attach($borrowerRole->id);

        // ─── 2. Try accessing KYC without profile (should redirect) ────
        $response = $this->actingAs($borrower)->get(route('kyc.index'));
        $response->assertRedirect(route('profile.edit'));

        // ─── 3. Complete Profile ──────────────────────────────────────
        $profileData = [
            'full_name'      => 'Bintang Borrower',
            'phone'          => '081234567899',
            'address'        => 'Jalan Portofolio No. 10',
            'city'           => 'Jakarta',
            'province'       => 'DKI Jakarta',
            'occupation'     => 'Merchant Developer',
            'monthly_income' => 15000000,
        ];

        $response = $this->actingAs($borrower)->put(route('profile.update'), $profileData);
        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('profiles', [
            'user_id'   => $borrower->id,
            'full_name' => 'Bintang Borrower',
            'phone'     => '081234567899',
        ]);

        $borrower->refresh();

        // ─── 4. Access KYC index (should load form now) ──────────────
        $response = $this->actingAs($borrower)->get(route('kyc.index'));
        $response->assertStatus(200);

        // ─── 5. Submit KYC documents ──────────────────────────────────
        $ktp = UploadedFile::fake()->image('ktp_card.jpg');
        $selfie = UploadedFile::fake()->image('selfie_holding_ktp.jpg');

        $response = $this->actingAs($borrower)->post(route('kyc.submit'), [
            'ktp'    => $ktp,
            'selfie' => $selfie,
        ]);

        $response->assertRedirect(route('kyc.index'));
        
        // Assert KYC was created in database with pending status
        $this->assertDatabaseHas('kycs', [
            'user_id' => $borrower->id,
            'status'  => 'pending',
        ]);

        $kyc = KYC::where('user_id', $borrower->id)->first();
        $this->assertCount(2, $kyc->documents);

        // Verify files were stored securely
        foreach ($kyc->documents as $doc) {
            Storage::disk('local')->assertExists($doc->file_path);
        }

        // ─── 6. Try accessing admin queue as borrower (should fail) ───
        $response = $this->actingAs($borrower)->get(route('admin.kyc.index'));
        $response->assertStatus(403);

        // ─── 7. Create Admin and Access queue ─────────────────────────
        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->id);

        $response = $this->actingAs($admin)->get(route('admin.kyc.index'));
        $response->assertStatus(200);
        $response->assertSee('Bintang Borrower');

        // ─── 8. Admin reviews documents ───────────────────────────────
        $doc = $kyc->documents->first();
        $response = $this->actingAs($admin)->get(route('admin.kyc.document', $doc->id));
        $response->assertStatus(200); // Admin can stream private files

        // ─── 9. Admin approves KYC ────────────────────────────────────
        $response = $this->actingAs($admin)->post(route('admin.kyc.approve', $kyc->id));
        $response->assertRedirect(route('admin.kyc.index'));

        // Assert KYC approved in DB
        $this->assertDatabaseHas('kycs', [
            'id'          => $kyc->id,
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
        ]);
    }
}
