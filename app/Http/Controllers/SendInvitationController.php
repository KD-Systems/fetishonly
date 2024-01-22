<?php

namespace App\Http\Controllers;

use App\Mail\GenericEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendInvitationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sendInvitationEmail(Request $request) {

        $email = $request->email;
        $user = Auth::user();
        $text = "Hello, would like you to come join Fetishonly.com as a creator to share content. Click the link below";
        $link = route('register');
        $subject = 'Invitation to join as performer.';

        $data = [
            'subject' => $subject,
            'mailTitle' => $subject,
            'mailContent' => $text,
            'button' => [
                'url' => $link,
                'text' => 'Join with me'
            ],
            'mailQuote' => false,
            'replyTo' => 'noreplay@fetishonly.com'
        ];

        Mail::to($email)->send(new GenericEmail($data));

        return response()->json([
            'message' => 'success'
        ]);

    }
}
