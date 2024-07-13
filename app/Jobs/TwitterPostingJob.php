<?php

namespace App\Jobs;

use App\Model\Post;
use App\TwitterAccess;
use App\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TwitterPostingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media_id = false;
        $route = route('posts.get', ['post_id' => $this->post->id, 'username' => $this->user->username]);
        $text = substr($this->post->text, 0, 180);

        if(strlen($this->post->text) != strlen($text))
            $text = $text.'...';

        $twitterAccess = TwitterAccess::where('user_id', $this->user->id)->first();

        if(!$twitterAccess)
            return;

        $twitterAccess = getTwitterToken($twitterAccess);

        if(!$twitterAccess)
            return;


        $twitterUser = $this->getTwiteerUser($twitterAccess);

        $client = new Client();

        if($this->post->attachments->count() > 0) {
            $media_id = $this->uploadMedia($this->post->attachments->first()->path, $twitterUser['data']['id'])->media_id;
        }

        if($media_id != false) {
            $json = [
                'text' => "$text $route",
                "media" => [
                    "media_ids" => ["$media_id"]
                ]
            ];
        } else {
            $json = [
                'text' => "$text $route",
            ];
        }


        try {
            $client->post('https://api.twitter.com/2/tweets', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '. $twitterAccess->access_token
                ],
                'json' => $json
            ]);
        } catch (Exception $ex) {
            logger("Twitter Posting Exception: ", [$ex->getMessage()]);
        }


    }


    private function getTwiteerUser($twitterAccess) {
        $client = new Client();

        try {
            $response = $client->post('https://api.twitter.com/2/users/me', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '. $twitterAccess->access_token
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $th) {
            logger("Error: ", [$th->getMessage()]);
        }
    }

    private function uploadMedia($url, $ownerId) {
        $oauth = new Oauth1([
            'consumer_key'    => env('X_API_KEY'),
            'consumer_secret' => env('X_API_SECRET'),
            'token'           => env('X_ACCESS_TOKEN'),
            'token_secret'    => env('X_TOKEN_SECRET')
        ]);

        $client = new Client([
            'handler' => \GuzzleHttp\HandlerStack::create(),
            'auth' => 'oauth'
        ]);

        $client->getConfig('handler')->push($oauth);

        $image_path = file_get_contents($url);

        try {
            // Upload the image
            $response = $client->post('https://upload.twitter.com/1.1/media/upload.json', [
                'multipart' => [
                    [
                        'name'     => 'media',
                        'contents' => (string) $image_path
                    ],
                    [
                        'name'     => 'media_category',
                        'contents' => 'tweet_image'
                    ],
                    [
                        'name'     => 'additional_owners',
                        'contents' => ["$ownerId"]
                    ]
                ]
            ]);

            return $media = json_decode($response->getBody()->getContents());

        } catch (RequestException $e) {
            logger("Error uploading: ", [$e->getMessage()]);
        }
    }
}
