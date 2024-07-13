<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest.manager');
    }

    public function login() {
        return view('manager.login');
    }


    public function attempt(Request $request) {

        $user = User::where('email', $request->email)->whereIn('role_id', [1,4])->first();

        if (!$user) {
            return back()->with('error', 'User not found!');
        }

        if(Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->has('remember'))); {
            return redirect()->route('manager.dashboard');
        }

        return back()->with('error', 'Invalid credentials!');

    }
}
