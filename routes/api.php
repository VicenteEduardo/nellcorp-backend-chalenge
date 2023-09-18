<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', 'AuthController@login');

Route::post('/registerConta', 'AuthController@register');


// Rotas para depósito, saque e transferência
Route::post('deposit', 'Api\Account\DepositController@store')->middleware('auth:sanctum'); // POST para criar um depósito
Route::post('withdraw', 'Api\Account\WithdrawController@store')->middleware('auth:sanctum'); // POST para criar um saque
Route::post('transfer', 'Api\Account\TransferController@store')->middleware('auth:sanctum'); // POST para criar uma transferência

// Rota para obter o saldo de uma conta com base no conta_origem_id
Route::post('getBalance', 'Api\Account\GetBalanceController@index')->middleware('auth:sanctum');

// Rota para obter o histórico de transações de uma conta com base no ID da conta
Route::get('historyaccount', 'Api\Account\HistoryAccountController@index')->middleware('auth:sanctum');

// Rota para solicitar um reembolso com base no ID da transação
Route::post('reimbursement', 'Api\Account\ReimbursementController@store')->middleware('auth:sanctum');
