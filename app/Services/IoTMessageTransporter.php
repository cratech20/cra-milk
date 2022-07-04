<?php


namespace App\Services;


use App\Models\DeviceMessage;
use App\Models\RawDeviceMessage;
use Carbon\Carbon;

class IoTMessageTransporter
{
    /**
     * @throws \JsonException
     */
    public function run()
    {
        $rawDeviceMessages = RawDeviceMessage::whereNull('parsed_at')->get();
        foreach ($rawDeviceMessages as $rawDeviceMessage) {
            $json = $rawDeviceMessage->message;
            $payload = json_decode($json, true, 10);
            if (!$this->isValidPayload($payload)) {
                $this->checkParsed($rawDeviceMessage);
                continue;
            }
            $server_created_at = $rawDeviceMessage->created_at;
            $device_created_at = Carbon::parse((int)$payload['t']);
            $yield = (int)$payload['y'];
            if ($this->isDuplicate($rawDeviceMessage->device_id, $device_created_at, $payload['n'], $yield)) {
                $this->checkParsed($rawDeviceMessage);
                continue;
            }
            $dataForCreate = [
                'device_created_at' => $device_created_at,
                'device_login' => $rawDeviceMessage->device_id,
                'cow_code' => $payload['c'],
                'yield' => $yield,
                'impulses' => $payload['i'],
                'battery' => $payload['b'],
                'error' => $payload['e'],
                'message_num' => $payload['n'],
                'server_created_at' => $server_created_at
            ];
            DeviceMessage::insert($dataForCreate);
            $this->checkParsed($rawDeviceMessage);
        }
    }

    /**
     * @param $payload
     *
     * @return bool
     */
    private function isValidPayload($payload): bool
    {
        if (!is_array($payload)) {
            return false;
        }
        return (
            isset($payload['c']) &&
            isset($payload['y']) &&
            isset($payload['t']) &&
            isset($payload['i']) &&
            isset($payload['b']) &&
            isset($payload['e']) &&
            isset($payload['n'])
        );
    }

    /**
     * @param $device_id
     * @param $device_created_at
     * @param $messageNumber
     * @param $yield
     * @return bool
     */
    private function isDuplicate($device_id, $device_created_at, $messageNumber, $yield): bool
    {
        return (bool) DeviceMessage::where('device_login', $device_id)
            ->where('device_created_at', $device_created_at)
            ->where('message_num', $messageNumber)
            ->where('yield', $yield)
            ->count();
    }

    private function checkParsed($rawDeviceMessage)
    {
        $rawDeviceMessage->update(['parsed_at' => Carbon::now()]);
    }
}
