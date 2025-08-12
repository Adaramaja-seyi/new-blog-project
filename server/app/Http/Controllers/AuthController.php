<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',

                Password::min(8)
                // ->letters()
                // ->mixedCase()
                // ->numbers()
                // ->symbols()
                // ->uncompromised()
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $token = $user->createToken($request->name)->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // Login existing user
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken($user->name)->plainTextToken,
        ]);
    }

    // Logout current user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }


    public function getUserData(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'posts' => $user->posts()->with('comments')->get(),
            'comments' => $user->comments()->with('post')->get(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        // Log the authenticated user for debugging
        Log::info('Authenticated user', ['user' => $request->user()]);
        // Log each field individually for debugging
        Log::info('Profile update fields', [
            'user_id' => optional($request->user())->id,
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
            'gender' => $request->input('gender'),
            'avatar' => $request->file('avatar') ? $request->file('avatar')->getClientOriginalName() : null,
            'all' => $request->all(),
        ]);
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
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }
}
