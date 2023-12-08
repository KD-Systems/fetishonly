<?php

namespace App\Http\Controllers;

use App\TwitterAccess;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TwitterAccessController extends Controller
{
    public function index(Request $request) {

        if(!$request->has('code')) {
            return redirect()->route('my.settings', ['type' => 'twitter']);
        }

        $client_id = env('X_CLIENT_ID', '');
        $client_secret = env('X_CLIENT_SECRET', '');
        $redirect_url = env('X_REDIRECT_URI', '');
        $basic_auth = base64_encode($client_id.':'.$client_secret);

        $client = new Client();

        try {
            $response = $client->post('https://api.twitter.com/2/oauth2/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.$basic_auth
                ],
                'form_params' => [
                    'code'          => $request->code,
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $client_id,
                    'redirect_uri'  => $redirect_url,
                    'code_verifier' => 'challenge'
                ]
            ]);

            if($response->getStatusCode() != 200)
                throw new Exception('Error');

            $response = json_decode($response->getBody()->getContents(), true);

            TwitterAccess::where('user_id', Auth::user()->id)->delete();

            TwitterAccess::create([
                'user_id'   => Auth::user()->id,
                'code'      => $request->code,
                'access_token'  => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
                'refreshed_at'  => now()
            ]);

            return redirect()->route('my.settings', ['type' => 'twitter']);

        } catch (Exception $ex) {
            return $ex->getMessage();
            return redirect()->route('my.settings', ['type' => 'twitter']);
        }
    }

    public function discounnect() {
        $twitterAccess = TwitterAccess::where('user_id', Auth::user()->id)->first();

        if($twitterAccess)
            $twitterAccess->delete();

        return back();
    }

    public function test() {

        $twitterAccess = TwitterAccess::where('user_id', Auth::user()->id)->first();

        return getTwitterToken($twitterAccess);


    }
}
