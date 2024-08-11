<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    use ApiResponseTrait;
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->apiResponse(null, 'Email already verified.', 200);
        } else {
            $request->user()->sendEmailVerificationNotification();
        }

        return $this->apiResponse(null, 'Verification link sent on your email.', 200);
    }

    public function verifyEmail(Request $request)
    {
        try {

            $user = User::find($request->route('id'));

            if ($user->hasVerifiedEmail()) {
                return $this->apiResponse(null, 'Email already verified.', 200);
            }
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }


            return $this->apiResponse(null, 'Email verified successfully.', 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->apiResponse(null, 'Something went wrong.', 500);
        }
    }
}
