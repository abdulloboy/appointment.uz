<?php

use App\Http\Controllers\AppointmentController;
use App\Models\Appointment;
use App\Models\User;
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

Route::post('/tokens/create', function (Request $request) {
    $user = User::find(1);
    $token = $user->createToken($request->token_name);
    //$token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});


Route::middleware('auth:sanctum')->get('/Appointment', [AppointmentController::class, 'index']);
Route::middleware('auth:sanctum')->post('/Appointment', [AppointmentController::class, 'store']);
Route::middleware('auth:sanctum')->get('/Appointment/{appointment}', [AppointmentController::class, 'show']);
Route::middleware('auth:sanctum')->patch('/Appointment/{appointment}', [AppointmentController::class, 'update']);
