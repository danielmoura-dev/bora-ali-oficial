<?php

namespace Tests\Feature\Onboarding;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OnboardingStep3Test extends TestCase
{
    use RefreshDatabase;

    private function makeStep3User(array $extra = []): User
    {
        return User::create(array_merge([
            'name'              => 'João Silva',
            'email'             => 'joao@exemplo.com',
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'profile_type'      => 'cpf',
            'document_number'   => '52998224725',
            'birth_date'        => '1990-05-15',
            'onboarding_step'   => 3,
        ], $extra));
    }

    #[Test]
    public function step3_page_is_accessible_for_step3_user(): void
    {
        $user = $this->makeStep3User();

        $this->actingAs($user)
            ->get(route('onboarding.step3'))
            ->assertStatus(200);
    }

    #[Test]
    public function step2_user_cannot_access_step3(): void
    {
        $user = User::create([
            'name'              => 'Bloqueado',
            'email'             => 'bloqueado@exemplo.com',
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'onboarding_step'   => 2,
        ]);

        $this->actingAs($user)
            ->get(route('onboarding.step3'))
            ->assertRedirect(route('onboarding.step2'));
    }

    #[Test]
    public function user_can_request_whatsapp_code(): void
    {
        $user = $this->makeStep3User();

        $response = $this->actingAs($user)
            ->post(route('onboarding.step3.send'), [
                'phone' => '(85) 99999-1234',
            ]);

        $response->assertRedirect(route('onboarding.step3.verify'));

        // Código deve estar no cache
        $phone = '5585999991234';
        $this->assertTrue(Cache::has("whatsapp_code_{$phone}"));
    }

    #[Test]
    public function phone_send_requires_valid_brazilian_number(): void
    {
        $user = $this->makeStep3User();

        $this->actingAs($user)
            ->post(route('onboarding.step3.send'), [
                'phone' => '1234', // número inválido
            ])
            ->assertSessionHasErrors('phone');
    }

    #[Test]
    public function user_can_verify_whatsapp_code(): void
    {
        $user  = $this->makeStep3User();
        $phone = '5585999991234';

        Cache::put("whatsapp_code_{$phone}", '654321', now()->addMinutes(10));

        $user->forceFill(['phone' => $phone])->save();

        $this->actingAs($user)
            ->post(route('onboarding.step3.confirm'), [
                'code' => '654321',
            ])
            ->assertRedirect(route('home'));

        $updated = $user->fresh();
        $this->assertNotNull($updated->phone_verified_at);
        $this->assertEquals(4, $updated->onboarding_step);
        $this->assertFalse(Cache::has("whatsapp_code_{$phone}"));
    }

    #[Test]
    public function verification_fails_with_wrong_whatsapp_code(): void
    {
        $user  = $this->makeStep3User();
        $phone = '5585999991234';

        Cache::put("whatsapp_code_{$phone}", '654321', now()->addMinutes(10));
        $user->forceFill(['phone' => $phone])->save();

        $this->actingAs($user)
            ->post(route('onboarding.step3.confirm'), [
                'code' => '000000',
            ])
            ->assertSessionHasErrors('code');
    }

    #[Test]
    public function verification_fails_when_code_expired(): void
    {
        $user  = $this->makeStep3User();
        $phone = '5585999991234';

        // Cache já expirado — não inserimos nada
        $user->forceFill(['phone' => $phone])->save();

        $this->actingAs($user)
            ->post(route('onboarding.step3.confirm'), [
                'code' => '654321',
            ])
            ->assertSessionHasErrors('code');
    }

    #[Test]
    public function cannot_use_already_registered_phone(): void
    {
        // Outro usuário já tem esse celular verificado
        User::create([
            'name'              => 'Dono do Número',
            'email'             => 'dono@exemplo.com',
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'phone'             => '5585999991234',
            'phone_verified_at' => now(),
            'onboarding_step'   => 4,
        ]);

        $user = $this->makeStep3User();

        $this->actingAs($user)
            ->post(route('onboarding.step3.send'), [
                'phone' => '(85) 99999-1234',
            ])
            ->assertSessionHasErrors('phone');
    }
}