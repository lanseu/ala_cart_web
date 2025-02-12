<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->registerUser($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->only(['id', 'email', 'first_name', 'middle_name', 'last_name', 'full_name']),
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->authenticateUser($request->email, $request->password);
    
        if (!$user) {
            return response()->json([
                'message' => 'Invalid email or password. Please try again.'
            ], 401);
        }
    
        $token = $user->createToken('com.example.ala_cart')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'user' => $user->only(['id', 'email', 'full_name']), // 'full_name' is now included
        ]);
    }
    

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message'=> 'User has logged out successfully'
        ],200);
    }
}
