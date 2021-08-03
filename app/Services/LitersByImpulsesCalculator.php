<?php


namespace App\Services;


use Carbon\Carbon;

class LitersByImpulsesCalculator
{
    private $devices = [];

    public function __construct($devices)
    {
        $this->devices = $devices;
    }

    public function calc($deviceId, $liters, $impulses, $date)
    {
        if ($impulses === 0 || !isset($this->devices[$deviceId])) {
            return $liters;
        }

        $weight = $this->devices[$deviceId]->weight;
        $weightSetDate = Carbon::parse($this->devices[$deviceId]->weight_set_at);

        if ($weight === null || $weightSetDate === null || $date->lte($weightSetDate)) {
            return $liters;
        }

        return $impulses * $weight / 1000;
    }
}
