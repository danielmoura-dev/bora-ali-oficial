<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = preg_replace('/\D/', '', $value);

        if (strlen($cnpj) !== 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            $fail('CNPJ inválido.');
            return;
        }

        $calcDigit = function (string $cnpj, int $length): int {
            $sum    = 0;
            $pos    = $length - 7;
            for ($i = $length; $i >= 1; $i--) {
                $sum += $cnpj[$length - $i] * $pos--;
                if ($pos < 2) $pos = 9;
            }
            return $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        };

        if ($calcDigit($cnpj, 12) != $cnpj[12] ||
            $calcDigit($cnpj, 13) != $cnpj[13]) {
            $fail('CNPJ inválido.');
        }
    }
}