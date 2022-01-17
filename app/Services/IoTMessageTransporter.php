<?php


namespace App\Services;


use App\Models\DeviceMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IoTMessageTransporter
{
    /**
     * @throws \JsonException
     */
    public static function run()
    {
        $lastMessage = DeviceMessage::orderBy('id', 'desc')->get()->first();

        $postgreRows = DB::connection('pgsql')->table('iot_events')
            ->when(isset($lastMessage), function ($query) use ($lastMessage) {
                return $query->where('event_datetime', '>=', $lastMessage->device_created_at);
            })
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->i')
            ->whereNotNull('payload->l')
            ->whereNotNull('payload->t')
            ->whereNotNull('payload->y')
            ->whereNotNull('payload->e')
            ->limit(10)
            ->get();

        // dd($postgreRows);

        foreach ($postgreRows as $postgreRow) {
            $json = $postgreRow->payload;
            $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            $server_created_at = Carbon::parse($postgreRow->event_datetime);
            $device_created_at = Carbon::parse((int)$payload['t']);
            $yield = (int)$payload['y'];

            // искать за последние 2 недели server_created_at
            $whereData = [
                ['device_created_at', $device_created_at],
                ['device_login', $payload['l']],
                ['cow_code', $payload['c']],
                ['yield', $yield],
                ['impulses', $payload['i']],
                ['battery', $payload['b']],
                // ['error', $payload['e']],
                ['message_num', $payload['n']],
            ];

            $messages = DeviceMessage::where($whereData)->get();

            if ($messages->isEmpty()) {
                $dataForCreate = [
                    'device_created_at' => $device_created_at,
                    'device_login' => $payload['l'],
                    'cow_code' => $payload['c'],
                    'yield' => $yield,
                    'impulses' => $payload['i'],
                    'battery' => $payload['b'],
                    // 'error' => $payload['e'],
                    'message_num' => $payload['n'],
                    'server_created_at' => $server_created_at
                ];

                DeviceMessage::insert($dataForCreate);
            }
        }
    }
}
