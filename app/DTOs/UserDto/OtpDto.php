<?php

namespace App\DTOs\UserDto;

class OtpDto
{
    public $phone;
    public $otp;

    public function __construct($phone, $otp)
    {
        $this->phone = $phone;
        $this->otp = $otp;
    }
}
