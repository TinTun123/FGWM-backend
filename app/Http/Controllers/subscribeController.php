<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Illuminate\Http\Request;

class subscribeController extends Controller
{
    //
    public function store(Request $request) {
        $request->validate([
            'email' => 'required|email|unique:subscribes,email',
        ]);

        $subscribe = Subscribe::create([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Thanks for subscription. We will notify you our activities',
            'subscribe' => $subscribe
        ]);
    }
}
