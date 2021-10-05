<?php


namespace App\Services\Reports\DataFillers;


use App\Models\Cow;
use App\Models\Device;
use App\Models\DeviceMessage;
use App\Services\Reports\LitersByHourReport;
use Carbon\Carbon;

class LitersByHourPeriodsDataFiller3
{

    private $datePeriod;
    private $hourPeriods;
    private $user;
    private $isAdmin;
    private LitersByHourReport $report;
    private $startPeriod;
    private $period;
    private $endPeriod;

    public function __construct($datePeriod, $hourPeriods, $user, $isAdmin, LitersByHourReport $report)
    {
        $this->datePeriod = $datePeriod;
        $this->hourPeriods = $hourPeriods;
        $this->user = $user;
        $this->isAdmin = $isAdmin;
        $this->report = $report;

        if ($this->datePeriod) {
            // fillStartEndDatePeriod($datePeriod)
        } else {
            $this->fillDefaultDatePeriod(); // по дефолту отчет за последние 30 дней
        }
    }

    public function process()
    {
        $report = $this->report;

        $cows = $this->getCows();

        $report->setPeriod($this->startPeriod, $this->period, $this->endPeriod);
        $report->setHourPeriods($this->hourPeriods);
        $report->setDevices($this->getDevices());
        $report->setCows($cows);
        $report->setData($this->getData($cows));
        return $report;
    }

    private function getData($cows)
    {
        return DeviceMessage::whereBetween('device_created_at', [$this->startPeriod, $this->endPeriod])
            ->when(!$this->isAdmin, function ($query) use ($cows) {
                $cowCodes = $cows->pluck('cow_id');
                return $query->whereIn('cow_code', $cowCodes);
            })
            ->get();
    }

    private function getCows()
    {
        if ($this->isAdmin) {
            return Cow::all();
        }

        $cows = $this->user->cows;

        if ($cows->isEmpty()) {
            die('Коровы не добавлены в аккаунт');
        }

        return $cows->keyBy('cow_id');
    }

    private function getDevices()
    {
        return Device::all()->keyBy('device_id'); // потому что девайсы переходят из одного хоз-ва в другое
    }

    private function fillDefaultDatePeriod()
    {
        $startPeriod = Carbon::now()->subDays(30)->startOfDay();
        $period = Carbon::now()->addHours(1);
        $endPeriod = Carbon::now()->endOfDay();
        $this->setPeriod($startPeriod, $period, $endPeriod);
    }

    private function setPeriod($start, $period, $end)
    {
        $this->startPeriod = $start;
        $this->period = $period;
        $this->endPeriod = $end;
    }
}
