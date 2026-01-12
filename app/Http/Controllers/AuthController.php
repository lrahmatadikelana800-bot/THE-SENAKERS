<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Try using the auth guard first; fallback to JWTAuth facade for resilience
        $token = null;
        try {
            $token = auth('api')->attempt($credentials);
        } catch (\Exception $e) {
            // guard may not be available; try JWTAuth directly
        }

        if (!$token) {
            try {
                $token = JWTAuth::attempt($credentials);
            } catch (\Exception $e) {
                // ignore and return auth error below
            }
        }

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        $ttl = config('jwt.ttl', 60);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60
        ]);
    }

    public function logout(Request $request)
    {
        try {
            // Prefer parsing the token (handles Bearer header automatically)
            if (JWTAuth::getToken()) {
                JWTAuth::parseToken()->invalidate();
            } else {
                // Fallback to auth guard logout if available
                if (auth('api')->check()) {
                    auth('api')->logout();
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Authorization token not provided'
                    ], 401);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'error', 'message' => 'Token sudah kadaluarsa'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'error', 'message' => 'Token tidak valid'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengeluarkan token'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }
}
