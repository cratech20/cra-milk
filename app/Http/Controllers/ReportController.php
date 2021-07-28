<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Models\User;
use App\Services\ReportGenerator;
use App\Services\Reports\Generators\LitersByHourPeriodsGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function liters(Request $request)
    {
        $result = LitersByHourPeriodsGenerator::process([], [], auth()->user(), !auth()->user()->hasRole('client'));

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-liter-cow-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3]);
    }

    public function litersByHour(Request $request)
    {
        $hourPeriods = [
            '12:00:00',
        ];

        $result = LitersByHourPeriodsGenerator::process([], $hourPeriods, auth()->user(), !auth()->user()->hasRole('client'));

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-lit-cow-h-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3, 'stickyCol' => 1]);
    }

    public function litersByDevice(Request $request)
    {
        $result = ReportGenerator::getLitersByDevice();

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-liter-device-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'downloadRoute' => 'reports.liters.device']);
    }

    public function impulse(Request $request)
    {
        $result = ReportGenerator::getImpulsesByCow();

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-impulse-cow-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3, 'downloadRoute' => 'reports.impulse']);
    }

    public function impulseByDevice(Request $request)
    {
        $result = ReportGenerator::getImpulsesByDevice();

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-impulse-device-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'downloadRoute' => 'reports.impulse.device']);
    }

    public function mlk()
    {
        $user = User::find(4);

        $deviceNames = $user->devices->pluck('device_id');

        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        $today = Carbon::now();

        $data = DB::connection('pgsql')->table('iot_events')
            ->whereBetween('event_datetime', [$yesterdayStart, $yesterdayEnd])
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->i')
            ->whereNotNull('payload->l')
            ->whereNotNull('payload->t')
            ->whereNotNull('payload->y')
            ->whereIn('payload->l', $deviceNames)
            ->get();

        $result = '';

        foreach ($data as $key => $rowDB) {
            $row = json_decode($rowDB->payload, true);
            $cowId = hexdec(strrev($row['c'])) % 100000;

            // TODO считать время с первой дойки
            $date = Carbon::parse((int)$row['t']);
            $time = $date->format('His');
            $stopTime = $time;

            $stopTimeStr = str_pad($stopTime, 6, '0', STR_PAD_LEFT);
            $cowIdStr = str_pad($cowId, 7, ' ', STR_PAD_LEFT);
            $groupId = str_pad(111, 4, ' ', STR_PAD_LEFT);
            $stall = str_pad(0, 5, ' ', STR_PAD_LEFT);
            $yieldValue = str_pad(round($row['y'], 1), 7, ' ', STR_PAD_LEFT);
            $yieldDuration = str_pad(0, 6, ' ', STR_PAD_LEFT);
            $identTime = str_pad($time, 8, ' ', STR_PAD_LEFT);
            $yieldTime = str_pad($time, 7, ' ', STR_PAD_LEFT);
            $flow = str_pad(0, 6, ' ', STR_PAD_LEFT);
            $electricalConductivity = str_pad(0, 5, ' ', STR_PAD_LEFT);
            $transponderNumber = str_pad(0, 9, ' ', STR_PAD_LEFT);
            $endLine = " 0 ???????? 000000\n";
            $result .= $stopTimeStr . $cowIdStr . $groupId . $stall . $yieldValue
                . $yieldDuration . $identTime . $yieldTime . $flow
                . $electricalConductivity . $transponderNumber . $endLine;
        }

        return response()->streamDownload(function () use ($result) {
            echo $result;
        }, $today->format('dmy') . '.mlk');
    }
}
