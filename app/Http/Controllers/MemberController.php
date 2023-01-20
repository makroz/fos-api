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

        if (Auth::guard('member')->attempt(['icn' => $icn, 'password' => $password])) {
            $user             = Auth::guard('member')->user();
            if (empty($user->register_date)) {
                $user->register_date = date('Y-m-d H:i:s');
                $user->save();
                // $challenges = Challenge::where('status', 1)->where('level_id', $user->level_id)->orderBy('position', 'asc')->get();
                // $separation = 1;
                // $dateBase = date('Y-m-d', strtotime($user->register_date));
                // foreach ($challenges as $challenge) {

                //     for ($i = 0; $i < $challenge->repeat; $i++) {
                //         $dateBase = date('Y-m-d', strtotime($dateBase));
                //         $dateBase .= ' ' . date('H:i:s', strtotime($challenge->time_begin));
                //         $dateBase = date('Y-m-d H:i:s', strtotime('+' . $separation . ' days', strtotime($dateBase)));
                //         Task::create([
                //             'member_id' => $user->id,
                //             'challenge_id' => $challenge->id,
                //             'to_date' => $dateBase,
                //             'lavel_id' => $user->level_id,
                //         ]);
                //         $separation = $challenge->separation;
                //     }
                // }
            }
            $success['token'] = $user->createToken(env('APP_KEY', 'Fos'))->plainTextToken;
            $success['user']  = $user;
            return $this->sendResponse($success, 'Login member successfull');
        }
        // La autenticación ha fallado
        return $this->sendError('Incorrect Access', ['email' => 'Incorrect Credentials'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard('member')->logout();

        return $this->sendResponse('Session Member close successfull');
    }

    public function guard()
    {
        return Auth::guard('member');
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
        $challenges = Challenge::where('status', 'A')->where('level_id', $data->level_id)->orderBy('position', 'asc')->get();
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
                    'lavel_id' => $challenge->level_id,
                ]);
                $separation = $challenge->separation;
            }
        }
    }
}
