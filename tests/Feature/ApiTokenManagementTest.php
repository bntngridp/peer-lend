<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'testuser@lendflow.com',
        ]);

        $this->user->profile()->create([
            'full_name' => 'Test User',
            'phone'     => '081234567890',
        ]);
    }

    /**
     * Test a user can create a new API Token.
     */
    public function test_user_can_create_api_token(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('profile.tokens.store'), [
                'name'        => 'Production Node 1',
                'permissions' => 'write',
            ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'security']));
        $response->assertSessionHas('success');
        $response->assertSessionHas('generated_api_token');

        $this->assertDatabaseHas('personal_access_tokens', [
            'user_id'     => $this->user->id,
            'name'        => 'Production Node 1',
            'permissions' => 'Read / Write',
        ]);
    }

    /**
     * Test a user can revoke an API Token.
     */
    public function test_user_can_revoke_api_token(): void
    {
        $token = $this->user->apiTokens()->create([
            'name'        => 'Trading Bot Key',
            'token'       => hash('sha256', 'lf_live_test123'),
            'permissions' => 'Read Only',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('profile.tokens.destroy', $token));

        $response->assertRedirect(route('profile.edit', ['tab' => 'security']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }
}
