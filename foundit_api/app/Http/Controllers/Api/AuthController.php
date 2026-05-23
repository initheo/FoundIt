<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @tags Authentication
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * Mendaftarkan user baru ke aplikasi FoundIt.
     * Email wajib menggunakan domain UISI (@student.uisi.ac.id atau @uisi.ac.id).
     * Setelah registrasi berhasil, user akan mendapatkan token untuk autentikasi.
     * 
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => $request->password,
            'phone' => $request->phone,
            'prodi_unit' => $request->prodi_unit,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'prodi_unit' => $user->prodi_unit,
                    'photo_url' => $user->photo_url,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Login user
     * 
     * Login ke aplikasi FoundIt dengan email dan password.
     * Email wajib menggunakan domain UISI (@student.uisi.ac.id atau @uisi.ac.id).
     * Setelah login berhasil, user akan mendapatkan token untuk autentikasi.
     * 
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'prodi_unit' => $user->prodi_unit,
                    'photo_url' => $user->photo_url,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Logout user
     * 
     * Logout dari aplikasi FoundIt.
     * Token yang digunakan akan dihapus dan tidak bisa digunakan lagi.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
