<?php

namespace App\Http\Controllers;

use App\Models\DeviceMessage;
use App\Services\DI\YaCloud;
use App\Services\IoTMessageTransporter;
use App\Services\LitersByImpulsesCalculator;
use App\Services\YaCloud\Device;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Easy\Build;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Facades\DaDataCompany;
use App\Models\Device as DM;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('spa');
    }

    public function deviceMessages()
    {
        $devices = \App\Models\Device::all()->keyBy('device_id');
        $messages = DeviceMessage::all();
        $litersByImpulsesCalculator = new LitersByImpulsesCalculator($devices);

        $messagesWithLiters = $messages->map(static function ($message) use ($litersByImpulsesCalculator) {
            $calculatedLiters = $litersByImpulsesCalculator->calc(
                $message->device_login, $message->liters, $message->impulses, Carbon::parse($message->device_created_at)
            );

            return [
                'l' => $message->device_login,
                'mac' => $message->mac,
                'c' => $message->cow_code,
                'y' => $message->yield,
                'i' => $message->impulses,
                'li' => $calculatedLiters,
                'b' => $message->battery,
                'e' => $message->error,
                'n' => $message->message_num,
                'st' => $message->server_created_at,
                'dt' => $message->device_created_at,
            ];
        });

        return response()->json($messagesWithLiters);
    }

    public function auth()
    {
        $url = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';

        $headers = [
            'Content-Type: application/json',
        ]; // заголовки нашего запроса

        $post_data = [ // поля нашего запроса
            "yandexPassportOauthToken" => "AQAAAAAJnHzYAATuwd-2FmZNs0MIsNF2Ne3jj98"
        ];

        $data_json = json_encode($post_data); // переводим поля в формат JSON

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl); // результат POST запроса

        $iamToken = json_decode($result);
        // dd($iamToken);
        return $iamToken->iamToken;
    }

    public function testJSON()
    {
        $iamToken = $this->auth();
        $time = time();
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/registries/arerbdnuk54prrjanos2/publish';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$iamToken
        ];

        $post_data = [
            "topic" => '$devices/arein49ukcajcgulva8c/commands',
            'data' =>  base64_encode('{"com": "update", "a": "48:3F:DA:5C:89:FF"}')
        ];

        $data_json = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl); // результат POST запроса

        dd($result);
    }

    public function run()
    {
        // $lastMessage = DeviceMessage::orderBy('id', 'desc')->get()->first();

        // $postgreRows = DB::connection('pgsql')->table('iot_events')
        //     ->when(isset($lastMessage), function ($query) use ($lastMessage) {
        //         return $query->where('event_datetime', '>=', $lastMessage->device_created_at);
        //     })
        //     ->whereNotNull('payload->c')
        //     ->whereNotNull('payload->i')
        //     ->whereNotNull('payload->l')
        //     ->whereNotNull('payload->t')
        //     ->whereNotNull('payload->y')
        //     ->get();

        // $postgreRows2 = DB::connection('pgsql')->table('iot_events')->get();

        // // dd($postgreRows[2]);

        // foreach ($postgreRows as $postgreRow) {
        //     $json = $postgreRow->payload;
        //     $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        //     $server_created_at = Carbon::parse($postgreRow->event_datetime);
        //     $device_created_at = Carbon::parse((int)$payload['t']);
        //     $yield = (int)$payload['y'];

        //     // искать за последние 2 недели server_created_at
        //     $whereData = [
        //         ['device_created_at', $device_created_at],
        //         ['device_login', $payload['l']],
        //         ['cow_code', $payload['c']],
        //         ['yield', $yield],
        //         ['impulses', $payload['i']],
        //         ['battery', $payload['b']],
        //         ['error', $payload['e']],
        //         ['message_num', $payload['n']],
        //     ];

        //     $messages = DeviceMessage::where($whereData)->get();

        //     if ($messages->isEmpty()) {
        //         $dataForCreate = [
        //             'device_created_at' => $device_created_at,
        //             'device_login' => $payload['l'],
        //             'cow_code' => $payload['c'],
        //             'yield' => $yield,
        //             'impulses' => $payload['i'],
        //             'battery' => $payload['b'],
        //             'error' => $payload['e'],
        //             'message_num' => $payload['n'],
        //             'server_created_at' => $server_created_at
        //         ];

        //         DeviceMessage::insert($dataForCreate);
        //     }
        // }
        // $test = DB::connection('pgsql')
        //     ->select("select * from iot_events
        //         where event_datetime > current_date - INTERVAL '14 days'
        //         and payload->>l = 'areb9niqt2jf1ffodohg'
        //         order by event_datetime DESC
        //         limit 100000;");

        $json = DB::connection('pgsql')->table('iot_events')
                ->whereJsonContains('payload->l', 'are77vr56e5ucsqva03j')
                ->first();
        dd($json);
    }
}
