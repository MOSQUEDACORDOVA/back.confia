<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionsController;
use App\Http\Middleware\CheckUserType;

Route::post('/login', [AuthController::class, 'login']);
//Crear usuario
Route::post('/register', [AuthController::class, 'register_width_operation_number']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('auth:sanctum')->get('/check-session', [AuthController::class, 'checkSession']);

    //Crear registro
    Route::post('/deposit', [TransactionsController::class, 'store']);

    // Crear y editar usuario
    Route::middleware(CheckUserType::class)->group(function () {
        //Ver delivery
        Route::get('/deposit', [TransactionsController::class, 'getUserTransactions']);
        //Ver usuarios
        Route::get('/users', [UserController::class, 'index']);
    });

});



