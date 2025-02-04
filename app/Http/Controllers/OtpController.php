<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\Newuser;
use Illuminate\Support\Str;
use App\Services\OtpService;
use App\DTOs\UserDto\OtpDto;

class OtpController extends Controller
{
    protected $otpService;
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function generate(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
        ]);
        $phone = $request->phone;

        $otpDto = new OtpDto($phone, null);

        $result = $this->otpService->generateOtp($otpDto->phone);

        return response()->json($result);
    }
   
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:4',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $otpDto = new OtpDto($phone, $otp);

        $result = $this->otpService->verifyOtp($otpDto->phone, $otpDto->otp);

        if ($result['success']){
            $user = NewUser::create([
                'phone' => $otpDto->phone,
            ]);
        }
        return response()->json($result);
    }
}