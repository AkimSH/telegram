<?php

use App\Http\Controllers\TelegramController;
use App\Models\Telegauth;
use App\Models\Telegramsuspense;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('get-me', [TelegramController::class, 'getMe']);
Route::get('set-hook', [TelegramController::class, 'setWebHook']);
Route::post(env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegramController::class, 'handleRequest']);

Route::get('test', function () {
    //$data = Telegauth::where('chat_id', 201502307 )->get();
    /*$data = Telegramsuspense::where('chat_id', 201502307 )->first();
    $asd = $data->suspense_from;

    if ($data->suspense_from === 'authentication') {
        echo 'asd';
    }*/

    /*$str = 'Sharapov-123456';

    $str = explode('-', $str);

    dd(Telegauth::where('surname', $str[0])->where('verification_code', $str[1])->exists());*/


    //$telegram = Telegramsuspense::where('chat_id', 201502307)->first();

    /*$asd = Telegauth::where('surname', 'Sharapov')->where('verification_code', '123456')->update([
        'chat_id' => 2255522,
        'verified' => true
    ]);

    dd($asd);*/


    $url = "https://terminal.ffins.kz/api/v2/message/send";
    $apiKey = env('TELEGRAM_FFI_BOT_API');
    $userName = env('TELEGRAM_FFI_BOT_USER');
    $pass = env('TELEGRAM_FFI_BOT_PASS');

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        "Accept: application/json",
        "Api-key: " . $apiKey,
        'Authorization: Basic '. base64_encode($userName . ":" . $pass),
        "Content-Type: application/json",
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = <<<DATA
        {
            "phone": "77778078802",
            "message": "new test"
        }
        DATA;

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($resp, true);
    //var_dump(json_decode($resp['success']));


    //$response = json_decode($response, true);

    echo isset($response['success']) ? $response[]= true : $response[]=false;

    dd($response);
});
