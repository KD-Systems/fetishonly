<?php

namespace App\Http\Controllers;

use App\Model\Subscription;
use App\TrailLink;
use App\TrailLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $trailLink = TrailLink::where('slug', $slug)->firstOrFail();
        $profile = User::find($trailLink->user_id);

        return view('free-trail-box', compact('profile', 'trailLink'));
    }

    public function delete($id) {
        $trail = TrailLink::where('user_id', Auth::user()->id)->where('id', $id)->firstOrFail();

        if($trail)
            TrailLink::where('id', $id)->delete();

        return redirect()->route('my.settings', ['type' => 'rates']);
    }

    public function active($slug) {
        $trailLink = TrailLink::where('slug', $slug)->firstOrFail();

        if($trailLink->expire_at <= now() || $trailLink->trailLog->count() >= $trailLink->limit)
            return back();

        $user = User::find($trailLink->user_id);


        DB::beginTransaction();

        try {
            TrailLog::create([
                'user_id' => Auth::user()->id,
                'trail_links_id'    => $trailLink->id
            ]);

            Subscription::create([
                'sender_user_id' => Auth::user()->id,
                'recipient_user_id' => $trailLink->user_id,
                'type'  => 'free-trail',
                'provider'  => 'freetrail',
                'status'    => 'completed',
                'expires_at'    => now()->addDays($trailLink->duration),
                'amount'        => 0.00
            ]);

            DB::commit();

            return redirect()->route('profile', ['username' => $user->username]);

        } catch (\Exception $ex) {
            //throw $th;
            logger("ERROR: ", [$ex->getMessage()]);
            DB::rollBack();
            return back();
        }
    }
}
