<?php


namespace App\Repositories\Auth;

use App\Models\User;
use App\Traits\ApiResponseWithHttpSTatus;
use Illuminate\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use JWTAuth;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class AuthRepository
{
    use ApiResponseWithHttpSTatus;

    /**
     * $result payload Api
     * @var \stdClass
     */
    protected $responses;

    /**
     * Model Auth
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

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
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'headerCode' => 401,
                    'message' => 'Unauthorized',
                ];
            }
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return [
            'status' => 200,
            'user' => $user,
            'token' => $token,
            'message' => 'Success'
        ];
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
                'slug' => Str::slug($request->name),
                'status' => 'active'
            ]
        ));

        $token = $user->createToken($user->email.'_Token')->plainTextToken;
        $token = substr($token, strpos($token, '|') + 1);

        $user->token = $token;
        $user->save();

        return [
            'status' => 200,
            'user' => $user,
            'token' => $token,
            'message' => 'Success'
        ];
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return RedirectResponse
     */
    public function accountVerify($token, $email): RedirectResponse
    {
        $user = User::where([['email', Crypt::decryptString($email)], ['token', $token]])->first();
        if ($user->token == $token) {
            $user->update([
                'verify' => true,
                'token' => null
            ]);
            return redirect()->to('http://127.0.0.1:8000/verify/success');
        }
        return redirect()->to('http://127.0.0.1:8000/verify/invalid_token');
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     */
    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     */
    public function userProfile()
    {
        return $this->apiResponse('Sign out success', $data = auth()->user(), Response::HTTP_OK, true);
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

    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $token = Str::random(15);
            $details = ['name' => $user->name, 'token' => $token, 'email' => $user->email, 'hashEmail' => Crypt::encryptString($user->email)];
            if (dispatch(new PasswordResetJob($details))) {
                DB::table('password_resets')->insert([
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => now()
                ]);
                return $this->apiResponse('Password reset link has been sent to your email address', null, Response::HTTP_OK, true);
            }
        } else {
            return $this->apiResponse('invalid email', null, Response::HTTP_OK, true);
        }

    }


    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $email = Crypt::decryptString($request->email);
        $user = DB::table('password_resets')->where([['email', $email], ['token', $request->token]])->first();
        if (!$user) {
            return $this->apiResponse('Invalid email address or token', null, Response::HTTP_OK, true);
        } else {
            $data = User::where('email', $email)->first();
            $data->update([
                'password' => Hash::make($request->password)
            ]);
            DB::table('password_resets')->where('email', $email)->delete();
            return $this->apiResponse('Password updated !', null, Response::HTTP_OK, true);
        }
    }
}
