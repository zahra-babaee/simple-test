<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\Newuser;
use Illuminate\Support\Str;


class OtpController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
        ]);

        $phone = $request->phone;
        $otp = Str::random(6); // یا mt_rand(100000, 999999) برای اعداد

        // ذخیره در ردیس با زمان انقضا
        Redis::set("otp:$phone", $otp, 'EX', 300); // انقضا ۵ دقیقه

        return response()->json([
            'success' => true,
            'otp' => $otp,
        ]);
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|string',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        // بررسی کد در ردیس
        $storedOtp = Redis::get("otp:$phone");

        if ($storedOtp && $storedOtp === $otp) {
            // ثبت شماره در پایگاه داده
            $user = Newuser::updateOrCreate(['phone' => $phone]);

            // حذف کد از ردیس
            Redis::del("otp:$phone");

            return response()->json([
                'success' => true,
                'message' => 'کاربر با موفقیت ثبت شد.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'کد وارد شده اشتباه است.',
        ]);
    }
}
