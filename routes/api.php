<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LiveController;
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
    });
});

// Rutas members
Route::controller(MemberController::class)->group(function () {
    Route::post('member-register', 'register');
    Route::post('member-login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('member-logout', 'logout');
        Route::post('member-iam', 'iam');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'levels' => LevelController::class,
        'roles' => RoleController::class,
        'abilities' => AbilityController::class,
        'challenges' => ChallengeController::class,
        'tasks' => TaskController::class,
        'lives' => LiveController::class,
        'members' => MemberController::class,
        'users' => UserController::class,
    ]);

    Route::controller(TaskController::class)->group(function () {
        Route::post('tasks-begin/{id}', 'beginTask');
        Route::post('tasks-end/{id}', 'endTask');
    });

    Route::controller(LevelController::class)->group(function () {
        Route::post('levels/listData', 'listData');
    });
    Route::controller(LiveController::class)->group(function () {
        Route::get('tasks-today', 'tasksToday');
        Route::post('lives-open/{id}', 'meetLive');
        Route::post('lives-close/{id}', 'closeLive');
    });
});
