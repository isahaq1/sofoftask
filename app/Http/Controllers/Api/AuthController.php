<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiBaseController
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendSuccess(
                ['user' => $user, 'token' => $token],
                'User registered successfully',
                201
            );
        } catch (ValidationException $e) {
            return $this->sendError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Registration failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->sendError(
                    'Invalid credentials',
                    ['email' => ['The provided credentials are incorrect.']],
                    401
                );
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendSuccess(
                ['user' => $user, 'token' => $token],
                'Login successful'
            );
        } catch (ValidationException $e) {
            return $this->sendError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Login failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            // Delete all tokens for the current user instead of just the current one
            $request->user()->tokens()->delete();

            return $this->sendSuccess(
                data: null,
                message: 'Successfully logged out',
                statusCode: 200
            );
        } catch (\Exception $e) {
            return $this->sendError(
                message: 'Logout failed',
                errors: ['error' => $e->getMessage()],
                code: 500
            );
        }
    }
}
