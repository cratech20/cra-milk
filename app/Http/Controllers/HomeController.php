<?php

namespace App\Http\Controllers;

use App\Models\DeviceMessage;
use App\Models\Cow;
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

    public function getDates()
    {
        $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
            ->select('event_datetime')
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->ar')
            ->orderBy('event_datetime', 'DESC')
            ->get();

        foreach ($chartDataAr as $date) {
            $dateAr[] = Carbon::parse($date->event_datetime)->format('d.m.Y');
        }

        return array_unique($dateAr);
    }

    public function getMac(Request $request)
    {
        $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->ar')
            ->orderBy('event_datetime', 'DESC')
            ->get();

        foreach ($chartDataAr as $data) {
            if (Carbon::parse($data->event_datetime)->format('d.m.Y') == $request->data) {
                $macAr[] = json_decode($data->payload)->c;
            };
        }

        $ar = array_unique($macAr);

        foreach ($ar as $a) {
            $num = Cow::where('cow_id', '=', $a)->first();
            if ($num) {
                $cowAr[] = [
                    'value' => $a.' ('.$num->internal_code.')',
                    'code' => $a
                ];
            } else {
                $cowAr[] = [
                    'value' => $a.' (0)',
                    'code' => $a
                ];
            };
        }

        return $cowAr;
    }

    public function getChartData(Request $request)
    {
        $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->ar')
            ->get();

        foreach ($chartDataAr as $data) {
            if ((Carbon::parse($data->event_datetime)->format('d.m.Y') == $request->date) &&
            (json_decode($data->payload)->c == $request->mac)) {
                $macAr[Carbon::parse($data->event_datetime)->format('H:i:s')] = json_decode($data->payload)->ar;
            };
        }

        foreach ($macAr as $k => $v) {
            $time[] = [
                'time' => $k,
                'value' => $v
            ];
        };


        foreach ($time as $k => $t) {
            $i = 1;
            $time[$k]['interval'][0] = $t['time'];
            while ($i < count($t['value'])) {
                $time[$k]['interval'][$i] = Carbon::parse($time[$k]['interval'][$i-1])->addSeconds(10)->format('H:i:s');
                $i++;
            }
        }

        return response()->json(['data' => [
                'labels' => $time[0]['interval'],
                'datasets' => array([
                    // 'label' => 'Test',
                    'backgroundColor' => '#007bff',
                    'data' => $time[0]['value']
                ])
            ]
        ]);
    }

    public function getData()
    {

        $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
            ->whereNotNull('payload->ar')
            ->whereNotNull('payload->a')
            ->orderBy('event_datetime', 'DESC')
            ->get();

        foreach ($chartDataAr as $item) {
            $payload[] = json_decode($item->payload);
        };

        $i = 0;
        $j = 1;
        $time[] = Carbon::parse('9:00:00')->format('H:i:s');
        while ($j < count($payload[0]->ar)) {
            $time[$j] = Carbon::parse($time[$j-1])->addSeconds(10)->format('H:i:s');
            $j++;
        }

        return response()->json(['data' => [
                'labels' => $time,
                'datasets' => array([
                    'label' => $payload[0]->a,
                    'backgroundColor' => '#007bff',
                    'data' => $payload[0]->ar
                ])
            ]
        ]);
    }

    public function deviceMessages()
    {
        $devices = \App\Models\Device::all()->keyBy('device_id');
        $messages = DeviceMessage::get();
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

        $json = DB::connection('pgsql_milk')->table('iot_events')
                ->whereJsonContains('payload->l', 'arerhs6djigmo6ji7pkf')
                ->first();
        dd($json);
    }

    public function table()
    {
        $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
            ->whereNotNull('payload->ar')
            ->whereNotNull('payload->c')
            ->orderBy('event_datetime', 'DESC')
            ->get();


        foreach ($chartDataAr as $k => $item) {
            $i = 0;
            $j = 0;
            $payload = json_decode($item->payload);
            $cow = Cow::where('cow_id', $payload->c)->first();
            if ($cow) {
                while ($i < count($payload->ar)) {
                    // dd($chartDataAr[$k]);
                    // dd(Carbon::parse($item->event_datetime)->addDay(1)->format('d.m.Y'));
                    $time = Carbon::parse($item->event_datetime)->addSecond($j)->format('H:i:s');
                    $data[] = [
                        'code' => $payload->c,
                        'num' => $cow['internal_code'],
                        '5num' => $cow->getNumberByCode($cow['cow_id']),
                        'date' => Carbon::parse($item->event_datetime)->format('d.m.Y'),
                        'time' => Carbon::parse($time)->addSecond(10)->format('H:i:s'),
                        'ar' => $payload->ar[$i],
                        'interval' => $j
                    ];
                    $j = $j+10;
                    $i++;

                }
            };
        };

        // dd($chartDataAr);
        return response()->json($data);
    }

    public function table2()
    {
        $devices = \App\Models\Device::all()->keyBy('device_id');
        $messages = DeviceMessage::get();
        $litersByImpulsesCalculator = new LitersByImpulsesCalculator($devices);

        $messagesWithLiters = $messages->map(static function ($message) use ($litersByImpulsesCalculator) {
            $calculatedLiters = $litersByImpulsesCalculator->calc(
                $message->device_login, $message->liters, $message->impulses, Carbon::parse($message->device_created_at)
            );

            return [
                'l' => $message->device_login,
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

        // dd(hexdec(strrev('A0348319BD')) % 100000);
        foreach ($messagesWithLiters as $k => $message) {
            $cow = Cow::where('cow_id', $message['c'])->first();
            if ($cow) {
                $cowArr[] = [
                    'c' => $message['c'],
                    'li' => $message['li'],
                    'dt' => Carbon::parse($message['dt'])->format("d.m.Y H:i:s"),
                    'st' => Carbon::parse($message['st'])->format("d.m.Y H:i:s"),
                    'code' => $cow->getNumberByCode($cow->cow_id),
                    'internalId' => isset($cow->internal_code) ? $cow->internal_code : 0,
                ];
            }
        };

        // dd($cowArr[1]);

        return view('devicesMessages', [
            'messages' => $cowArr
        ]);

        // return response()->json($messagesWithLiters);
    }
}
