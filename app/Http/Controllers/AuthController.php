<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function index()
    {
        return view('login');
    }

    // Proses Login Manual (Email & Password)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Cek Role dan Redirect
            $user = Auth::user();
            
            // Mahasiswa ke Dashboard Khusus
            if ($user->role == 'mahasiswa') {
                return redirect()->intended('mahasiswa/dashboard');
            }
            
            // Admin, Kaprodi, Dosen ke Dashboard Terpusat
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // 1. Redirect user ke halaman login Microsoft
    public function redirectToProvider()
    {
        return Socialite::driver('azure')->redirect();
    }

    // 2. Handle balikan dari Microsoft
    public function handleProviderCallback()
    {
        // Menggunakan stateless() untuk menghindari error InvalidStateException
        $microsoftUser = Socialite::driver('azure')->stateless()->user();
        
        // Cek apakah user dengan email ini sudah ada?
        $existingUser = User::where('email', $microsoftUser->getEmail())->first();

        if ($existingUser) {
            // Jika user sudah ada, update link account
            $existingUser->update([
                'microsoft_id' => $microsoftUser->getId(),
                'avatar' => $microsoftUser->getAvatar(),
            ]);
            
            Auth::login($existingUser);
        } else {
            // Jika user belum ada, BUAT BARU sebagai MAHASISWA
            $newUser = User::create([
                'name' => $microsoftUser->getName(),
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'avatar' => $microsoftUser->getAvatar(),
                'role' => 'mahasiswa', // Default Role Mahasiswa
                'password' => bcrypt(Str::random(16)), 
                'nomor_induk' => null, 
            ]);
            
            Auth::login($newUser);
        }

        // Redirect Logic
        $role = Auth::user()->role;
        
        // Mahasiswa ke Dashboard Khusus
        if ($role == 'mahasiswa') {
            return redirect()->intended('mahasiswa/dashboard');
        }

        // Role Lain (Admin, Kaprodi, Dosen) ke Dashboard Terpusat
        return redirect()->intended('/dashboard');
    }
}