<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Log request payload before any processing (without sensitive data)
        Log::info('Register request received', [
            'payload' => [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'has_password' => $request->filled('password'),
            ],
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => [
                    'required',
                    Password::min(8)
                ],
            ]);

            // Let the Eloquent 'hashed' cast handle password hashing
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors with 422 Unprocessable Entity
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            // Handle common DB errors like unique email constraint and missing tables
            $sqlState = $e->getCode();
            $errorInfo = method_exists($e, 'errorInfo') ? $e->errorInfo : null;

            // Duplicate key (PostgreSQL 23505, MySQL 23000 with duplicate entry)
            if ($sqlState === '23505' || ($sqlState === '23000' && str_contains(strtolower($e->getMessage()), 'duplicate'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'email' => ['The email has already been taken.']
                    ]
                ], 422);
            }

            // Missing table (PostgreSQL 42P01, MySQL 42S02)
            if ($sqlState === '42P01' || $sqlState === '42S02') {
                Log::error('Registration failed due to missing table', [
                    'sql_state' => $sqlState,
                    'message' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Server misconfiguration: database tables are missing. Run migrations.'
                ], 500);
            }

            Log::error('Registration QueryException', [
                'sql_state' => $sqlState,
                'message' => $e->getMessage(),
                'error_info' => $errorInfo,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Database error during registration'
            ], 500);
        } catch (\Throwable $e) {
            Log::error('Registration failed with unexpected error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
            ], 500);
        }
    }

    // Login existing user
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'email' => ['The credentials are incorrect.']
                    ]
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user'  => $user,
                    'token' => $user->createToken($user->name)->plainTextToken,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Logout current user
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getUserData(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'posts' => $user->posts()->with('comments')->get(),
                    'comments' => $user->comments()->with('post')->get(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            // Validate the request
            $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'bio' => 'nullable|string|max:1000',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'gender' => 'nullable|string|in:male,female,other',
            ]);

            // Update only fields that are present in the request (including empty strings)
            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->has('bio')) {
                $user->bio = $request->input('bio');
            }
            if ($request->has('gender')) {
                $user->gender = $request->input('gender');
            }

            // Handle file upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = '/storage/' . $avatarPath;
            }

            // Save the user
            $user->save();

            // Return fresh user data
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
