<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Dados básicos
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // nullable para login via Google

            // OAuth Google
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();

            // Onboarding (passo a passo)
            $table->unsignedTinyInteger('onboarding_step')->default(1);

            // Perfil (Passo 2 do onboarding)
            $table->enum('profile_type', ['cpf', 'cnpj'])->nullable();
            $table->string('document_number', 18)->nullable(); // CPF: 11, CNPJ: 14 dígitos
            $table->string('company_name')->nullable();        // Somente CNPJ
            $table->date('birth_date')->nullable();            // Somente CPF

            // Celular (Passo 3 do onboarding)
            $table->string('phone', 20)->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            // Verificação de e-mail
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};