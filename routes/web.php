<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CustomerController::class, 'createView']);
Route::post('/stripe/ach/create', [CustomerController::class, 'createCustomerStripe'])->name('create-customer-stripe');

Route::get('/verify', [CustomerController::class, 'verifyView']);
Route::post('/stripe/ach/verify', [CustomerController::class, 'verifyCustomerStripe'])->name('verify-customer-stripe');

Route::get('/charge', [CustomerController::class, 'chargeView']);
Route::post('/stripe/ach/charge', [CustomerController::class, 'chargeCustomerStripe'])->name('charge-customer-stripe');

Route::get('/replace', [CustomerController::class, 'replaceView']);
Route::post('/stripe/ach/replace', [CustomerController::class, 'replaceCustomerStripe'])->name('replace-customer-stripe');

Route::get('/create/data', [DataController::class, 'index']);
Route::get('/get/data', [DataController::class, 'getData']);