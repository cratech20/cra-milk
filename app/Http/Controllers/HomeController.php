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
