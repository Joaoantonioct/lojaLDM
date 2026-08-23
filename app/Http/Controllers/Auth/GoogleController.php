<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redireciona o usuário para o Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Manipula o callback do Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Procura por um usuário existente com o email do Google
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Se o usuário existe, atualiza informações do Google se necessário
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Se o usuário não existe, cria um novo
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)), // Senha aleatória
                ]);
            }

            // Faz login do usuário
            Auth::login($user);

            // Redireciona para a página inicial ou dashboard
            return redirect()->intended('/');

        } catch (\Exception $e) {
            // Em caso de erro, redireciona para login com mensagem de erro
            return redirect()->route('login')
                ->with('error', 'Erro ao autenticar com Google. Tente novamente.');
        }
    }
}
