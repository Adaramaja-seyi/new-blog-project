<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => [
                    'required',
                    Password::min(8)
                ],
            ]);

            $user = User::create([
                'id' => (string) Str::uuid(),
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
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
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
                ], 422); 
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
        // dd($request->all());
        try {
            $user_id = $request->input('user_id');
           
            $user = User::with([
                'posts',
                'comments',
                'likes'
            ])->findOrFail($user_id)->first();
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,

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
        // dd($request->all());
        try {
            $user = $request->user();

            // Validate the request
            $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
                'gender' => 'nullable|string|in:male,female,other',
            ]);

            if ($request->has('name')) {
                $user->name = $request->name;
            }
         
            if ($request->has('email')) {
                $user->email = $request->email;
            }
           
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('location')) {
                $user->location = $request->location;
            }
            if ($request->has('bio') && $request->bio > 0) {
                $user->bio = $request->bio;
            }
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                try {
                    // Delete old profile image if exists
                    if ($user->profile_image) {
                        $oldImagePath = str_replace('/storage/', '', $user->profile_image);
                        if (Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }

                    // Get the file
                    $file = $request->file('profile_image');
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Ensure the directory exists
                    $uploadPath = storage_path('app/public/profile-images');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    //  store in public storage
                    $profileImagePath = $file->storeAs('profile-images', $fileName, 'public');

                    // Verify the file was actually saved
                    if (!$profileImagePath || !Storage::disk('public')->exists('profile-images/' . $fileName)) {
                        throw new \Exception('Failed to save file to storage');
                    }

                    $user->profile_image = '/storage/' . $profileImagePath;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Profile image upload failed: ' . $e->getMessage()
                    ], 500);
                }
            }
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

    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            // Validate the request
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string',
            ]);

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => [
                        'current_password' => ['Current password is incorrect']
                    ]
                ], 422); 
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();
            
            $user->posts()->delete();

            $user->comments()->delete();

            $user->likes()->delete();
            
            if ($user->profile_image) {
                $profileImagePath = str_replace('/storage/', '', $user->profile_image);
                if (Storage::disk('public')->exists($profileImagePath)) {
                    Storage::disk('public')->delete($profileImagePath);
                }
            }
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
