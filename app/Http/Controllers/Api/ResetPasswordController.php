<?php

namespace App\Http\Controllers\Api;

use App\Events\ResetPasswordEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    use ApiResponseTrait;

    public function requestPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->apiResponse(null, __('messages.user_not_found'), 400);
        } else {
            $token = app('auth.password.broker')->createToken($user);
            // $user->sendPasswordResetNotification($token);
            event(new ResetPasswordEvent([$user->id], ['token' => $token]));
        }

        return $this->apiResponse(null, __('messages.reset_link_sent'), 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !app('auth.password.broker')->tokenExists($user, $request->token)) {
            return $this->apiResponse(null, __('messages.invalid_token'), 400);
        }

        $user->update(['password' => bcrypt($request->password)]);

        // Optionally, delete the token after use
        app('auth.password.broker')->deleteToken($user);

        return $this->apiResponse(null, __('messages.password_reset_success'), 200);
    }
}
