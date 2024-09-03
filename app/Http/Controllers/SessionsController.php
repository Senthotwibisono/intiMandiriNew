<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function unsetSession($key)
    {
        if (session()->has($key)) {
            session()->forget($key);
            return response()->json(['message' => 'Session variable unset']);
        }

        return response()->json(['message' => 'Session variable not found'], 404);
    }
}
