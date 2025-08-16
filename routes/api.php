<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('/users', UserController::class);
Route::apiResource('/clients', ClientController::class);
