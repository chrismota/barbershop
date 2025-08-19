<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('/users', UserController::class);
Route::get('/clients/schedulings', [SchedulingController::class, 'index']);
Route::apiResource('/clients', ClientController::class);
Route::get('/clients/schedulings/{schedulingId}', [SchedulingController::class, 'show']);
Route::post('/clients/schedulings/{clientId}', [SchedulingController::class, 'store']);
Route::put('/clients/{clientId}/schedulings/{schedulingId}', [SchedulingController::class, 'update']);
Route::delete('/clients/{clientId}/schedulings/{schedulingId}', [SchedulingController::class, 'destroy']);

Route::get('/scheduling/available-slots', [SchedulingController::class, 'getAvailableSlots']);


