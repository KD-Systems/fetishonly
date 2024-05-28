<?php

namespace App\Http\Controllers;

use App\Contracts\IdentityVerificationService;
use Illuminate\Http\Request;

class IdentityVerifyController extends Controller
{
    protected $identityVerificationService;

    public function __construct(IdentityVerificationService $identityVerificationService)
    {
        $this->identityVerificationService = $identityVerificationService;
    }


    public function create() {
        $user = auth()->user();

        try {
            $response = $this->identityVerificationService->verify($user);
            return redirect()->to($response['public_url']);
        } catch (\Exception $exception) {
            //throw $th;
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }

    }

}
