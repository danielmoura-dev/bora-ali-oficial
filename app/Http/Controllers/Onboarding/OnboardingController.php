<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Rules\ValidCnpj;
use App\Rules\ValidCpf;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function __construct(private WhatsAppService $whatsApp)
    {
    }

    // ── Step 2 ────────────────────────────────────────────────

    public function step2()
    {
        return view('onboarding.step2');
    }

    public function step2Store(Request $request)
    {
        $profileType = $request->input('profile_type');

        $rules = [
            'profile_type' => ['required', 'in:cpf,cnpj'],
            'document_number' => [
                'required',
                'string',
                $profileType === 'cpf' ? new ValidCpf() : new ValidCnpj(),
            ],
        ];

        if ($profileType === 'cpf') {
            $rules['birth_date'] = ['required', 'date', 'before:today'];
        }

        if ($profileType === 'cnpj') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $onlyDigits = preg_replace('/\D/', '', $validated['document_number']);

        Auth::user()->forceFill([
            'profile_type' => $profileType,
            'document_number' => $onlyDigits,
            'birth_date' => $validated['birth_date'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'onboarding_step' => 3,
        ])->save();

        return redirect()->route('onboarding.step3');
    }

    // ── Step 3 ────────────────────────────────────────────────

    public function step3()
    {
        return view('onboarding.step3');
    }

    public function step3Send(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->whatsApp->isValidBrazilianPhone($value)) {
                        $fail('Número de celular inválido. Use o formato (99) 99999-9999.');
                    }
                },
            ],
        ]);

        // Formata antes de verificar unicidade
        $formatted = $this->whatsApp->formatPhone($request->phone);

        $alreadyUsed = \App\Models\User::where('phone', $formatted)
            ->whereNotNull('phone_verified_at')
            ->where('id', '!=', Auth::id())
            ->exists();

        if ($alreadyUsed) {
            return back()->withErrors([
                'phone' => 'Este número já está cadastrado em outra conta.',
            ]);
        }

        Auth::user()->forceFill([
            'phone'             => $formatted,
            'phone_verified_at' => now(),
            'onboarding_step'   => 4,
        ])->save();

        return redirect()->route('home');
    }
    public function step3Verify()
    {
        if (!Auth::user()->phone) {
            return redirect()->route('onboarding.step3');
        }

        return view('onboarding.step3-verify');
    }

    public function step3Confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();
        $phone = $user->phone;

        $valid = $this->whatsApp->verifyCode($phone, $request->code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Código inválido ou expirado.',
            ]);
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'onboarding_step' => 4,
        ])->save();

        return redirect()->route('home');
    }
}