<?php


namespace App\Services;


use App\Models\Cow;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ReportGenerator
{
    private $startPeriod;
    private $endPeriod;
    private $devices;
    private $cows;
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
        $this->devices = Device::get(['name', 'device_id'])->pluck('name', 'device_id');
        $this->cows = Cow::all()->keyBy('cow_id');
    }

    public function getAndParseJSON()
    {
        if (env('APP_ENV') === 'local') {
            $json = Storage::get('milk-bi.json');
        } else {
            $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        }

        $json = preg_replace('|\\\\u0003|', '', $json);
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

    /**
     * Алгоритмы для получения отчета
     */

    public static function getLitersByCow()
    {
        $generator = new self();
        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesAndDatesForHead();
        $generator->getLitersByCowBody();

        return $generator->result;
    }

    public static function getLitersByDevice()
    {
        $generator = new self();
        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesAndDatesForHead();
        $generator->getLitersByDeviceBody();

        return $generator->result;
    }

    public static function getImpulsesByCow()
    {
        $generator = new self();
        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesAndDatesForHead();
        $generator->getImpulsesByCowBody();

        return $generator->result;
    }

    public function getLitersByCowBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство', 'Корова', 'Группа'];
        $result['head'] = array_merge($head, $this->datesForHead);

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
            $deviceName = $devices[$deviceId] ?? $deviceId;
            $cowName = $cows[$cowId]->calculated_name ?? $cowId;
            $group = $cows[$cowId]->group->calculated_name ?? 'Неизвестно';
            $body[$cowId] = [$deviceName, $cowName, $group];

            foreach ($this->dates as $dateKey => $trash) {
                $body[$cowId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

    public function getLitersByDeviceBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство'];

        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litresByDay = [];
        foreach ($this->data as $rowDirty) {
            $row = $rowDirty[3];

            if (!isset($row['y'], $row['c'])) {
                continue;
            }

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $litresByDay[$deviceId][$date] = ($litresByDay[$deviceId][$date] ?? 0) + $row['y'];
        }

        $body = [];

        foreach ($litresByDay as $deviceId => $volumes) {
            $deviceName = $devices[$deviceId] ?? $deviceId;
            $body[$deviceId] = [$deviceName];

            foreach ($this->dates as $dateKey => $trash) {
                $body[$deviceId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

    public function getImpulsesByCowBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство', 'Корова', 'Группа'];
        $result['head'] = array_merge($head, $this->datesForHead);

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
            $litresByDay[$cowId][$date] = ($litresByDay[$cowId][$date] ?? 0) + $row['i'];
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litresByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $devices[$deviceId] ?? $deviceId;
            $cowName = $cows[$cowId]->calculated_name ?? $cowId;
            $group = $cows[$cowId]->group->calculated_name ?? 'Неизвестно';
            $body[$cowId] = [$deviceName, $cowName, $group];

            foreach ($this->dates as $dateKey => $trash) {
                $body[$cowId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

}