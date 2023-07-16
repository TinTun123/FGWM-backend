<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //
    public function login(Request $request) {
        try {
    
            $user = User::where('email', '=', $request->input('email'))->firstOrFail();
            if(!$user) {
                return response()->json([
                    'user' => 'Email incorrect'
                ], 400);
            }
            if(!Hash::check($request->input('pwd'), $user->password)) {
                return response()->json([
                    'user' => 'Incorrect password.'
                ], 401);
            }

            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'user' => 'Email incorrect.'
            ], 400);
        }
    }

    public function logout(Request $request) {
        
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function getUser(Request $request) {
        $id = Auth::user()->id;
        $user = User::withCount(['protests', 'activities', 'article', 'campagins', 'news'])->get()->toArray();
        return response()->json([
            'users' => $user,
            'currentId' => $id
        ], 200);
    }

    public function updateProfile(Request $request, $id) {
        $currentId = Auth::user()->id;
        
        $currentUser = User::findOrFail($currentId);

        if($currentUser && $currentId === (int) $id) {
            if($request->hasFile('profile')) {
                $image = $request->file('profile');
                $path = $image->store('images/profiles/', 'public');
                $currentUser->img_url = asset($path);
            }

            if($request->input('name')) {
                $currentUser->name = $request->input('name');
            }

            $currentUser->save();
            return response()->json([
                'msg' => 'Update success',
                'url' => $currentUser->img_url,
                'name' => $request->name
            ], 200);

        }

        if ($currentUser && $currentUser->user_level === 2) {
            $targetUser = User::findOrFail((int) $id);

            if ($targetUser) {
                if($request->hasFile('profile')) {
                    $image = $request->file('profile');
                    $path = $image->store('images/profiles/', 'public');
                    $targetUser->img_url = asset($path);
                }

                if($request->input('name')) {
                    $targetUser->name = $request->input('name');
                }

                $targetUser->save();
                return response()->json([
                    'msg' => 'update success'
                ], 200);
            }
        }

    }

    public function updatePassword(Request $request, $id) {
        $request->validate([
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required| min:8'
        ]);

        $currentId = Auth::user()->id;
        
        $currentUser = User::findOrFail($currentId);

        if ($currentUser && $currentId === (int) $id) {
            if($request->input('password')) {
                $currentUser->password = Hash::make($request->input('password'));
                $currentUser->save();

                PersonalAccessToken::where('tokenable_id', $currentUser->id)->delete();

                return response()->json([
                    'msg' => 'Password updated for user ' . $currentUser->name
                ], 200);
            }
        }

        if($currentUser && $currentUser->user_level === 2) {
            if($request->input('password')) {
                $targetUser = User::findOrFail($id);
                $targetUser->password = Hash::make($request->input('password'));
                $targetUser->save();

                PersonalAccessToken::where('tokenable_id', $targetUser->id)->delete();

                return response()->json([
                    'msg' => 'Password updated for user ' . $targetUser->name,
                ], 200);
            }
        }
    }

    public function updateEmail(Request $request, $id) {
        $currentId = Auth::user()->id;
        
        $currentUser = User::findOrFail($currentId);

        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if($currentUser && $currentId === (int) $id) {
            if($request->input('email')) {
                $currentUser->email = $request->input('email');
                $currentUser->save();

                return response()->json([
                    'msg' => 'Email updated for ' . $currentUser->name . '. Please verify email to have full access.',
                    'email' => $request->input('email'),
                ], 200);
            }
        }

        if($currentUser && $currentUser->user_level === 2) {
            if($request->input('email')) {
                $targetUser = User::findOrFail($id);
                $targetUser->email = $request->input('email');
                $targetUser->save();

                return response()->json([
                    'msg' => 'Email updated for ' . $currentUser->name . '. Please verify email to have full access.'
                ], 200);
            }
        }
    }
}
