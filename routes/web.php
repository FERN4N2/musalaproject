<?php

use App\Http\Controllers\GatewayController;
use App\Http\Controllers\PeripheralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/indexGateways', [GatewayController::class, 'index']);
Route::post('/storeGateway', [GatewayController::class, 'store']);
Route::post('/updateGateway', [GatewayController::class, 'update']);
Route::post('/destroyGateway', [GatewayController::class, 'destroy']);

Route::post('/indexPeripherals', [PeripheralController::class, 'index']);
Route::post('/storePeripheral', [PeripheralController::class, 'store']);
Route::post('/updatePeripheral', [PeripheralController::class, 'update']);
Route::post('/destroyPeripheral', [PeripheralController::class, 'destroy']);


Route::get('/', function () {
    return view('welcome');
});
