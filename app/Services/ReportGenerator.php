<?php


namespace App\Services;


use App\Models\Cow;
use App\Models\Device;
use App\Services\Reports\Generators\LitersByHourPeriodsGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
    /**
     * @var array
     */
    public $periods;
    /**
     * @var array
     */
    private $hourPeriods;
    private $fullHourPeriods;

    public function setPeriod($start, $end)
    {
        $this->startPeriod = $start;
        $this->endPeriod = $end;
    }

    public function fillDefaultPeriod()
    {
        $startPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endPeriod = Carbon::now()->startOfDay();
        $this->setPeriod($startPeriod, $endPeriod);
    }

    public function fillDevicesAndCows()
    {
        $this->devices = Device::all()->keyBy('device_id');
        $this->cows = Cow::all()->keyBy('cow_id');
    }

    public function getAndParseJSON()
    {
        $this->data = DB::connection('pgsql')->table('iot_events')
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->i')
            ->whereNotNull('payload->l')
            ->whereNotNull('payload->t')
            ->whereNotNull('payload->y')
            ->get();
    }

    public function fillDatesAndDatesForHead()
    {
        $currentDay = clone $this->startPeriod;

        // заполняются даты для шапки
        $dates = [];
        $datesForHead = [];

        while ($currentDay->lessThanOrEqualTo($this->endPeriod)) {
            $date = $currentDay->format('d.m.y');
            $dateKey = $currentDay->format('Ymd');
            $dates[$dateKey] = $date;
            $datesForHead[] = $date;
            $currentDay = $currentDay->addDay();
        }

        $this->dates = $dates;
        $this->datesForHead = $datesForHead;
    }

    public function fillDatesByHour()
    {
        $currentDay = clone $this->startPeriod;

        // заполняются даты для шапки
        $dates = [];

        while ($currentDay->lessThanOrEqualTo($this->endPeriod)) {

            foreach ($this->hourPeriods as $hourPeriod) {
                $currentDate = $currentDay->format('Y-m-d');
                $fullHourPeriod = $currentDate . ' ' . $hourPeriod;
                $this->fullHourPeriods[$currentDate][] = $fullHourPeriod;
            }

            $currentDay->addDay();
        }
    }

    public function fillDatesByHourOld()
    {
        $currentDay = clone $this->startPeriod;

        // заполняются даты для шапки
        $dates = [];

        while ($currentDay->lessThanOrEqualTo($this->endPeriod)) {

            $hour = $currentDay->format('H');

            $periodKey = null;
            $diffHours = 1;

            foreach ($this->periods as $key => $period) {
                $startHour = explode(':', $period[0])[0];
                $endHour = explode(':', $period[1])[0];

                // если текущее дата-время входит в нужный период
                if ($hour >= $startHour && $hour < $endHour) {
                    $periodKey = $key;
                    $diffHours = abs($endHour - $startHour);
                    break;
                }
            }

            if ($periodKey !== null) {
                $date = $currentDay->format('d.m.y') . ' с ' . $this->periods[$periodKey][0] . ' до ' . $this->periods[$periodKey][1];
                $dateKey = $currentDay->format('Ymd') . $periodKey;

                $dates[$dateKey] = $date;
            }

            // если при прибавке разницы в часах в периоде произошел переход на след день,
            // то обнуляем часы нового дня, иначе часы неправильно будут копиться
            $nextDate = clone $currentDay;
            $nextDate = $nextDate->addHours($diffHours);
            if ($nextDate->format('Ymd') > $currentDay->format('Ymd')) {
                $currentDay = $nextDate->startOfDay();
            } else {
                $currentDay = $nextDate;
            }
        }

        krsort($dates);

        $this->dates = $dates;
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

    public static function getImpulsesByDevice()
    {
        $generator = new self();
        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesAndDatesForHead();
        $generator->getImpulsesByDeviceBody();

        return $generator->result;
    }

    public static function getLitersByHour()
    {
        return LitersByHourPeriodsGenerator::process();
    }

    public static function getLitersByHourOld()
    {
        $generator = new self();

        // TODO переделать остальные отчеты
        $generator->hourPeriods = self::generateHourPeriods(
            [
                '12:00:00',
            ]
        );

        $generator->fillDefaultPeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();
        $generator->fillDatesByHour();

        foreach ($generator->dates as $key => $date) {
            $generator->datesForHead[] = $date;
        }

        $generator->getLitersByHourBody();

        return $generator->result;
    }

    /**
     * Bodies
     */

    public function getLitersByCowBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство', 'Корова', 'Группа'];
        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litersByDay = [];
        foreach ($this->data as $rowDB) {
            $row = self::getRow($rowDB);

            $carbonDate = Carbon::parse((int)$row['t']);
            $date = $carbonDate->format('Ymd');
            $deviceId = $row['l'];
            $cowId = $row['c'];
            $liters = $this->getCalculatedLiters($deviceId, $row['y'], $row['i'], $carbonDate);
            $litersByDay[$cowId][$date] = ($litersByDay[$cowId][$date] ?? 0) + $liters;
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litersByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
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

        $litersByDay = [];
        foreach ($this->data as $rowDB) {
            $row = self::getRow($rowDB);

            $carbonDate = Carbon::parse((int)$row['t']);
            $date = $carbonDate->format('Ymd');
            $deviceId = $row['l'];
            $liters = $this->getCalculatedLiters($deviceId, $row['y'], $row['i'], $carbonDate);
            $litersByDay[$deviceId][$date] = ($litersByDay[$deviceId][$date] ?? 0) + $liters;
        }

        $body = [];

        foreach ($litersByDay as $deviceId => $volumes) {
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
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

        $litersByDay = [];
        foreach ($this->data as $rowDB) {
            $row = self::getRow($rowDB);

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $cowId = $row['c'];
            $litersByDay[$cowId][$date] = ($litersByDay[$cowId][$date] ?? 0) + $row['i'];
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litersByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
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

    public function getImpulsesByDeviceBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство'];

        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litersByDay = [];
        foreach ($this->data as $rowDB) {
            $row = self::getRow($rowDB);

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $litersByDay[$deviceId][$date] = ($litersByDay[$deviceId][$date] ?? 0) + $row['i'];
        }

        $body = [];

        foreach ($litersByDay as $deviceId => $volumes) {
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
            $body[$deviceId] = [$deviceName];

            foreach ($this->dates as $dateKey => $trash) {
                $body[$deviceId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

    public function getLitersByHourBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $this->deviceByCow = [];
        $result = [];

        $head = ['Устройство', 'Корова', 'Группа'];
        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litersByDay = [];
        foreach ($this->data as $rowDB) {
            $row = self::getRow($rowDB);

            $carbonDate = Carbon::parse((int)$row['t']);

            $hour = $carbonDate->format('H');

            $periodKey = null;

            foreach ($this->periods as $key => $period) {
                $startHour = explode(':', $period[0])[0];
                $endHour = explode(':', $period[1])[0];
                if ($hour >= $startHour && $hour < $endHour) {
                    $periodKey = $key;
                    break;
                }
            }

            $dateKey = $carbonDate->format('Ymd') . $periodKey;
            $deviceId = $row['l'];
            $cowId = $row['c'];
            $liters = $this->getCalculatedLiters($deviceId, $row['y'], $row['i'], $carbonDate);
            $litersByDay[$cowId][$dateKey] = ($litersByDay[$cowId][$dateKey] ?? 0) + $liters;
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litersByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
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

    public static function getRow($rowDB)
    {
        $row = json_decode($rowDB->payload, 1, 512, JSON_THROW_ON_ERROR);
        return $row;
    }

}