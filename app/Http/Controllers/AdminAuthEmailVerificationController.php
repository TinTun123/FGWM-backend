<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminAuthEmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $token)
    {
        //
        
        $admin = User::where('verification_token')->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Invalid verfication token.',

            ], 400);
        }

        if ($admin->hasVerifiedEmail()) {
            return response()->json();
        }
    }
}
