<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function beforeCreate(Request $request)
    {
        $input = $request->all();
        $input['name'] = strtolower($input['name']);
        return $input;
    }
}
