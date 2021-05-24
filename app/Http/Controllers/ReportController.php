<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Services\ReportGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
}
