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
            //si es un array
            if (is_array($message)) {
                foreach ($message as $key => $value) {
                    $response[$key] = $value;
                }
            } else {
                $response['message'] = $message;
            }
        }

        if (!empty($result)) {
            $response['data'] = $result;
        }
        $response['Debug_Querys'] = DB::getQueryLog();
        return response()->json($response, $code);
    }

    public function sendError($message, $errors = [], $code = 404)
    {
        $response = [
            'success' => false,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        $response['Debug_Querys'] = DB::getQueryLog();

        return response()->json($response, $code);
    }

    public function search($model, $busquedas, &$inicio, $fin)
    {
        $join = '';
        for ($i = $inicio; $i < $fin; $i++) {
            if ($i > 0) {
                $join = explode(',', $busquedas[$i - 1] . ',,,,,,')[3];
            }
            $busqueda = $busquedas[$i];
            $busqueda = explode(',', $busqueda . ',,,,,,');
            if (empty($busqueda)) {
                continue;
            }

            if ($busqueda[4] != '' && $i > $inicio) {
                if ($join == '' || $join == 'a') {
                    $model = $model->where(function ($query) use ($busquedas, &$i, $fin) {
                        $query = $this->search($query, $busquedas, $i, $fin);
                        $i++;
                    });
                } else {
                    $model = $model->orWhere(function ($query) use ($busquedas, &$i, $fin) {
                        $query = $this->search($query, $busquedas, $i, $fin);
                        $i++;
                    });
                }
                $inicio = $i;
                return $model;
            }

            $busqueda = $busquedas[$i];
            if (empty($busqueda)) {
                continue;
            }
            if ($i > 0) {
                $join = explode(',', $busquedas[$i - 1] . ',,,,,,')[3];
            }
            if ($i < $fin) {
                $busqueda = explode(',', $busqueda . ',,,,,,');
                if ($busqueda[1] == 'l') {
                    $busqueda[1] = 'like';
                    $busqueda[2] = '%' . str_replace('%', '', $busqueda[2]) . '%';
                }
                if ($busqueda[1] == '!l') {
                    $busqueda[1] = 'not like';
                    $busqueda[2] = '%' . str_replace('%', '', $busqueda[2]) . '%';
                }
                if ($busqueda[1] == 'le') {
                    $busqueda[1] = 'like';
                    $busqueda[2] = '%' . str_replace('%', '', $busqueda[2]);
                }
                if ($busqueda[1] == 'lb') {
                    $busqueda[1] = 'like';
                    $busqueda[2] = str_replace('%', '', $busqueda[2]) . '%';
                }
            }
            if ($join == '' || $join == 'a') {
                $model = $model->where($busqueda[0], $busqueda[1], $busqueda[2]);
            } else {
                $model = $model->orWhere($busqueda[0], $busqueda[1], $busqueda[2]);
            }

            if ($busqueda[5] == '1') {
                $inicio = $i;
                return $model;
            }
        }
        $inicio = $i;
        return $model;
    }
    public function index(Request $request)
    {
        $page     = self::getParam('page', 1,);
        $perPage  = self::getParam('perPage', 5);
        $sortBy   = self::getParam('sortBy', 'id');
        $order    = self::getParam('orderBy', 'asc');
        $buscar   = self::getParam('searchBy', '');
        $recycled = $request->recycled;
        $disabled = $request->disabled;
        $cols     = ['*'];

        if ($request->cols) {
            $cols = explode(',', $request->cols);
        }
        $model = new $this->__modelo();
        if (!empty($model->relations)) {
            $model->with($model->relations);
        }

        if ($request->relations) {
            $rel = explode(',', $request->relations);
            $model = $model->with($rel);
        }
        $model = $model->select($cols)->orderBy($sortBy, $order);
        $model = $this->beforeList($request, $model);
        if (!empty($buscar)) {
            $busquedas = explode('|', urldecode($buscar) . '|');
            $i = 0;
            $model = $model->where(function ($query) use ($busquedas, &$i) {
                $query = $this->search($query, $busquedas, $i, count($busquedas));
                return  $query;
            });
            $i++;
            if ($i <= count($busquedas)) {
                $model = $this->search($model, $busquedas, $i, count($busquedas));
            }
        }
        $total = $model->count();
        if ($perPage > 0) {
            $model = $model->offset(($page - 1) * $perPage)->limit($perPage);
        }
        $data = $model->get();
        return $this->sendResponse($data, ['total' => $total]);
    }

    public function beforeList(Request $request, $model)
    {
        return $model;
    }

    public function beforeCreate(Request $request)
    {
        return $request->all();
    }

    public function afterCreate(Request $request, $data, $input)
    {
        return true;
    }

    public function beforeUpdate(Request $request, $id)
    {
        return $request->all();
    }

    public function afterUpdate(Request $request, $data, $input)
    {
        return true;
    }

    public function beforeDelete(Request $request, $id)
    {
        return $request->all();
    }

    public function afterDelete(Request $request, $data, $input)
    {
        return true;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $this->beforeCreate($request);
            $data = $this->__modelo::create($input);
            $this->afterCreate($request, $data, $input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($data->id, 'Registro creado con exito');
    }

    public function show(Request $request, $id)
    {
        $data = $this->__modelo::find($id);
        return $this->sendResponse($data, "Show $id $this->__modelo");
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $input = $this->beforeUpdate($request, $id);
            $data = $this->__modelo::where('id', $id)->update($input);
            $this->afterUpdate($request, $data, $input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($data, 'Registro actualizado con exito');
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $input = $this->beforeDelete($request, $id);
            $data = $this->__modelo::where('id', $id)->delete();
            $this->afterDelete($request, $data, $input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }

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
