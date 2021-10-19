<?php

namespace App\Services\YaCloud;

use Illuminate\Support\Carbon;

class YaCloud
{
    const AUTH_URI = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';

    var $iamToken = '';

    public function __construct()
    {
        $this->auth();
    }

    private function auth()
    {
        $url = self::AUTH_URI;

        $headers = [
            'Content-Type: application/json',
        ]; // заголовки нашего запроса

        // TODO Перенести в env
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
        $this->iamToken = $iamToken->iamToken;
    }

    public function getCloudId()
    {
        $url = 'https://resource-manager.api.cloud.yandex.net/resource-manager/v1/clouds';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl); // результат POST запроса
        $res = json_decode($result);

        return $res->clouds;
    }

    public function getFolders($cloudId)
    {
        $url = 'https://resource-manager.api.cloud.yandex.net/resource-manager/v1/folders?cloud_id='.$cloudId;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl); // результат POST запроса
        $res = json_decode($result);

        return $res->folders;
    }

    public function getRegistryList($folderId)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/registries?folderId='.$folderId;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl); // результат POST запроса
        $res = json_decode($result);

        return $res->registries;
    }

    public function getRegistry($client_id)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices/'.$client_id;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl); // результат POST запроса
        $res = json_decode($result);

        return $res->registryId;
    }

    public function commands($registry, $command, $mac, $gate_device)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/registries/arerbdnuk54prrjanos2/publish';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $post_data = [];
        switch ($command['com']) {
            case 'reset':
                $post_data = [
                    "topic" => '$devices/'.$gate_device.'/commands',
                    'data' =>  base64_encode('{"com": "reset", "a": "'.$mac.'"}')
                ];
                break;
            case 'update':
                $post_data = [
                    "topic" => '$devices/arein49ukcajcgulva8c/commands',
                    'data' =>  base64_encode('{"com": "update", "a": "'.$mac.'"}')
                ];
                break;
            case 'settime':
                $time  = Carbon::createFromFormat('H:i' , $command['time']['HH'].':'.$command['time']['mm'],'Europe/Moscow')->timestamp;
                $post_data = [
                    "topic" => '$devices/'.$gate_device.'/commands',
                    'data' =>  base64_encode('{"com": "settime", "time": "'.$time.'", "a": "'.$mac.'"}')
                ];
                break;
            case 'setnum':
                $post_data = [
                    "topic" => '$devices/'.$gate_device.'/commands',
                    'data' =>  base64_encode('{"com": "setnum", "num": '.$command['num'].', "a": "'.$mac.'"}')
                ];
                break;
            case 'setcal':
                $post_data = [
                    "topic" => '$devices/'.$gate_device.'/commands',
                    'data' =>  base64_encode('{"com": "setcal", "ves": "'.$command['cal'].'", "a": "'.$mac.'"}')
                ];
                break;
        }

        $data_json = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl); // результат POST запроса

        return $result;
    }

    public function getDevicesList($registryId)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices?registryId='.$registryId;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->iamToken
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl); // результат POST запроса
        $res = json_decode($result);

        return $res->devices;
    }
}
