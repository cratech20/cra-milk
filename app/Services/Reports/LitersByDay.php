<?php


namespace App\Services\Reports;


use App\Models\User;
use App\Services\Reports\Generators\LitersByHourPeriodsGenerator;

class LitersByDay
{
    public static function process(User $user, $isAdmin)
    {
        $generator = new LitersByHourPeriodsGenerator();

        return $generator->process([], $user, $isAdmin);
    }
}
