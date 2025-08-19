<?php

use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminSchedulingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/scheduling/available-slots', [SchedulingController::class, 'getAvailableSlots']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('ability:client')->group(function () {
        Route::get('/client', [ClientController::class, 'show']);
        Route::put('/clients', [ClientController::class, 'update']);
        Route::delete('/clients', [ClientController::class, 'destroy']);

        Route::get('/schedulings', [SchedulingController::class, 'index']);
        Route::get('/schedulings/{schedulingId}', [SchedulingController::class, 'show']);
        Route::post('/schedulings', [SchedulingController::class, 'store']);
        Route::put('/schedulings/{schedulingId}', [SchedulingController::class, 'update']);
        Route::delete('/schedulings/{schedulingId}', [SchedulingController::class, 'destroy']);
    });

    Route::middleware('ability:admin')->group(function () {
        Route::get('/clients', [AdminClientController::class, 'index']);
        Route::get('/client/{clientId}', [AdminClientController::class, 'show']);
        Route::put('/clients/{clientId}', [AdminClientController::class, 'update']);
        Route::delete('/clients/{clientId}', [AdminClientController::class, 'destroy']);

        Route::get('/admin-only/schedulings/', [AdminSchedulingController::class, 'index']);
        Route::get('/admin-only/schedulings/{schedulingId}', [AdminSchedulingController::class, 'show']);
        Route::post('/admin-only/schedulings/{clientId}', [AdminSchedulingController::class, 'store']);
        Route::put('/admin-only/schedulings/{schedulingId}', [AdminSchedulingController::class, 'update']);
        Route::delete('/admin-only/schedulings/{schedulingId}', [AdminSchedulingController::class, 'destroy']);

        Route::get('/admins', [UserController::class, 'index']);
        Route::get('/admin/{adminId}', [UserController::class, 'show']);
        Route::get('/admin', [UserController::class, 'showLoggedUser']);
        Route::post('/admins', [UserController::class, 'store']);
        Route::put('/admins/{adminId}', [UserController::class, 'update']);
        Route::put('/admins', [UserController::class, 'updateLoggedUser']);
        Route::delete('/admins/{adminId}', [UserController::class, 'destroy']);
        Route::delete('/admins', [UserController::class, 'destroyLoggedUser']);
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/clients', [ClientController::class, 'store']);
