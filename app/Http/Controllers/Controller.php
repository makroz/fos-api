<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public $__modelo = '';

    public static $ses;
    public static $timeSession = 86400;

    public static $request;
    public static function getSession($name, $default = '')
    {
        $user = Auth::user();
        $r = '';
        if (!$user) {
            //Mk_debug::warning(Session::get($name, $default), 'Sesion', 'GetToken*'.$token);
            $r = Session::get($name, $default);
        } else {
            $r = Cache::remember("{$user->uuid}.{$name}", self::$timeSession, function () use ($default) {
                return $default;
            });
        }
        //Mk_debug::warning($r, 'Sesion', 'Get*'.$token);
        return $r;
    }


    public static function setSession($name, $value = '', $time = '')
    {
        if ($time == '') {
            $time = self::$timeSession;
        }

        $user = Auth::user();
        if (!$user) {
            Session::put($name, $value, $time);
        } else {
            Cache::put("{$user->uuid}.{$name}", $value, self::$timeSession);
        }
        //Mk_debug::warning("$name, $value", 'Sesion', 'Set*'.$token);
        return true;
    }
    public static function getParam($name, $default = '', $refresh = false)
    {
        //$request = new Request();
        $clase = self::$request->route()->getAction();
        $clase = basename($clase['controller']);
        $clase = explode('Controller@', $clase);
        $clase = $clase[0];
        $ruta = "params.{$clase}.{$name}";
        if (self::$request->has($name)) {
            $param = self::$request->input($name);
            self::setSession($ruta, $param);
        } else {
            if ($refresh) {
                $param = $default;
                self::setSession($ruta, $param);
            } else {
                $param = self::getSession($ruta, $default);
            }
        }
        return $param;
    }

    public function __construct(Request $request)
    {
        self::$request = $request;
        DB::connection()->enableQueryLog();
        if ($this->__modelo == '') {
            $this->__modelo = $this->getNameModel($this);
        }
        return true;
    }

    public function sendResponse($result, $message = '', $code = 200)
    {
        $response = [
            'success' => true,
        ];
        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($result)) {
            $response['data'] = $result;
        }
        $response['Debug_Querys'] = DB::getQueryLog();
        return response()->json($response, $code);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'Debug_Querys' => DB::getQueryLog()
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function index(Request $request)
    {
        $page     = self::getParam('page', 1,);
        $perPage  = self::getParam('perPage', 5);
        $sortBy   = self::getParam('sortBy', 'id');
        $order    = self::getParam('orderBy', 'asc');
        $buscar   = self::getParam('searchBy', '');
        $recycled = $request->recycled;
        $cols     = $request->cols ?? ['*'];
        $disabled = $request->disabled;
        $model = new $this->__modelo();
        if (!empty($model->relations)) {
            $model->with($model->relations);
        }
        if (!empty($buscar)) {
            $busqueda = explode(',', urldecode($buscar) . ',,');
            $model = $model->where($busqueda[0], $busqueda[1], $busqueda[2]);
        }
        $model = $model->select($cols)->orderBy($sortBy, $order);

        if ($perPage < 1) {
            $data = $model->get();
        } else {
            $data = $model->paginate($perPage, $cols, 'page', $page);
        }
        return $this->sendResponse($data);
    }


    public function store(Request $request)
    {
        $data = $this->__modelo::create($request->all());
        return $this->sendResponse($data, 'Registro creado con exito');
    }

    public function show(Request $request, $id)
    {
        $data = $this->__modelo::find($id);
        return $this->sendResponse($data, "Show $id $this->__modelo");
    }

    public function update(Request $request, $id)
    {
        $data = $this->__modelo::where('id', $id)->update($request->all());
        return $this->sendResponse($data, 'Registro actualizado con exito');
    }

    public function destroy($id)
    {
        $data = $this->__modelo::where('id', $id)->delete();
        return $this->sendResponse($data, 'Registro Eliminado con exito');
    }


    public function getNameModel($clase)
    {
        //echo  get_class($clase) . '***';
        $nameSpace = explode('\\', get_class($clase));
        //$nameSpace
        $model = array_pop($nameSpace);
        array_pop($nameSpace);
        array_pop($nameSpace);
        $nameSpace = join('\\', $nameSpace);
        $model = explode('Controller', $model);
        $model = $model[0];
        $xx = $nameSpace . '\Models\\' . $model;
        //$xx = str_replace('\\', '/', $xx);
        //echo $xx . '***';
        return $xx;
    }
}
