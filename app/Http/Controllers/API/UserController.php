<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Find user by email
            $credencials = request(['email', 'password']);
            if (!Auth::attempt($credencials)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception("Invalid password");
            }

            // Generate token
            $toketResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token' => $toketResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login success');
        } catch (Exception $e) {
            return ResponseFormatter::error('Authentication Failed');
        }
    }
}
