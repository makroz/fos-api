<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AbilityController;
use App\Http\Controllers\ChallengeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Rutas usuarios
Route::controller(UserController::class)->group(function () {
    Route::post('admin-register', 'register');
    Route::post('admin-login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('admin-logout', 'logout');
        Route::get('users', 'index');
        Route::get('users/{id}', 'show');
        Route::put('users/{id}', 'update');
        Route::delete('users/{id}', 'destroy');
    });
});

// Rutas members
Route::controller(MemberController::class)->group(function () {
    Route::post('member-register', 'register');
    Route::post('member-login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('member-logout', 'logout');
        Route::get('members', 'index');
        Route::get('members/{id}', 'show');
        Route::put('members/{id}', 'update');
        Route::delete('members/{id}', 'destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'levels' => LevelController::class,
        'roles' => RoleController::class,
        'abilities' => AbilityController::class,
        'challenges' => ChallengeController::class,
        'tasks' => TaskController::class,
        'members' => MemberController::class,
    ]);
    Route::controller(LevelController::class)->group(function () {
        Route::post('levels/listData', 'listData');
    });
});
