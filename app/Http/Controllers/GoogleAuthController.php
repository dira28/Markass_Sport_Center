<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function googleSuccess(Request $request)
    {
        $payload = json_decode(
            base64_decode($request->user),
            true
        );

        session([
            'token' => $request->token,
            'user' => $payload
        ]);

        return redirect()->route('profile');
    }
}