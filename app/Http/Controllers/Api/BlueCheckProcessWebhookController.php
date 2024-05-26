<?php

namespace App\Http\Controllers\Api;

use App\Contracts\IdentityVerificationService;
use App\Http\Controllers\Controller;
use App\Services\BlueCheckService;
use App\User;
use Illuminate\Http\Request;

class BlueCheckProcessWebhookController extends Controller
{

    protected $identityService;

    public function __construct(IdentityVerificationService $identityService) {
        $this->identityService = $identityService;
    }

    public function process(Request $request) {

        try {

            $service = $this->identityService->verify(User::first());
            return $service;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
