<?php

use App\Http\Controllers\OtpController;

Route::post('/generate-otp', [OtpController::class, 'generate']);
Route::post('/verify-otp', [OtpController::class, 'verify']);