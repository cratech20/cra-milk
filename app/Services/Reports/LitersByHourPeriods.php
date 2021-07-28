<?php


namespace App\Services\Reports;


use App\Models\User;
use App\Services\Reports\Generators\LitersByHourPeriodsGenerator;

class LitersByHourPeriods
{
    public static function process(User $user, $isAdmin = false)
    {
        $generator = new LitersByHourPeriodsGenerator();

        $hourPeriods = [
            '12:00:00',
        ];

        return $generator->process($hourPeriods, $user, $isAdmin);
    }
}
