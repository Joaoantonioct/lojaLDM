<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($dados);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'E-mail ou senha inválidos.'])
                ->withInput($request->only('email', 'remember', 'redirect'));
        }

        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectAfterAuthentication(Request $request)
    {
        $destination = trim((string) $request->input('redirect', ''));

        if ($destination === ''
            || ! str_starts_with($destination, '/')
            || str_starts_with($destination, '//')
            || parse_url($destination, PHP_URL_SCHEME) !== null
            || parse_url($destination, PHP_URL_HOST) !== null) {
            return redirect()->route('cliente');
        }

        return redirect()->to($destination);
    }
}
