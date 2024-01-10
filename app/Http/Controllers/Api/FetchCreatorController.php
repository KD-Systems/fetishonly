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

        $input = $request->all();

        $users = User::when($request->has('not'), function($q) use ($input){
            return $q->whereNotIn('id', $input['not']);
        })->where('id', '!=', Auth::user()->id)
        ->where(function($q) use ($request) {
            return $q->where('username', 'LIKE', $request->mention.'%')->orWhere('name', 'LIKE', $request->mention.'%')->whereNotNull('identity_verified_at');
        })
        ->select('id', 'name', 'username')
        ->get();
        return response()->json($users);
    }
}
