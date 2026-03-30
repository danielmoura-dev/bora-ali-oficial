<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Rules\ValidCnpj;
use App\Rules\ValidCpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function step2()
    {
        return view('onboarding.step2');
    }

    public function step2Store(Request $request)
    {
        $profileType = $request->input('profile_type');

        $rules = [
            'profile_type'    => ['required', 'in:cpf,cnpj'],
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
            'profile_type'    => $profileType,
            'document_number' => $onlyDigits,
            'birth_date'      => $validated['birth_date'] ?? null,
            'company_name'    => $validated['company_name'] ?? null,
            'onboarding_step' => 3,
        ])->save();

        return redirect()->route('onboarding.step3');
    }

    public function step3()
    {
        return view('onboarding.step3');
    }
}