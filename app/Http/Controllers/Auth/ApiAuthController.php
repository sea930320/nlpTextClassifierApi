<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Events\JwtLogin;

class ApiAuthController extends LoginController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * ApiLoginController constructor.
     *
     * @param User $user
     * @param JWTAuth $jwtAuth
     */
    public function __construct(User $user, JWTAuth $jwtAuth)
    {
        parent::__construct();
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $this->jwtAuth->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Failed to create token'
            ], 401);
        }

        event(new JwtLogin(auth()->user()));
        return response()->json([
            'response' => 'success',
            'token' => $token
        ]);
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        //$request->password = Hash::make($request->password);
        //$user = $this->user->create($request->validatedOnly());
        $user = $this->user->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        event(new UserRegistered($user));
        $token = $this->jwtAuth->fromUser($user);

        $this->guard()->login($user);

        return response()->json([
            'message' => 'Welcome to HumanDetection!',
            'token' => $token,
            'id' => $user->id
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = $this->jwtAuth->parseToken()->refresh();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->jwtAuth->parseToken()->invalidate();
        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser()
    {
        $user = $this->jwtAuth->toUser();

        return response()->json(['result' => $user]);
    }
}
