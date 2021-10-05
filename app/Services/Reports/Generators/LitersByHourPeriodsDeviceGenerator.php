<?php


namespace App\Services\Reports\Generators;

use App\Models\User;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDeviceDataFiller;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDeviceDataFiller2;
use App\Services\Reports\LitersByHourPeriodsDeviceReport;
use App\Services\Reports\LitersPeriodsDeviceReport;

class LitersByHourPeriodsDeviceGenerator
{
    public static function process($datePeriod, $hourPeriods, User $user, $isAdmin = false, $reports = '')
    {
        if ($reports == '') {
            $dataFiller = new LitersByHourPeriodsDeviceDataFiller($datePeriod, $hourPeriods, $user, $isAdmin, new LitersByHourPeriodsDeviceReport());
            $report = $dataFiller->process();
        } else {
            $dataFiller = new LitersByHourPeriodsDeviceDataFiller2($datePeriod, $hourPeriods, $user, $isAdmin, new LitersPeriodsDeviceReport());
            $report = $dataFiller->process();
        }
        
        return $report->getResult();
    }
}
