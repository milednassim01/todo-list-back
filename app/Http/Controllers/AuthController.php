<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users,email',
            'name' => 'required|string|between:3,255',
            'password' => 'required|string|between:8,255',
        ]);

        User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password)

        ]);
        return response()->json(['message' => 'Your account has been created successfully', "success" => true]);
    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|between:8,255',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'invalid email or invalid password',
            ], 401);
        }
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            return response()->json([
                'token' => $token,
                "message" => 'login successfully'
            ]);
        } else {
            return response()->json(['message' => 'Password mismatch'], 422);
        }
    }
}
