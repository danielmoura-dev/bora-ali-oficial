<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function users_table_has_all_required_columns(): void
    {
        $columns = [
            'id', 'name', 'email', 'password',
            'google_id', 'avatar',
            'profile_type',
            'document_number',
            'company_name',
            'birth_date',
            'phone',
            'phone_verified_at',
            'email_verified_at',
            'verification_code',
            'verification_code_expires_at',
            'onboarding_step',
            'remember_token',
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('users', $column),
                "Coluna ausente na tabela users: {$column}"
            );
        }
    }

    #[Test]
    public function user_model_fillable_fields_are_correct(): void
    {
        $user = new User();

        $expected = [
            'name', 'email', 'password',
            'google_id', 'avatar',
            'profile_type', 'document_number',
            'company_name', 'birth_date',
            'phone', 'phone_verified_at',
            'email_verified_at',
            'verification_code',
            'verification_code_expires_at',
            'onboarding_step',
        ];

        foreach ($expected as $field) {
            $this->assertContains(
                $field,
                $user->getFillable(),
                "Campo '{$field}' não está no \$fillable do modelo User."
            );
        }
    }

    #[Test]
    public function user_model_hides_sensitive_fields(): void
    {
        $user = new User();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
        $this->assertContains('verification_code', $hidden);
    }

    #[Test]
    public function user_can_be_created_with_minimal_data(): void
    {
        $user = User::create([
            'name'     => 'João Silva',
            'email'    => 'joao@exemplo.com',
            'password' => bcrypt('senha123'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'joao@exemplo.com']);
        $this->assertEquals(1, $user->onboarding_step);
    }

    #[Test]
    public function user_profile_type_accepts_cpf_and_cnpj(): void
    {
        $cpfUser = User::create([
            'name'         => 'Maria CPF',
            'email'        => 'maria@exemplo.com',
            'password'     => bcrypt('senha123'),
            'profile_type' => 'cpf',
        ]);

        $cnpjUser = User::create([
            'name'         => 'Empresa CNPJ',
            'email'        => 'empresa@exemplo.com',
            'password'     => bcrypt('senha123'),
            'profile_type' => 'cnpj',
        ]);

        $this->assertEquals('cpf', $cpfUser->profile_type);
        $this->assertEquals('cnpj', $cnpjUser->profile_type);
    }

    #[Test]
    public function verification_code_is_hidden_from_serialization(): void
    {
        $user = User::create([
            'name'              => 'Teste Código',
            'email'             => 'teste@exemplo.com',
            'password'          => bcrypt('senha123'),
            'verification_code' => '123456',
        ]);

        $array = $user->toArray();
        $this->assertArrayNotHasKey('verification_code', $array);
    }
}