<?php

namespace App\Http\Controllers\Api;

use App\Contracts\IdentityVerificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProcessIdentityWebhookController extends Controller
{

    protected $identityService;

    public function __construct(IdentityVerificationService $identityService) {
        $this->identityService = $identityService;
    }
    public function process(Request $request) {

        logger("ID WH => ", [$request->all()]);

        if($request->verification_order['type'] != 'photo_id')
            return response()->json([
                'status' => 'success',
            ], 200);


        $status = $request->verification_order['verification']['status'];
        $uuid = $request->verification_order['verification']['uuid'];
        try {
            switch ($status) {
                case 'approved':
                    $this->identityService->setApprove($uuid);
                    break;
                case 'rejected':
                case 'cancelled':
                    $this->identityService->setRejected($uuid);
                    break;

                default:
                    $this->identityService->setPending($uuid);
                    break;
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
