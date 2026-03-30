<?php

namespace Tests\Feature\Onboarding;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OnboardingStep2Test extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(array $extra = []): User
    {
        return User::create(array_merge([
            'name'              => 'João Silva',
            'email'             => 'joao@exemplo.com',
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'onboarding_step'   => 2,
        ], $extra));
    }

    #[Test]
    public function step2_page_is_accessible_for_verified_user(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->get(route('onboarding.step2'))
            ->assertStatus(200);
    }

    #[Test]
    public function unverified_user_cannot_access_step2(): void
    {
        $user = User::create([
            'name'     => 'Sem verificação',
            'email'    => 'sem@exemplo.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $this->actingAs($user)
            ->get(route('onboarding.step2'))
            ->assertRedirect(route('auth.verify.notice'));
    }

    #[Test]
    public function user_can_save_cpf_profile(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cpf',
                'document_number' => '529.982.247-25',
                'birth_date'      => '1990-05-15',
            ])
            ->assertRedirect(route('onboarding.step3'));

        $updated = $user->fresh();
        $this->assertEquals('cpf', $updated->profile_type);
        $this->assertEquals('52998224725', $updated->document_number);
        $this->assertEquals(3, $updated->onboarding_step);
    }

    #[Test]
    public function user_can_save_cnpj_profile(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cnpj',
                'document_number' => '11.222.333/0001-81',
                'company_name'    => 'Eventos Ltda',
            ])
            ->assertRedirect(route('onboarding.step3'));

        $updated = $user->fresh();
        $this->assertEquals('cnpj', $updated->profile_type);
        $this->assertEquals('11222333000181', $updated->document_number);
        $this->assertEquals('Eventos Ltda', $updated->company_name);
        $this->assertEquals(3, $updated->onboarding_step);
    }

    #[Test]
    public function cpf_profile_requires_birth_date(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cpf',
                'document_number' => '529.982.247-25',
                // birth_date ausente
            ])
            ->assertSessionHasErrors('birth_date');
    }

    #[Test]
    public function cnpj_profile_requires_company_name(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cnpj',
                'document_number' => '11.222.333/0001-81',
                // company_name ausente
            ])
            ->assertSessionHasErrors('company_name');
    }

    #[Test]
    public function invalid_cpf_is_rejected(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cpf',
                'document_number' => '111.111.111-11', // CPF inválido
                'birth_date'      => '1990-05-15',
            ])
            ->assertSessionHasErrors('document_number');
    }

    #[Test]
    public function invalid_cnpj_is_rejected(): void
    {
        $user = $this->makeVerifiedUser();

        $this->actingAs($user)
            ->post(route('onboarding.step2.store'), [
                'profile_type'    => 'cnpj',
                'document_number' => '11.111.111/1111-11', // CNPJ inválido
                'company_name'    => 'Empresa Teste',
            ])
            ->assertSessionHasErrors('document_number');
    }
}