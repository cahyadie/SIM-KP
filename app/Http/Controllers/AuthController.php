<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return $this->redirectByRole();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function handleProviderCallback()
    {
        $microsoftUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('email', $microsoftUser->getEmail())->first();

        if ($user) {
            $user->update([
                'microsoft_id' => $microsoftUser->getId(),
                'avatar' => $microsoftUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $microsoftUser->getName(),
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'avatar' => $microsoftUser->getAvatar(),
                'role' => 'mahasiswa',
                'password' => bcrypt(Str::random(16)),
                'nomor_induk' => null,
            ]);
        }

        Auth::login($user);

        return $this->redirectByRole();
    }

    private function redirectByRole()
    {
        return Auth::user()->role === 'mahasiswa'
            ? redirect()->intended('mahasiswa/dashboard')
            : redirect()->intended('/dashboard');
    }
}
