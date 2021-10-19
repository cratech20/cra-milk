<?php


namespace App\Services\YaCloud;


class Device extends YaCloudEntity
{
    public function list($data)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices';
        return $this->client->call($url, $data);
    }

    public function create($data)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices';
        return $this->client->call($url, $data, 'POST');
    }
}
