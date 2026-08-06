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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Route::get('token', [App\Http\Controllers\API\APIController::class, 'getToken']);
Route::post('login', [App\Http\Controllers\API\APIController::class, 'login']);
Route::post('datatypes', [App\Http\Controllers\API\APIController::class, 'getDataTypes']);
Route::post('datalist', [App\Http\Controllers\API\APIController::class, 'getData']);
Route::post('search', [App\Http\Controllers\API\APIController::class, 'searchProfiles']);
Route::post('emailCheck', [App\Http\Controllers\API\APIController::class, 'emailInUse']);
Route::post('registerUser', [App\Http\Controllers\API\APIController::class, 'registerUser']);
Route::post('updateUserProfile', [App\Http\Controllers\API\APIController::class, 'updateUserProfile']);

// Stripe webhook (no CSRF)
Route::post('stripe/webhook', [App\Http\Controllers\PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');
