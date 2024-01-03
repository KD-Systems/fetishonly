<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FetchCreatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $request) {
        $users = User::where('id', '!=', Auth::user()->id)->where('username', 'LIKE', $request->mention.'%')->orWhere('name', 'LIKE', $request->mention.'%')->whereNotNull('identity_verified_at')->get();
        return response()->json($users);
    }
}
