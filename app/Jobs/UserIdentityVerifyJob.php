<?php

namespace App\Jobs;

use App\IdentityVerification;
use App\Model\UserVerify;
use App\Services\BlueCheckService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserIdentityVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $userVerify;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, UserVerify $userVerify)
    {
        $this->user = $user;
        $this->userVerify = $userVerify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $identityVerifyService = new BlueCheckService();

        $response = $identityVerifyService->verify($this->user);

        logger("UUID => ", [$response]);

        $identityVerification = IdentityVerification::where('user_id', $this->user->id)->first();

        $res = $identityVerifyService->uploadFiles($identityVerification, $this->userVerify);

        logger("UPLOAD => ", [$res]);
    }
}
