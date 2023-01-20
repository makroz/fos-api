<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbilityController extends Controller
{
    //beforeCreate
    public function beforeCreate(Request $request)
    {
        $input = $request->all();
        $input['name'] = strtolower($input['name']);
        return $input;
    }
}
