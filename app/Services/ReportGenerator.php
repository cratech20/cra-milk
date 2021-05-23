<?php


namespace App\Services;


use App\Models\Cow;
use App\Models\Device;
use Carbon\Carbon;

class ReportGenerator
{
    private $startPeriod;
    private $endPeriod;
    private $deviceNames;
    private $allCows;
    private $data;
    private $deviceByCow;
    private $result;
    private $dates;
    private $datesForHead;

    public function setPeriod($start, $end)
    {
        $this->startPeriod = $start;
        $this->endPeriod = $end;
    }

    public function fillDefaultPeriod()
    {
        $startPeriod = Carbon::now();
        $endPeriod = Carbon::now()->subDays(30);
        $this->setPeriod($startPeriod, $endPeriod);
    }

    public function fillDevicesAndCows()
    {
        $this->deviceNames = Device::get(['name', 'device_id'])->pluck('name', 'device_id');
        $this->allCows = Cow::all()->keyBy('cow_id');
    }

    public function getAndParseJSON()
    {
        $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        // $json = Storage::get('milk-bi.json');
        $this->data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    public function fillDatesAndDatesForHead()
    {
        $currentDay = clone $this->startPeriod;

        // заполняются даты для шапки
        $dates = [];
        $datesForHead = [];

        while ($this->endPeriod->lessThanOrEqualTo($currentDay)) {
            $date = $currentDay->format('d.m.y');
            $dateKey = $currentDay->format('Ymd');
            $dates[$dateKey] = $date;
            $datesForHead[] = $date;
            $currentDay = $currentDay->subDay();
        }

        $this->dates = $dates;
        $this->datesForHead = $datesForHead;
    }

    // public static function fillResultHeadDates()
    // {
    //
    // }

    /**
     * Алгоритмы для получения отчета
     */

    public static function getDataByLiters()
    {
        $generator = new self();
        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesAndDatesForHead();
        // $generator->fillResultHeadDates();
        $generator->getDataByLitersBody();

        return $generator->result;
    }

    public function getDataByLitersBody()
    {
        $this->deviceByCow = [];
        $result = [];

        $result['head'] = array_merge(['Устройство', 'Корова', 'Группа'], $this->datesForHead);

        // заполняются литры в день по коровам!

        $litresByDay = [];
        foreach ($this->data as $rowDirty) {
            $row = $rowDirty[3];

            if (!isset($row['y'], $row['c'])) {
                continue;
            }

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $cowId = $row['c'];
            $litresByDay[$cowId][$date] = ($litresByDay[$cowId][$date] ?? 0) + $row['y'];
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litresByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $deviceNames[$deviceId] ?? $deviceId;
            $cowName = $allCows[$cowId]->calculated_name ?? $cowId;
            $group = $allCows[$cowId]->group->calculated_name ?? 'Неизвестно';
            $body[$cowId] = [$deviceName, $cowName, $group];

            foreach ($this->dates as $dateKey => $trash) {
                $body[$cowId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }
}