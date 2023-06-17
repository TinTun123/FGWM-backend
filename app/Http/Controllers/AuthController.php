<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(Request $request) {
        try {
    
            $user = User::where('email', '=', $request->input('email'))->firstOrFail();
            
            if(!Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'user' => 'password went wrong'
                ]);
            }

            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in AuthController.login'
            ]);
        }
    }
}
