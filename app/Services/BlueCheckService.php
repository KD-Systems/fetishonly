<?php

namespace App\Services;

use App\Contracts\IdentityVerificationService;
use App\IdentityVerification;
use App\IdentityVerificationLog;
use App\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BlueCheckService implements IdentityVerificationService {

    protected $notification_url;
    protected $return_url;

    public function __construct()
    {
        $this->notification_url = route('identity-verify-webhook');
        // $this->notification_url = 'https://webhook.site/0d7a4023-7b84-4c67-9573-690dd8753beb';
        $this->return_url = route('identity-submitted');
    }

    public function verify(User $user) {

        $verification = IdentityVerification::where("user_id", $user->id)->first();

        if($verification) {
            return [
                'public_url' => $verification->public_url,
                'qr_code' => $verification->qr_code
            ];
        }

        $body =  '{
            "external_id": "",
            "notification_url": "",
            "return_url": "https://example.com/verification/return",
            "type": "photo_id",
            "data": {
              "first_name": "",
              "last_name": "",
              "email": "",
              "date_of_birth": "",
              "phone": "",
              "address": {
                "country": "",
                "district": "",
                "postal_code": "",
                "city": "",
                "line_1": ""
              }
            },
            "config": {
              "max_attempts": '.config('bluecheck.max_attempts').',
              "checkups": [
                "name",
                "address"
              ],
              "nsfw": {
                "threshold": '.config('bluecheck.threshold').'
              },
              "document_liveness": {
                "threshold": '.config('bluecheck.threshold').'
              },
              "face_detection": {
                "min_faces": 2
              },
              "face_comparison": {
                "min_face_matches": 2
              }
            },
            "ttl": 60
          }';

          $body = json_decode($body, true);
          $name = explode(" ", $user->name);
          $body['external_id'] = (string) ($user->id);
          $body['notification_url'] = $this->notification_url;
          $body['return_url'] = $this->return_url;
          $body['data']['first_name'] = $name[0];
          $body['data']['last_name'] = $name[1] ?? $name[0];
          $body['data']['email'] = $user->email ?? '';
          $body['data']['phone'] = $user->phone ?? (string) '+'.rand(1111111111, 9999999999);
          $body['data']['date_of_birth'] = date('Y-m-d', strtotime($user->birthdate)) ?? '';
          $body['data']['address']['country'] = 'US';
          $body['data']['address']['district'] = $user->state ?? '';
          $body['data']['address']['postal_code'] = $user->postcode ?? '';
          $body['data']['address']['city'] = $user->city ?? '';
          $body['data']['address']['line_1'] = (string) $user->location ?? $user->billing_address ?? '';


        try {

            $headers = [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('bluecheck.access_token')
              ];

            $response = Http::withHeaders($headers)->post(config('bluecheck.base_url').'/verification', $body)->json();

            logger("RESPONSE: ",[$response]);

            DB::beginTransaction();

            $verification = IdentityVerification::create([
                'user_id'       => $user->id,
                'uuid'          => $response['verification_order']['uuid'],
                'public_url'    => $response['verification_order']['public_url'],
                'qr_code'       => $response['verification_order']['qr_code_src'],
                'status'        => $response['verification_order']['verification']['status']
            ]);

            IdentityVerificationLog::create([
                'identity_verification_id' => $verification->id,
                'status' => $response['verification_order']['status'],
                'verification_status' => $response['verification_order']['verification']['status'],
                'response' => json_encode($response['verification_order'], true)
            ]);

            DB::commit();

            return [
                'public_url' => $verification->public_url,
                'qr_code' => $verification->qr_code
            ];

        } catch (Exception $th) {
            DB::rollBack();
            throw $th;
        }

    }

    public function getStatus(IdentityVerification $verification){
        $headers = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('bluecheck.access_token')
          ];

        try {

            $response = Http::withHeaders($headers)->get(config('bluecheck.base_url').'/verification/'.$verification->uuid)->json();
            return $response;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setApproved($uuid) {
        $verification = IdentityVerification::where('uuid', $uuid)->first();

        if(!$verification) {
            return;
        }

        try {
            $getStatus = $this->getStatus($verification);

            if($getStatus['verification_order']['status'] == 'completed' && $getStatus['verification_order']['verification']['status'] == 'approved') {

                DB::beginTransaction();
                $verification->update([
                    'status' => 'approved'
                ]);
                $this->updateLog($verification, $getStatus);
                DB::commit();

            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return;
    }

    public function setRejected($uuid) {
        $verification = IdentityVerification::where('uuid', $uuid)->first();

        if(!$verification) {
            return;
        }

        try {
            DB::beginTransaction();
            $getStatus = $this->getStatus($verification);

            if($getStatus['verification_order']['status'] == 'completed' && $getStatus['verification_order']['verification']['status'] =='rejected') {
                $verification->update([
                    'status' =>'rejected',
                    'reason' => $getStatus['verification_order']['verification']['reason'] ?? null
                ]);
            }

            $this->updateLog($verification, $getStatus);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return;

    }


    public function setPending($uuid) {
        $verification = IdentityVerification::where('uuid', $uuid)->first();

        if(!$verification) {
            return;
        }

        try {
            $getStatus = $this->getStatus($verification);

            $this->updateLog($verification, $getStatus);
        } catch (\Throwable $th) {
            throw $th;
        }

        return;
    }


    public function updateLog(IdentityVerification $identityVerification, $response) {

        try {
            DB::beginTransaction();
            IdentityVerificationLog::create([
                'identity_verification_id' => $identityVerification->id,
                'status' => $response['verification_order']['status'],
                'verification_status' => $response['verification_order']['verification']['status'],
                'response' => json_encode($response['verification_order']),
                'reason' => $response['verification_order']['verification']['reason'] ?? null,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

    public function uploadFiles(IdentityVerification $verification){

    }
}
