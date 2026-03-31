<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MercadoPagoOAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function mp_connect_redirects_to_mercadopago(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('mp.connect'))
            ->assertRedirect();
    }

    #[Test]
    public function mp_callback_saves_tokens_to_user(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'api.mercadopago.com/oauth/token' => Http::response([
                'access_token'  => 'TEST-new-access-token',
                'refresh_token' => 'TEST-refresh-token',
                'user_id'       => 987654321,
                'expires_in'    => 15552000,
            ], 200),
        ]);

        $this->actingAs($user)
            ->get(route('mp.callback', [
                'code'  => 'valid_auth_code',
                'state' => csrf_token(),
            ]))
            ->assertRedirect(route('mp.connected'));

        $updated = $user->fresh();
        $this->assertEquals('TEST-new-access-token', $updated->mp_access_token);
        $this->assertEquals('987654321', $updated->mp_user_id);
    }

    #[Test]
    public function mp_disconnect_clears_tokens(): void
    {
        $user = User::factory()->create([
            'mp_access_token' => 'TEST-token',
            'mp_user_id'      => '123',
        ]);

        $this->actingAs($user)
            ->post(route('mp.disconnect'))
            ->assertRedirect();

        $updated = $user->fresh();
        $this->assertNull($updated->mp_access_token);
        $this->assertNull($updated->mp_user_id);
    }
}