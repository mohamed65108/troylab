<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
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

Route::middleware('auth:api')->group(function () {
    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('students', StudentController::class);
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
});


Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');
