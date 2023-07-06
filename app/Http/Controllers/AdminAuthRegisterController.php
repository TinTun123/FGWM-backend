<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
        Log::info('check', [
            $request->input('password'),
            $request->input('confi')
        ]);

        $request->validate([

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required| min:8'
            
        ]);

        if($request->input('password') !== $request->input('password_confirmation')) {
            return response()->json([
                'error' => 'Password and comfirm password not match'
            ], 400);
        }
        
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $admin->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Admin registered successfully. Please check your email for verification.',
            'admin' => $admin,
        ]);
    }
}
