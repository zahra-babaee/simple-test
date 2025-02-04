<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class OtpService
{
    public function generateOtp($phone)
    {
        // بررسی وجود کلید OTP قبلی
        $exists = Redis::exists("otp:$phone");
        $ttl = Redis::ttl("otp:$phone");

        if ($exists) {
            return [
                'success' => false,
                'message' => 'کد قبلی هنوز معتبر است. لطفاً از آن استفاده کنید.',
                'ttl' => $ttl,
            ];
        }

        // تولید OTP
        $otp = mt_rand(1000, 9999);

        // ذخیره OTP در Redis با TTL 5 دقیقه
        Redis::set("otp:$phone", $otp, 'EX', 300);

        return [
            'success' => true,
            'message' => 'کد OTP با موفقیت ارسال شد.',
            'otp' => $otp,
        ];
    }

    public function verifyOtp($phone, $otp)
    {
        // بازیابی OTP از Redis
        $storedOtp = Redis::get("otp:$phone");

        if (!$storedOtp) {
            return [
                'success' => false,
                'message' => 'کد وارد شده منقضی شده است.',
            ];
        }

        if ($storedOtp === $otp) {
            // حذف OTP از Redis
            Redis::del("otp:$phone");

            return [
                'success' => true,
                'message' => 'کد تایید شد.',
            ];
        }

        return [
            'success' => false,
            'message' => 'کد وارد شده اشتباه است.',
        ];
    }
}
