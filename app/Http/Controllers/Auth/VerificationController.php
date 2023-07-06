<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class VerificationController extends Controller
{

    public function verify(Request $request, $id) {

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        if(!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }




        return response()->json([
            'message' => 'Email verified'
        ], 200);
    }

    public function resend(Request $request) {

        if($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }
    }
}
