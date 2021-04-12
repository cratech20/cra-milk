<?php


namespace App\Services\YaCloud;


use App\Services\DI\YaCloud;

class Device
{
    private $yaCloud;

    public function __construct(YaCloud $yaCloud)
    {
        $this->yaCloud = $yaCloud;
    }

    public function list($data)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices';
        return $this->yaCloud->request($url, $data);
    }

    public function create($data)
    {
        $url = 'https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices';
        return $this->yaCloud->request($url, $data);
    }
}