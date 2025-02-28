<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserLoginRequest;
use App\Http\Requests\API\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //function for registering the user and returning the Barear token
    public function register(UserRegisterRequest $request)
    {
        //create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        //return the token
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }


    //function for logging in the user and returning the Barear token
    public function login(UserLoginRequest $request)
    {
        //check if the user exists
        $user = User::where('email', $request->email)->first();

        //check if the user exists
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        //return the token
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

    //function for logging out the user
    public function logout(Request $request)
    {
        //revoke the token
        $request->user()->currentAccessToken()->delete();

        //return the response
        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }
}
