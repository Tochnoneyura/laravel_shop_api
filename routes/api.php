<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [UserController::class, 'create'])->name('api.auth.register');

});

Route::group([

    'middleware' => 'jwt.auth',

], function ($router) {

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('api.auth.me');
    
    

});

Route::group([

    'middleware' => 'jwt.auth',
    'prefix' => 'users'

], function ($router) {

    Route::post('/update/{id}', [UserController::class, 'update'])->name('api.users.update');
    Route::post('/delete/{id}', [UserController::class, 'delete'])->name('api.users.delete');
});


Route::group([

    'middleware' => 'auth.custom', 'jwt.auth',
    'prefix' => 'users'

], function ($router) {

    Route::get('/',[UserController::class, 'index'])->name('api.users');
});