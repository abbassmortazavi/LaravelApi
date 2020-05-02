<?php

namespace App\Http\Controllers\Api\v2;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login' , 'register' , 'checkUserExpireTime']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        //dreturn $credentials;
        $remmeber_me = request('remember_me');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // if($remmeber_me){
        //     $token->expires_in = Carbon::now()->addMinute(1);
        // }
        return $this->respondWithToken($token , $remmeber_me);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token , $remmeber_me)
    {
        $expires_in = '';
        $mytime = Carbon::now();

        if ($remmeber_me == true){
            $expires_in = Carbon::parse($mytime->now()->addHours(5))->toDateTimeString();
        }else{
            $expires_in = Carbon::parse($mytime->now()->addMinutes(1))->toDateTimeString();
        }
        //auth('api')->factory()->getTTL() * 60;
        return response()->json([
            'access_token' => $token,
            'user'=> $this->gaurd()->user(),
            'token_type' => 'bearer',
            'expires_in' => $expires_in
        ]);
    }

    public function gaurd()
    {
        return Auth::guard('api');
    }

    public function register(Request $request)
    {
       $this->validate($request , [
           'email'=>'required',
           'password'=>'required|min:4'
       ]);
        $email = $request->email;
        $password = $request->password;
        $name = $request->name;
        $passwordConfirm = $request->passwordConfirm;
        $user = User::whereEmail($email)->first();
        if($user){
            return response()->json([
              'messages'=> "$email exists"
            ]);
        }
        if ($passwordConfirm != $password){
            return response()->json([
                'messages'=> "Password not match!!"
              ]);
        }

        $input['password'] = bcrypt($password);
        $input['email'] = $email;
        $input['name'] = $name;
        //dd($input);
        User::create($input);
        return response()->json([
            'messages'=> "User Created SuccessFully!!"
          ]);

    }

    public function checkUserExpireTime(Request $request)
    {
        $expire_in = $request->expire_in;
   
        $mytime = Carbon::now();
        if ($expire_in < $mytime){
            return response()->json([
                'expire'=> false
              ]);
        }
        return response()->json([
            'expire'=> true
          ]);
    }

}
