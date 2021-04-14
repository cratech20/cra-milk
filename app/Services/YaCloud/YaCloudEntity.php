<?php


namespace App\Services\YaCloud;


use App\Services\DI\YaCloud;

class YaCloudEntity
{
    protected $client;

    public function __construct(YaCloud $yaCloud)
    {
        $this->client = $yaCloud;
    }
}