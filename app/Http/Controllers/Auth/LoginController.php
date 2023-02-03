<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if (!$token) {
            return false;
        }

        //Obtener el usuario autenticado
        $user = $this->guard()->user();
//        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
//            return false;
//        }
        //Pasar token al usuario
        $this->guard()->setToken($token);
        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'roles' => auth()->user()->getRoleNames(),
            'user' => auth()->user(),
            'token_type' => 'bearer',
            'expiration_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        throw ValidationException::withMessages([
            $this->username() => 'Credenciales de acceso incorrectas.'
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response()->json(['message' => '¡Ha cerrado la sesión correctamente!']);
    }

    public function user(Request $request)
    {
        if (auth()->check()) {
            $user = User::where('id',auth()->user()->id)
                ->first();
            return response()->json(['user' => $user], 200);
        }
        return response()->json(null, 200);
    }
}
