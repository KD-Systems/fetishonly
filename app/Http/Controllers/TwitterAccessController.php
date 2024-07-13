<?php

namespace App\Http\Controllers;

use App\TwitterAccess;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;

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

        $api_key = 'your_api_key';
        $api_secret_key = 'your_api_secret_key';
        $access_token = 'your_access_token';
        $access_token_secret = 'your_access_token_secret';


        $oauth = new Oauth1([
            'consumer_key'    => 'hJLoUJn1BFJz6pCQIR687Z0PU',
            'consumer_secret' => 'rVoqYLIkaEYLxTZj3pW128h8P17eH1cIWx4ytygwJuWD0Sp2Pc',
            'token'           => '82729409-5wKNRJog7k4nq1LxueCVoToS7HrvVb7ojZeRl3RB9',
            'token_secret'    => '4NRIdRMYPw6JXk9edfLLqZSANA0ZMopyPDDb2pMZe1cUA'
        ]);

        $stack = HandlerStack::create($oauth);

        $client = new Client([
            'handler' => \GuzzleHttp\HandlerStack::create(),
            'auth' => 'oauth'
        ]);
        $client->getConfig('handler')->push($oauth);

        // $image_path = 'path/to/your/image.jpg';

        // return Storage::disk('public')->get('image.png');
        $image_path = file_get_contents('https://immersion-next.vercel.app/_next/image?url=%2Fimages%2Fscreen-mockup.jpg&w=1080&q=75');


        try {
            // Upload the image
            $response = $client->post('https://upload.twitter.com/1.1/media/upload.json', [
                'multipart' => [
                    [
                        'name'     => 'media',
                        'contents' => (string) $image_path
                    ]
                ]
            ]);

            return $media = json_decode($response->getBody()->getContents());

        } catch (RequestException $e) {
            echo "Error: " . $e->getMessage();
        }


    }
}
