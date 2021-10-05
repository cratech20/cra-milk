<?php


namespace App\Services\Reports;


use App\Models\User;
use App\Services\LitersByImpulsesCalculator;
use Carbon\Carbon;

class LitersPeriodsDeviceReport extends \App\Services\Reports\LitersByHourPeriodsReport
{
    private $hourPeriods;
    private $parsedData;
    private $cows;
    private $devices;
    private array $datesForHead;
    private $startPeriod;
    private $endPeriod;
    private $result;
    private $fullHourPeriods;
    private LitersByImpulsesCalculator $litersByImpulsesCalculator;

    public function setHourPeriods($humanHourPeriods)
    {
        $this->hourPeriods = self::generateHourPeriods($humanHourPeriods);
    }

    public function setData($data)
    {
        $this->parsedData = $data;
    }

    public function setCows($cows)
    {
        $this->cows = $cows;
    }

    public function setDevices($devices)
    {
        $this->devices = $devices;
    }

    public function getResult()
    {
        $this->litersByImpulsesCalculator = new LitersByImpulsesCalculator($this->devices);
        $this->fillFullHourPeriods();
        $this->datesSortDesc();
        $this->fillDatesForHead();
        $this->fillHeadAndBody();

        return $this->getResultArray();
    }

    private function fillFullHourPeriods()
    {
        $currentDay = clone $this->startPeriod;
        $fullHourPeriods = [];

        while ($currentDay->lessThanOrEqualTo($this->endPeriod)) {
            $fullHourPeriodsByDay = [];

            // формируем и записываем границы периодов в один массив
            foreach ($this->hourPeriods as $hourPeriod) {
                $currentDate = $currentDay->format('Y-m-d');
                $fullHourPeriod = $currentDate . ' ' . $hourPeriod;
                $fullHourPeriodsByDay[$currentDate][] = $fullHourPeriod;
            }

            // соединяем обе границы в один период
            foreach ($fullHourPeriodsByDay as $date => $dayPeriods) {
                $dayPeriodsCount = count($dayPeriods);

                // до предпоследнего элемента
                for ($i = 0; $i < $dayPeriodsCount - 1; $i++) {
                    $fullHourPeriodFrom = $dayPeriods[$i];
                    $fullHourPeriodTo = $dayPeriods[$i + 1];
                    $fullHourPeriods[$date][] = [$fullHourPeriodFrom, $fullHourPeriodTo];
                }
            }

            $currentDay->addDay();
        }

        $this->fullHourPeriods = $fullHourPeriods;
    }

    private
    function fillDatesForHead()
    {
        $datesForHead = [];

        foreach ($this->fullHourPeriods as $date => $dayPeriods) {
            foreach ($dayPeriods as $key => $dayPeriod) {
                // TODO вместо Carbon создавать через $date и $this->hourPeriods
                $from = Carbon::parse($dayPeriod[0]);
                $to = Carbon::parse($dayPeriod[1]);
                $datesForHead[] = $from->format('d.m.y');
            }
        }

        $this->datesForHead = $datesForHead;
    }

    private
    function datesSortDesc()
    {
        krsort($this->fullHourPeriods);
    }

    private
    function fillHeadAndBody()
    {
        $devices = $this->devices;
        $result = [];

        $head = ['Устройство'];
        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litersByDay = [];
        foreach ($this->parsedData as $row) {

            $carbonDate = Carbon::parse($row->device_created_at);
            $carbonDateString = $carbonDate->format('Y-m-d H:i:s');

            $date = $carbonDate->format('Y-m-d');

            $periodKey = null;

            // пропускаем, если дата не входит в заданный период дат
            if (!isset($this->fullHourPeriods[$date])) {
                continue;
            }

            foreach ($this->fullHourPeriods[$date] as $key => $period) {
                if ($period[0] <= $carbonDateString && $carbonDateString < $period[1]) {
                    $periodKey = $key;
                    break;
                }
            }

            $dateKey = $carbonDate->format('Y-m-d') . $periodKey;
            $deviceId = $row->device_login;
            $liters = $this->getCalculatedLiters($deviceId, $row->liters, $row->impulses, $carbonDate);
            $litersByDay[$deviceId][$dateKey] = ($litersByDay[$deviceId][$dateKey] ?? 0) + $liters;
        }

        $body = [];

        foreach ($litersByDay as $deviceId => $volumes) {
            $deviceName = $devices[$deviceId]->name ?? $deviceId;
            $body[$deviceId] = [$deviceName];

            foreach ($this->fullHourPeriods as $date => $dayPeriods) {
                foreach ($dayPeriods as $periodKey => $trash) {
                    $dateKey = $date . $periodKey;
                    $body[$deviceId][] = $volumes[$dateKey] ?? 0;
                }
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

    private
    function getResultArray()
    {
        return $this->result;
    }

    private static function generateHourPeriods($humanHourPeriods)
    {
        array_unshift($humanHourPeriods, '00:00:00');
        $humanHourPeriods[] = '23:59:59';
        return $humanHourPeriods;
    }

    public function setPeriod($start, $end)
    {
        $this->startPeriod = $start;
        $this->endPeriod = $end;
    }

    private function getCalculatedLiters($deviceId, $liters, $impulses, $date)
    {
        return $this->litersByImpulsesCalculator->calc($deviceId, $liters, $impulses, $date);
    }
}
