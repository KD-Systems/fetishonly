<?php

namespace App\Http\Controllers;

use App\TrailLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class FreeTrailController extends Controller
{
    public function store(Request $request) {

        try {

            TrailLink::create([
                'user_id'   => Auth::user()->id,
                'slug'      => Str::uuid(),
                'name'      => $request->name,
                'limit'     => $request->limit,
                'expire_at'    => now()->addDays($request->expire),
                'duration'  => $request->duration,
            ]);

            return redirect()->route('my.settings', ['type' => 'rates']);

        } catch (\Exception $ex) {
            logger("ERROR: ", [$ex->getMessage()]);
            return redirect()->route('my.settings', ['type' => 'rates']);
        }

    }


    public function show($slug) {

    }

    public function delete($id) {
        $trail = TrailLink::where('user_id', Auth::user()->id)->where('id', $id)->firstOrFail();

        $trail->delete();
        return redirect()->route('my.settings', ['type' => 'rates']);
    }
}
