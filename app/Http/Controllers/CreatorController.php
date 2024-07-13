<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class CreatorController extends Controller
{
    public function index() {

        $users = User::whereNotNull('identity_verified_at')->orderBy('id', 'DESC')->paginate();

        return view('vendor.creator.read', compact('users'));
    }
}
