<?php


namespace App\Services\Reports\Generators;


use App\Models\Cow;
use App\Models\Device;
use App\Models\DeviceMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait GeneratorTrait
{
    private $isAdmin = false;
    private $startPeriod;
    private $endPeriod;
    private $devices;
    private $cows;
    private $cowCodes = [];
    private $parsedData;
    private $datesForHead;
    private $result;

    private function fillDefaultDatePeriod()
    {
        $startPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endPeriod = Carbon::now()->startOfDay();
        $this->setPeriod($startPeriod, $endPeriod);
    }

    private function getData()
    {
        $cowCodes = $this->cowCodes;
        $this->parsedData = DeviceMessage::whereBetween('device_created_at', [$this->startPeriod, $this->endPeriod])
            ->when(!$this->isAdmin, function ($query) use ($cowCodes) {
                return $query->whereIn('cow_code', $cowCodes);
            })
            ->get();
    }

    private function fillDevicesAndCows()
    {
        $this->devices = Device::all()->keyBy('device_id');
        $cowCodes = $this->cowCodes;
        $cows = Cow::when(!$this->isAdmin, function ($query) use ($cowCodes) {
            return $query->whereIn('cow_id', $cowCodes);
        })->get()->keyBy('cow_id');

        if ($cows->isEmpty()) {
            die('Коровы не добавлены в аккаунт');
        }

        $this->cows = $cows;
    }

    private function setPeriod($start, $end)
    {
        $this->startPeriod = $start;
        $this->endPeriod = $end;
    }

    private static function generateHourPeriods($oldPeriods)
    {
        array_unshift($oldPeriods, '00:00:00');
        $oldPeriods[] = '23:59:59';
        return $oldPeriods;
    }

    private function getResult()
    {
        return $this->result;
    }

    private function getCalculatedLiters($deviceId, $liters, $impulses, $date)
    {
        if ($impulses === 0 || !isset($this->devices[$deviceId])) {
            return $liters;
        }

        $weight = $this->devices[$deviceId]->weight;
        $weightSetDate = Carbon::parse($this->devices[$deviceId]->weight_set_at);

        if ($weight === null || $weightSetDate === null || $date->lte($weightSetDate)) {
            return $liters;
        }

        return $impulses * $weight;
    }
}
