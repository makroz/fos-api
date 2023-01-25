<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public $_guard = 'api';

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::guard($this->_guard)->attempt(['email' => $email, 'password' => $password])) {
            $user             = Auth::guard($this->_guard)->user();
            $success['token'] = $user->createToken($_SERVER['HTTP_USER_AGENT'] . '-' . $_SERVER['REMOTE_ADDR'])->plainTextToken;
            $user->role;
            $success['user']  = $user;
            $success['status']  = 'ok';
            return $this->sendResponse($success, 'Login successfull');
        }
        return $this->sendError('Incorrect Access', ['email' => 'Credentials Invalid'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard($this->_guard)->logout();

        // Devuelve una respuesta de éxito al cliente
        return $this->sendResponse('Session close successfull');
    }

    public function guard()
    {
        return Auth::guard($this->_guard);
    }
}
