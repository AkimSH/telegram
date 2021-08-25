<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\TelegauthController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('get-me', [TelegauthController::class, 'getMe']);
Route::get('set-hook', [TelegauthController::class, 'setWebHook']);
Route::get('info-hook', [TelegauthController::class, 'getWebhookInfo']);
Route::post(env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegauthController::class, 'handleRequest']);

/*Route::get(env('TELEGRAM_BOT_TOKEN') . '/webhook', function () {
    $updates = Telegram::getWebhookUpdates();

    dd($updates);
});*/

