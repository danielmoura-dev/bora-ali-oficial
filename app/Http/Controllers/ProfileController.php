<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        // Se mudou o e-mail, exige reverificação
        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        $user->forceFill($validated)->save();

        // Se mudou o e-mail, envia novo código
        if ($validated['email'] !== $user->email || !$user->isEmailVerified()) {
            app(\App\Services\AuthService::class)->sendVerificationCode($user);
            return redirect()->route('auth.verify.notice')
                ->with('status', 'E-mail atualizado. Verifique seu novo endereço.');
        }

        return redirect()->route('profile.show')
            ->with('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('A senha atual está incorreta.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('profile.show')
            ->with('status', 'Senha atualizada com sucesso.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        // Remove avatar antigo se for um arquivo local
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->forceFill(['avatar' => $path])->save();

        return redirect()->route('profile.show')
            ->with('status', 'Foto de perfil atualizada.');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Senha incorreta.');
                    }
                },
            ],
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')
            ->with('status', 'Conta encerrada com sucesso.');
    }
}