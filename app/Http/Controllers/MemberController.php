<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member as User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icn' => 'required|string|max:15|unique:members,icn',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $name = $request->input('name');
        $icn = $request->input('icn');
        $password = $request->input('password');

        $user = new User;
        $user->name = $name;
        $user->icn = $icn;
        $user->password = bcrypt($password);
        $user->pin =  $user->password;
        $user->save();

        $success['token'] = $user->createToken(env('APP_KEY', 'Fos'))->plainTextToken;
        //$success['user']  = $user;
        $success['status']  = 'ok';

        return $this->sendResponse($success, 'Member Register Succesfull');
    }

    public function login(Request $request)
    {
        $icn = $request->input('icn');
        $password = $request->input('password');

        if (Auth::guard('member')->attempt(['icn' => $icn, 'pin' => $password])) {
            $user             = Auth::guard('member')->user();
            $success['token'] = $user->createToken(env('APP_KEY', 'Fos'))->plainTextToken;
            $success['user']  = $user;
            $success['status']  = 'ok';
            echo Auth;

            // Devuelve el token al cliente
            return $this->sendResponse($success, 'Login member successfull');
        }
        // La autenticación ha fallado
        return $this->sendError('Incorrect Access', null, 400);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard()->logout();

        // Devuelve una respuesta de éxito al cliente
        return $this->sendResponse('Session Member close successfull');
    }

    public function guard()
    {
        return Auth::guard('member');
    }
}
