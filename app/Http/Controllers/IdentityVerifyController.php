<?php

namespace App\Http\Controllers;

use App\Contracts\IdentityVerificationService;
use App\IdentityVerificationLog;
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


    public function showLogs($id) {
        $details = IdentityVerificationLog::where('identity_verification_id', $id)->orderBy('id')->get();

        return view('admin/identity-verification/details', compact('details'));
    }

}
