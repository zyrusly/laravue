<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:sanctum'])->group(function () {

    //get current user
    Route::get('get-user', function (Request $request) {
        return $request->user();
    });

    //users 
    Route::resource('user', UserController::class);

    //roles
    Route::resource('role', RoleController::class);
});

Route::post('/login', [LoginController::class, 'login']);
Route::delete('/logout', [LoginController::class, 'logout']);
