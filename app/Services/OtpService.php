<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class OtpService
{
    public function sendOtp($mobile, $otp)
    {
        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_AUTH_TOKEN');
        $phone  = env('TWILIO_PHONE');

        try {
            $client = new Client($sid, $token);

            $client->messages->create(
                "+91" . $mobile,
                [
                    'from' => $phone,
                    'body' => "FINVELS : Your OTP for verification is: $otp"
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Twilio OTP Error: ' . $e->getMessage());
            return false;
        }
    }
}
