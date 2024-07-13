<?php

use App\Http\Controllers\TwitterAccessController;
use Illuminate\Http\Request;

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


Route::post("identity-verify-webhook", [\App\Http\Controllers\Api\ProcessIdentityWebhookController::class, 'process'])->name('identity-verify-webhook');
Route::post("identity-verify-create", [\App\Http\Controllers\Api\BlueCheckProcessWebhookController::class, 'process'])->name('identity-verify-create');


Route::get('twitter-test', [TwitterAccessController::class, 'test']);

