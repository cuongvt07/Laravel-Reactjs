<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @var AuthRepository
     */
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }


    public function login(LoginRequest $request)
    {
        return $this->authRepository->login($request);
    }

    /**
     * @param LoginRequest $request
     * @return array
     * @throws ValidationException
     */
    public function register(LoginRequest $request): array
    {
        return $this->authRepository->register($request);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Application|ResponseFactory|Response
     */
    public function logout()
    {
        return $this->authRepository->logout();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Application|ResponseFactory|Response
     */
    public function refresh()
    {
        return $this->authRepository->refresh();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Application|ResponseFactory|Response
     */
    public function userProfile()
    {
        return $this->authRepository->userProfile();
    }

}
