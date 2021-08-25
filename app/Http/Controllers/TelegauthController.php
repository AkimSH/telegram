<?php

namespace App\Http\Controllers;
use Brick\Math\Exception\DivisionByZeroException;
use Telegram\Bot\Api;
use App\Telegram;
use App\Models\Telegauth;
use App\Models\Telegramsuspense;


use Illuminate\Http\Request;


/**
 * LIST OF BOT COMMANDS
 * /sendclientnotification
 * /authorization
 * */
class TelegauthController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $username;
    protected $text;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function WebhookInfo()
    {
        $response = $this->telegram->WebhookInfo();
        return $response;
    }

    public function setWebHook()
    {
        $url = 'https://akmperformance.com/api/' . env('TELEGRAM_BOT_TOKEN') . '/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == true ? redirect()->back() : dd($response);
    }

    public function handleRequest(Request $request)
    {
        $telegramApiRequest = $request;
        file_put_contents(__DIR__.'/log.txt', '/////////////////////////////////////////////||NEW REQUEST||/////////////////////////////////////////////'."\n" . $telegramApiRequest ."\n"."\n", FILE_APPEND);

        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];

        switch ($this->text) {
            case '/start':
                if ($this->checkAuth($this->chat_id)) {
                    $this->sendMessage($this->chat_id);
                } else {
                    $this->sendMessage('Please proceed /authentication');
                }
            break;
            case '/authorization':
                $this->authorization($this->chat_id);
            break;
            case '/sendclientnotification':
                $this->sendClientNotification($this->chat_id);
            break;
            default:
                $this->checkDatabase();
            break;
        }
    }

    protected function checkDatabase()
    {
        try {
            $telegram = Telegramsuspense::where('chat_id', $this->chat_id)->first();
            if ($telegram->suspense_from == 'authorization') {
                $input = explode(' ', $this->text);
                if (Telegauth::where('surname', $input[0])->where('verification_code', $input[1])->update([
                    'chat_id' => $this->chat_id,
                    'verified' => true
                ])) {
                    $this->sendMessage('Done, now u are verified');
                } else {
                    $this->sendMessage('No such record in data base');
                }

                Telegramsuspense::where('chat_id', $this->chat_id)->delete();
            } elseif ($telegram->suspense_from == 'sendClientNotification') {
                Telegramsuspense::where('chat_id', $this->chat_id)->delete();
                $input = explode(' ', $this->text);
                if ($this->sendTelegramMainApi($input[0], 'test')) {
                    $this->sendMessage('Message sent successful');
                } else {
                    $this->sendMessage('Error');
                }
            } else {
                $this->sendMessage('Choose command');
            }
        } catch (\Exception $e) {
            $this->sendMessage('something went wrong');
        }


    }

    protected function sendClientNotification($chat_id)
    {
        $this->sendMessage('Please write clients number name and surname. Example +77778078802 Sharapov Akim');

        Telegramsuspense::create([
            'chat_id' => $chat_id,
            'suspense_from' => __FUNCTION__
        ]);
    }

    protected function authorization($chat_id)
    {
        Telegramsuspense::create([
            'chat_id' => $chat_id,
            'suspense_from' => __FUNCTION__
        ]);

        $this->sendMessage("Please write your surname and code. Example Sharapov 123456 ");
    }

    protected function checkAuth($chat_id)
    {
        if (Telegauth::where('chat_id', $chat_id )->exists()){
            return true;
        }
        return false;
    }

    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];

        if ($parse_html) $data['parse_mode'] = 'HTML';

        $this->telegram->sendMessage($data);
    }

    protected function sendTelegramMainApi($phone, $message)
    {
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
            "phone": $phone,
            "message": $message
        }
        DATA;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        return isset($response['success']) ? true : false;
    }
}
