<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // تحديد إذا كان المستخدم مسؤولاً بناءً على الإيميل
        $isAdmin = $request->email === 'admin@events.com';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_admin' => $isAdmin,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'is_admin' => $user->is_admin // إرجاع حالة الصلاحية
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'is_admin' => $user->is_admin // إرجاع حالة الصلاحية
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                $currentToken = $user->currentAccessToken();
                // If the current token supports deletion, delete it; otherwise, delete all tokens
                if (method_exists($currentToken, 'delete')) {
                    $currentToken->delete();
                } else {
                    $user->tokens()->delete();
                }
                \Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}