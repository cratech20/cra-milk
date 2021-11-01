<?php


namespace App\Services\Reports\Generators;

use App\Models\User;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDataFiller;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDataFiller2;
use App\Services\Reports\DataFillers\LitersByHourPeriodsDataFiller3;
use App\Services\Reports\LitersByHourPeriodsReport;
use App\Services\Reports\LitersByHourReport;
use App\Services\Reports\LitersPeriodsReport;

class LitersByHourPeriodsGenerator
{
    public static function process($datePeriod, $hourPeriods, User $user, $isAdmin = false, $report = '')
    {
        if ($report == '') {
            $dataFiller = new LitersByHourPeriodsDataFiller($datePeriod, $hourPeriods, $user, $isAdmin, new LitersByHourPeriodsReport());
            $report = $dataFiller->process();
        } elseif ($report == 'hour') {
            $dataFiller = new LitersByHourPeriodsDataFiller3($datePeriod, $hourPeriods, $user, $isAdmin, new LitersByHourReport());
            $report = $dataFiller->process();
        } else {
            $dataFiller = new LitersByHourPeriodsDataFiller2($datePeriod, $hourPeriods, $user, $isAdmin, new LitersPeriodsReport());
            $report = $dataFiller->process();
        }

        return $report->getResult();
    }
}
