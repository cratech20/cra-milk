<?php


namespace App\Services\Reports\Generators;

use Carbon\Carbon;

class LitersByHourPeriodsGenerator
{
    use GeneratorTrait;

    private $hourPeriods;
    public $fullHourPeriods;

    public static function process()
    {
        $generator = new self();

        $hourPeriods = [
            '12:00:00',
        ];

        $generator->hourPeriods = self::generateHourPeriods($hourPeriods);

        // по дефолту отчет за последние 30 дней
        $generator->fillDefaultDatePeriod();
        $generator->fillDevicesAndCows();
        $generator->getAndParseJSON();

        $generator->fillFullHourPeriods();

        // сортировка дат в обратном порядке
        $generator->datesSortDesc();
        $generator->fillDatesForHead();

        // TODO Не учитывается инфа отправленная на время 23:59:59
        $generator->fillHeadAndBody();

        return $generator->getResult();
    }

    public function datesSortDesc()
    {
        krsort($this->fullHourPeriods);
    }

    public function fillDatesForHead()
    {
        $datesForHead = [];

        foreach ($this->fullHourPeriods as $date => $dayPeriods) {
            foreach ($dayPeriods as $key => $dayPeriod) {
                // TODO вместо Carbon создавать через $date и $this->hourPeriods
                $from = Carbon::parse($dayPeriod[0]);
                $to = Carbon::parse($dayPeriod[1]);
                $datesForHead[] = $from->format('d.m.y') . ' с ' . $from->format('H:i') . ' до ' . $to->format('H:i');
            }
        }

        $this->datesForHead = $datesForHead;
    }

    public function fillFullHourPeriods()
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

    public function fillHeadAndBody()
    {
        $cows = $this->cows;
        $devices = $this->devices;
        $deviceByCow = [];
        $result = [];

        $head = ['Устройство', 'Корова', 'Группа'];
        $result['head'] = array_merge($head, $this->datesForHead);

        // заполняются литры в день по коровам!

        $litersByDay = [];
        foreach ($this->parsedData as $rowDB) {
            $row = self::getRow($rowDB);

            $carbonDate = Carbon::parse((int)$row['t']);
            $carbonDateString = $carbonDate->format('Y-m-d H:i:s');

            $hour = $carbonDate->format('H');
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

            foreach ($this->fullHourPeriods as $date => $dayPeriods) {
                foreach ($dayPeriods as $periodKey => $trash) {
                    $dateKey = $date . $periodKey;
                    $body[$cowId][] = $volumes[$dateKey] ?? 0;
                }
            }

            $result['body'] = $body;
        }

        $this->result = $result;
    }

}