<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->save();

        $success['token'] = $user->createToken(env('APP_KEY', 'Fos'))->plainTextToken;
        //$success['user']  = $user;
        $success['status']  = 'ok';

        return $this->sendResponse($success, 'User Register Succesfull');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::guard('api')->attempt(['email' => $email, 'password' => $password])) {
            $user             = Auth::guard('api')->user();
            $success['token'] = $user->createToken(env('APP_KEY', 'Fos'))->plainTextToken;
            $success['user']  = $user;
            $success['status']  = 'ok';


            // Devuelve el token al cliente
            return $this->sendResponse($success, 'Login successfull');
        }
        // La autenticación ha fallado
        return $this->sendError('Incorrect Access', null, 400);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard()->logout();

        // Devuelve una respuesta de éxito al cliente
        return $this->sendResponse('Session close successfull');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $user = $this->__modelo::where('id', $id)->update($request->all());
        return $this->sendResponse($user, 'Registro actualizado con exito');
    }

    public function guard()
    {
        return Auth::guard('api');
    }
}
