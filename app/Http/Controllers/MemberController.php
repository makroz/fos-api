<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Challenge;
use Illuminate\Http\Request;
use App\Models\Member as User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public $_guard = 'member';
    public function username()
    {
        return "icn";
    }

    public function login(Request $request)
    {
        $icn = $request->input('email');
        $password = $request->input('password');
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:15',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        if (Auth::guard($this->_guard)->attempt(['icn' => $icn, 'password' => $password])) {
            $user = Auth::guard('member')->user();
            if (empty($user->register_date)) {
                $user->register_date = date('Y-m-d H:i:s');
                $user->save();
            }
            $success['token'] = $user->createToken($_SERVER['HTTP_USER_AGENT'] . '-' . $_SERVER['REMOTE_ADDR'])->plainTextToken;
            // $user->role;
            $user->sponsor;
            $success['user']  = $user;
            return $this->sendResponse($success, 'Login member successfull');
        }

        return $this->sendError('Incorrect Access', ['email' => 'Incorrect Credentials'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard($this->_guard)->logout();

        return $this->sendResponse('Session Member close successfull');
    }

    public function iam(Request $request)
    {
        $user = Auth::guard(    )->user();
        //$user->role;
        $user->sponsor;
        $success['user']  = $user;
        return $this->sendResponse($success, 'member details');
    }

    public function guard()
    {
        return Auth::guard($this->_guard);
    }

    public function beforeCreate(Request $request)
    {
        $input = $request->all();
        $pin = bcrypt(substr($request->input('icn'), 0, 4));
        $input['pin'] = $pin;
        $input['password'] = $pin;
        $input['name'] = strtolower($input['name']);
        $input['sponsor_id'] = Auth::user()->id;
        return $input;
    }

    public function afterCreate(Request $request, $data, $input)
    {
        $data->refresh();
        $challenges = Challenge::where('status', 'A')->where('level_id', 1)->orderBy('position', 'asc')->get();
        $separation = 0;
        //obtener la fecha del siguiente lunes a partir de hoy
        $dateBase = date('Y-m-d', strtotime('next monday'));
        foreach ($challenges as $challenge) {

            for ($i = 0; $i < $challenge->repeat; $i++) {
                $dateBase = date('Y-m-d', strtotime($dateBase));
                $dateBase .= ' ' . date('H:i:s', strtotime($challenge->time_begin));
                $dateBase = date('Y-m-d H:i:s', strtotime('+' . $separation . ' days', strtotime($dateBase)));
                Task::create([
                    'member_id' => $data->id,
                    'challenge_id' => $challenge->id,
                    'to_date' => $dateBase,
                    'level_id' => 1,
                    'type' => $challenge->type,
                ]);
                $separation = $challenge->separation;
            }
        }
    }

    public function beforeList(Request $request, $query)
    {
        $query->where('sponsor_id', Auth::user()->id);
        return $query;
    }
}
