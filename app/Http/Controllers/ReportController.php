<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Models\Device;
use App\Models\User;
use App\Services\ReportGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @deprecated не применяется
     */
    public function index()
    {
        $json = Storage::get('milk.json');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $result = [];

        $dates = [];
        $header = ['ID'];
        // TODO переделать 30 дней подряд формировать, а не брать из джсон
        foreach ($data as $row) {
            // TODO учитывать временные зоны
            $carbonDate = Carbon::parse($row['d']);
            $date = $carbonDate->format('Ymd');
            // $dates[$date] = $date;
            $header[$date] = $carbonDate->format('d.m.y');
        }

        $litersByDay = [];
        foreach ($data as $row) {
            $id = $row['l'];
            $date = Carbon::parse($row['d'])->format('Ymd');
            $litersByDay[$id][$date] = ($litersByDay[$id][$date] ?? 0) + $row['y'];
        }

        foreach ($data as $row) {
            $id = $row['l'];
            $date = Carbon::parse($row['d'])->format('Ymd');
            $liters = $litersByDay[$id][$date] ?? 0;
            $result[$id] = $result[$id] ?? [$id];
            $result[$id][$date] = $liters;

        }

        array_unshift($result, $header);

        foreach ($result as &$row) {
            $row = array_values($row);
        }

        $result = array_values($result);

        $result = json_encode($result, JSON_THROW_ON_ERROR);

        return view('reports.index', ['data' => $result]);
    }

    public function liters(Request $request)
    {
        $result = ReportGenerator::getLitersByCow();

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-liter-cow-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3, 'downloadRoute' => 'reports.liters']);
    }

    public function litersByHour(Request $request)
    {
        $result = ReportGenerator::getLitersByHour();

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-lit-cow-h-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3, 'downloadRoute' => 'reports.liters', 'stickyCol' => 1]);
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
