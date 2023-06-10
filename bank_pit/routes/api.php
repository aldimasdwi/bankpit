<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\SaldoTabunganController;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/token', [AuthController::class, 'otp']);
Route::post('/tokenpassword', [AuthController::class, 'otptoken']);
Route::post('/tokenulang', [AuthController::class, 'kirimulang']);
Route::post('/passworddepan', [AuthController::class, 'passworddepan']);



Route::group(['middleware' => ['auth:sanctum']], function () {
    //saldo
    Route::post('/ceksaldo',[SaldoController::class, 'ceksaldo']);
    Route::post('/isisaldo', [SaldoController::class, 'isisaldo']);
    Route::post('/isitabungan', [SaldoTabunganController::class, 'isitabungan']);
    Route::post('/isisaldoutama', [SaldoTabunganController::class, 'isisaldoutama']);
    Route::post('/cektabungan', [SaldoTabunganController::class, 'cektabungan']);
    Route::post('/transfer', [TransferController::class, 'transfer']);
    Route::post('/data', [DataController::class, 'historyuser']);


    Route::post('/profil', [WebController::class, 'profil']);
    Route::post('/password', [AuthController::class, 'password']);


    Route::post('/logoutuser', [AuthController::class, 'logout']);
});




Route::group(['middleware' => ['auth:sanctum','admin']], function () {

    Route::post('/datauser', [DataController::class, 'datauser']);
    Route::post('/dataadmin', [DataController::class, 'dataadmin']);
    Route::post('/data/{id}', [DataController::class, 'historyadmin']);
    Route::post('/search', [DataController::class, 'search']);




    Route::post('/logoutadmin', [AuthController::class, 'logout']);
});
