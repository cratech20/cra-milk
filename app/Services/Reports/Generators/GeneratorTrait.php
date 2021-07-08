<?php


namespace App\Services\Reports\Generators;


use App\Models\Cow;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait GeneratorTrait
{
    private $startPeriod;
    private $endPeriod;
    private $devices;
    private $cows;
    private $parsedData;
    private $datesForHead;
    private $result;

    public function fillDefaultDatePeriod()
    {
        $startPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endPeriod = Carbon::now()->startOfDay();
        $this->setPeriod($startPeriod, $endPeriod);
    }

    public function getAndParseJSON()
    {
        $this->parsedData = DB::connection('pgsql')->table('iot_events')
            ->where('event_datetime', '>=', $this->startPeriod)
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->i')
            ->whereNotNull('payload->l')
            ->whereNotNull('payload->t')
            ->whereNotNull('payload->y')
            ->get();
    }

    public function fillDevicesAndCows()
    {
        $this->devices = Device::all()->keyBy('device_id');
        $this->cows = Cow::all()->keyBy('cow_id');
    }

    public function setPeriod($start, $end)
    {
        $this->startPeriod = $start;
        $this->endPeriod = $end;
    }

    public static function getRow($rowDB)
    {
        $row = json_decode($rowDB->payload, 1, 512, JSON_THROW_ON_ERROR);
        return $row;
    }

    public static function generateHourPeriods($oldPeriods)
    {
        array_unshift($oldPeriods, '00:00:00');
        $oldPeriods[] = '23:59:59';
        return $oldPeriods;
    }

    public function getResult()
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