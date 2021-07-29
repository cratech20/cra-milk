<?php


namespace App\Services\Reports\Generators;

use App\Models\User;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDeviceDataFiller;
use App\Services\Reports\LitersByHourPeriodsDeviceReport;

class LitersByHourPeriodsGenerator
{
    public static function process($datePeriod, $hourPeriods, User $user, $isAdmin = false)
    {
        $dataFiller = new LitersByHourPeriodsDeviceDataFiller($datePeriod, $hourPeriods, $user, $isAdmin, new LitersByHourPeriodsDeviceReport());
        $report = $dataFiller->process();
        return $report->getResult();
    }
}
