<?php

namespace App\Http\Controllers\Api;

use App\Events\RegisterUserEvent;
use App\Events\VerifyMailEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ReferralService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

// use Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;
    protected $ReferralService;

    public function __construct(ReferralService $ReferralService)
    {
        $this->middleware('jwt.authenticate', ['except' => ['login', 'register']]);
        $this->ReferralService = $ReferralService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }
        if (!$token = auth('api')->attempt($validator->validated())) {
            // return response()->json(['error' => 'Unauthorized'], 401);
            return $this->apiResponse(null, __('messages.unauthorized'), 401);
        }
        return $this->createNewToken($token);
    }
    public function register(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'age' => 'required|integer',
                'gender' => 'required|string|between:2,10',
                'job_title' => 'required|string|between:2,50',
                'category_id' => 'required|integer',
                'phone' => 'required|string|max:20',
                // 'username' => 'required|string|between:2,50|alpha_dash|unique:users',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|confirmed|min:8',
            ]);
            if ($validator->fails()) {
                DB::rollBack();

                return $this->apiResponse(null, $validator->errors()->first(), 400);
            }

            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));

            // add points to user who referred this user
            if ($request->parent_code) {
                $this->ReferralService->add($user, $request->parent_code, 30);
            }


            DB::commit();

            event(new VerifyMailEvent([$user->id]));

            // notify admins
            event(new RegisterUserEvent([], ['userName' => $user->name]));

            return $this->apiResponse(new UserResource($user), __('messages.registered'), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        auth('api')->logout();
        return $this->apiResponse(null, __('messages.logged_out'), 200);
    }

    public function refresh()
    {
        return $this->createNewToken(auth('api')->refresh());
    }

    public function userProfile()
    {
        return $this->apiResponse(new UserResource(auth('api')->user()), 'ok', 200);
    }


    public function updateProfile(Request $request)
    {

        try {
            $user = auth('api')->user();

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'email' => "required|string|email|max:100|unique:users,email,{$user->id}",
                'age' => 'required|integer',
                'gender' => 'required|string|between:2,100',
                'job_title' => 'required|string|between:2,100',
                'category_id' => 'required|integer',
                'phone' => 'sometimes|string|max:100',
                'country' => 'sometimes|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 400);
            }

            $user->update($validator->validated());

            return $this->apiResponse(new UserResource($user), __('messages.profile_updated'), 200);
        } catch (QueryException $e) {
            return $this->apiResponse(null, __('messages.error_occurred:') . $e->getMessage(), 500);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(null, __('messages.user_not_found'), 404);
        } catch (Exception $e) {
            return $this->apiResponse(null, __('messages.error_occurred:') . $e->getMessage(), 500);
        }
    }


    protected function createNewToken($token)
    {
        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth('api')->factory()->getTTL() * 60,
        //     'user' => new UserResource(auth('api')->user())
        // ]);

        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('api')->user())
        ];
        return $this->apiResponse($data, 'ok', 200);
    }

    // change password
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $user->update(['password' => bcrypt($data['password'])]);


        return $this->apiResponse(new UserResource($user), __('messages.password_updated'), 200);
    }

    // update picture profile
    public function updatePicture(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'picture' => 'required|image',
        ]);

        $user->update(['picture' => $request->picture]);

        return $this->apiResponse(new UserResource($user), __('messages.picture_updated'), 200);
    }
}
