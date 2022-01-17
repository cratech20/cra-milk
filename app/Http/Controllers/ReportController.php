<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Models\Cow;
use App\Models\DeviceMessage;
use App\Models\User;
use App\Services\ReportGenerator;
use App\Services\Reports\Generators\LitersByHourPeriodsDeviceGenerator;
use App\Services\Reports\Generators\LitersByHourPeriodsGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function index()
    {
        // echo 'Тут будут все отчеты, но пока они на главной ЛК';
        return back();
    }

    public function getDayReport()
    {
        // Cache::forget('chartDataAr');
        $value = Cache::get('chartDataAr');
        // dd($value);
        $data = [
            'date' => Carbon::parse($value[0]['date2'])->format('d.m.Y'),
            'value' => $value
        ];
        return response()->json($data);
    }

    public function getDayReport7()
    {
        $dates = 7;
        $value = Cache::get('chartDataAr7');
        for ($i = 0; $i <= $dates; $i++) {
            $dateAr[] = Carbon::now()->subDays($i)->format('d.m.Y');
        }
        $result = LitersByHourPeriodsGenerator::process([], [], auth()->user(), !auth()->user()->hasRole('client'), 'liters');
        dd($result);
        $data = [
            'dates' => $dateAr,
            'value' => $value
        ];
        return response()->json($data);
    }

    public function getDayReport30()
    {
        $dates = 30;
        $value = Cache::get('chartDataAr7');
        for ($i = 0; $i <= $dates; $i++) {
            $dateAr[] = Carbon::now()->subDays($i)->format('d.m.Y');
        }
        $data = [
            'dates' => $dateAr,
            'value' => $value
        ];
        return response()->json($data);
    }

    public function liters(Request $request)
    {
        // dd("Ok");
        $result = LitersByHourPeriodsGenerator::process([], [], auth()->user(), !auth()->user()->hasRole('client'), 'liters');
        // dd("Ok");
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

    public function litersByHour2(Request $request)
    {
        $hourPeriods = [
            '10:00:00',
            '15:00:00',
        ];

        $result = LitersByHourPeriodsGenerator::process([], $hourPeriods, auth()->user(), !auth()->user()->hasRole('client'), 'hour');

        if ($request->input('download')) {
            return Excel::download(new ExportReport($result), 'report-lit-cow-h-' . date('H-i_d-m-y') . '.xlsx');
        }

        return view('reports.bi', ['data' => $result, 'groupColumn' => 3, 'stickyCol' => 1]);
    }

    public function litersByDevice(Request $request)
    {
        $result = LitersByHourPeriodsDeviceGenerator::process([], [], auth()->user(), !auth()->user()->hasRole('client'), 'liters');

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
        $user = User::find(3);

        $cowCodes = $user->cows->pluck('cow_id');

        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        $today = Carbon::now();

        $data = DeviceMessage::whereBetween('device_created_at', [$yesterdayStart, $yesterdayEnd])
            ->whereIn('cow_code', $cowCodes)
            ->get();

        $result = '';

        foreach ($data as $row) {
            $cowId = Cow::getNumberByCode($row->cow_code);

            // TODO считать время с первой дойки
            $date = Carbon::parse($row->device_created_at);
            $time = $date->format('His');
            $stopTime = $time;

            $stopTimeStr = str_pad($stopTime, 6, '0', STR_PAD_LEFT);
            $cowIdStr = str_pad($cowId, 7, ' ', STR_PAD_LEFT);
            $groupId = str_pad(111, 4, ' ', STR_PAD_LEFT);
            $stall = str_pad(0, 5, ' ', STR_PAD_LEFT);
            $yieldValue = str_pad(round($row->yield, 1), 7, ' ', STR_PAD_LEFT);
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
        }, $yesterdayStart->format('dmy') . '.mlk');
    }
}
