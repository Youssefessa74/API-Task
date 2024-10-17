<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone_number' => 'required|string|unique:users',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $request->name,
        'phone_number' => $request->phone_number,
        'password' => Hash::make($request->password),
        'verification_code' => random_int(100000, 999999), // Generate 6-digit code
    ]);

    // Log the verification code
    Log::info('Verification code for ' . $user->phone_number . ': ' . $user->verification_code);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['user' => $user, 'token' => $token]);
   }

   public function login(Request $request)
   {
       $request->validate([
           'phone_number' => 'required|string',
           'password' => 'required|string',
       ]);

       $user = User::where('phone_number', $request->phone_number)->first();

       if (! $user || ! Hash::check($request->password, $user->password)) {
           return response()->json(['message' => 'Invalid login credentials'], 401);
       }

       if (!$user->is_verified) {
           return response()->json(['message' => 'Please verify your account'], 403);
       }

       $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json(['user' => $user, 'token' => $token]);
   }

   public function verify(Request $request)
{
    $request->validate([
        'phone_number' => 'required|string',
        'verification_code' => 'required|integer',
    ]);

    $user = User::where('phone_number', $request->phone_number)->first();

    if (!$user || $user->verification_code != $request->verification_code) {
        return response()->json(['message' => 'Invalid verification code'], 400);
    }

    $user->is_verified = true;
    $user->verification_code =0;
    $user->save();

    return response()->json(['message' => 'Account verified successfully']);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully']);
}


}
