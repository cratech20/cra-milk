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

        $yesterday = Carbon::yesterday();
        $today = Carbon::now();

        $data = $this->data = DB::connection('pgsql')->table('iot_events')
            ->whereBetween('event_datetime', [$yesterday, $today])
            ->whereNotNull('payload->c')
            ->whereNotNull('payload->i')
            ->whereNotNull('payload->l')
            ->whereNotNull('payload->t')
            ->whereNotNull('payload->y')
            ->whereIn('payload->l', $deviceNames)
            ->get();

        $result = '';

        // преобразовать дату во время 235714 $row[4]
        // считать время с первой дойки $row[0]
        $db = [
            [5, "8A20064B40", 3, 5.6, 235714],
            [13, "8A20064B41", 3, 8.2, 235806],
            [13, "6A20064B40", 3, 8.2, 235806],
        ];

        $cowIdNew = hexdec(strrev($row[1])) % 100000;

        foreach ($db as $row) {
            $startTime = str_pad($row[0], 6, '0', STR_PAD_LEFT);
            $cowId = str_pad($cowIdNew, 7, ' ', STR_PAD_LEFT);
            $groupId = str_pad($row[2], 4, ' ', STR_PAD_LEFT);
            $stall = str_pad(0, 5, ' ', STR_PAD_LEFT);
            $yieldValue = str_pad($row[3], 7, ' ', STR_PAD_LEFT);
            $yieldDuration = str_pad(0, 6, ' ', STR_PAD_LEFT);
            $identTime = str_pad($row[4], 8, ' ', STR_PAD_LEFT);
            $yieldTime = str_pad($row[4], 7, ' ', STR_PAD_LEFT);
            $flow = str_pad(0, 6, ' ', STR_PAD_LEFT);
            $electricalConductivity = str_pad(0, 5, ' ', STR_PAD_LEFT);
            $transponderNumber = str_pad(0, 9, ' ', STR_PAD_LEFT);
            $endLine = " 0 ???????? 000000\n";
            echo $startTime . $cowId . $groupId . $stall . $yieldValue . $yieldDuration . $identTime
                . $yieldTime . $flow . $electricalConductivity . $transponderNumber . $endLine;
        }

        return response()->streamDownload(function () use ($result) {
            echo $result;
        }, '220721.mlk');
    }
}
