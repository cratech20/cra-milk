<?php


namespace App\Services\Reports\Generators;

use App\Models\Device;
use App\Models\DeviceMessage;
use App\Models\User;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDataFiller;
use App\Services\Reports\LitersByHourPeriodsReport;
use Carbon\Carbon;

class LitersByHourPeriodsGenerator
{
    public static function process($datePeriod, $hourPeriods, User $user, $isAdmin = false)
    {
        $dataFiller = new LitersByHourPeriodsDataFiller($datePeriod, $hourPeriods, $user, $isAdmin, new LitersByHourPeriodsReport());
        $report = $dataFiller->process();
        return $report->getResult();
    }
}
