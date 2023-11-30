<?php

namespace App\Jobs;

use App\Model\Post;
use App\TwitterAccess;
use App\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        $client = new Client();


        try {
            $client->post('https://api.twitter.com/2/tweets', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '. $twitterAccess->access_token
                ],
                'json' => [
                    'text' => "$text $route"
                ]
            ]);
        } catch (Exception $ex) {
            logger("Twitter Posting Exception: ", [$ex->getMessage()]);
        }


    }
}
