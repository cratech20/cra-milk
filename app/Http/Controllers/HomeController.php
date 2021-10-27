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
use DB;

class HomeController extends Controller
{
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

    public function table()
    {
        $chartDataAr = DB::connection('pgsql')->table('iot_events')
            ->whereNotNull('payload->ar')
            ->whereNotNull('payload->c')
            ->orderBy('event_datetime', 'DESC')
            ->get();

        foreach ($chartDataAr as $item) {
            $payload = json_decode($item->payload);
            // dd($item);
            $cow = Cow::where('cow_id', $payload->c)->first();
            if ($cow) {
                $data[] = [
                    'code' => $payload->c,
                    'num' => $cow['internal_code'],
                    '5num' => $cow->getNumberByCode($cow['cow_id']),
                    'date' => Carbon::parse($item->event_datetime)->format('d.m.Y H:i:s'),
                    'ar_count' => count($payload->ar),
                    'ar' => $payload->ar
                ];
            };
        };

        // dd($data);

        $i = 0;
        while ($i < count($data)-1) {
            if ($data[$i]['code'] == $data[$i+1]['code']) {
                // dd($data[$i]);
                // $dates[$data[$i]['code']]['date'][] = $data[$i]['date'];
                $dates[$data[$i]['code']][$data[$i]['date']] = $data[$i]['ar'];
                $arr[$data[$i]['code']] = [
                    'num' => $data[$i]['num'],
                    '5num' => $data[$i]['5num'],
                    'ar_count' => $data[$i]['ar_count']
                ];
            }
            $i++;
        }

        foreach ($dates as $k => $date) {
            // foreach($date as $d) {
            //     dd($date);
            // };
            $arr[$k]['data'] = $date;
        }

        return response()->json($arr);
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
