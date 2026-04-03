<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function show(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'token'                 => ['required'],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('auth.login')
            ->with('status', 'Senha redefinida com sucesso! Faça login com a nova senha.');
    }
}
