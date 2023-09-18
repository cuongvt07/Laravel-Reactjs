<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;
use JWT;

class AuthController extends Controller
{


    /**
     * @param Request $request
     * @param Application $app
     */
    public function __construct(
        Request     $request,
        Application $app
    )
    {
        $this->responses = new stdClass();
    }
    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function register(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'headerCode' => 400,
                'message' => $validator->errors()->toJson(),
            ];
        }
        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'status' => 'active'
            ]
        ));
        $token = $user->createToken($user->email.'_Token')->plainTextToken;

        return [
            'status' => 200,
            'user' => $user,
            'token' => $token,
            'message' => 'Success'
        ];
    }

    /**
     * @param Request $request
     * @return array|Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'headerCode' => 401,
                    'message' => 'Unauthorized',
                ];
            }
            $user =Auth::user();
            $token = $this->createNewToken($token);

            return response([
                'user' => $user,
                'token' => $token
            ]);
        } catch (ValidationException $e) {
            return [
                'status' => false,
                'headerCode' => 422,
                'message' => $e,
            ];
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     */
    public function logout()
    {
        $user = Auth::user();
        $user = currentAccessToken()->delete();
        auth()->logout();
        return response([
            'success' => true
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     */
    protected function createNewToken(string $token)
    {
        $data['token'] = $token;
        $data['token_type'] = 'bearer';
        $data['expires_in'] = JWTAuth::factory()->getTTL() * 60;
        $data['user'] = auth()->user();
        return $this->apiResponse('success', $data);
    }
}
