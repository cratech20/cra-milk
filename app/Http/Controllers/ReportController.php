<?php

namespace App\Http\Controllers;

use App\Exports\ExportBladeBasic;
use App\Models\Cow;
use App\Models\Device;
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

        $litresByDay = [];
        foreach ($data as $row) {
            $id = $row['l'];
            $date = Carbon::parse($row['d'])->format('Ymd');
            $litresByDay[$id][$date] = ($litresByDay[$id][$date] ?? 0) + $row['y'];
        }

        foreach ($data as $row) {
            $id = $row['l'];
            $date = Carbon::parse($row['d'])->format('Ymd');
            $litres = $litresByDay[$id][$date] ?? 0;
            $result[$id] = $result[$id] ?? [$id];
            $result[$id][$date] = $litres;

        }

        array_unshift($result, $header);

        foreach ($result as &$row) {
            $row = array_values($row);
        }

        $result = array_values($result);

        $result = json_encode($result, JSON_THROW_ON_ERROR);

        return view('reports.index', ['data' => $result]);
    }

    public function liters()
    {
        $result = ReportGenerator::getDataByLiters();
        return view('reports.bi', ['data' => $result]);
    }

    public function litersByDevice()
    {
        $deviceNames = Device::get(['name', 'device_id'])->pluck('name', 'device_id');
        $allCows = Cow::all()->keyBy('cow_id');

        // TODO Postgres
        $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        // $json = Storage::get('milk-bi.json');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $result = [];

        $result['head'] = ['Устройство'];

        // Начало и конец периода

        // $startPeriod = Carbon::now()->startOfMonth();
        // $endPeriod = Carbon::now()->endOfMonth();
        $startPeriod = Carbon::now();
        $endPeriod = Carbon::now()->subDays(30);

        $currentDay = clone $startPeriod;

        // заполняются даты для шапки
        $dates = [];
        while ($endPeriod->lessThanOrEqualTo($currentDay)) {
            $date = $currentDay->format('d.m.y');
            $dateKey = $currentDay->format('Ymd');
            $dates[$dateKey] = $date;
            $result['head'][] = $date;
            $currentDay = $currentDay->subDay();
        }

        // заполняются литры в день по коровам!

        $litresByDay = [];
        foreach ($data as $rowDirty) {
            $row = $rowDirty[3];

            if (!isset($row['y'], $row['c'])) {
                continue;
            }

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $litresByDay[$deviceId][$date] = ($litresByDay[$deviceId][$date] ?? 0) + $row['y'];
        }

        $body = [];

        foreach ($litresByDay as $deviceId => $volumes) {
            $deviceName = $deviceNames[$deviceId] ?? $deviceId;
            $body[$deviceId] = [$deviceName];

            foreach ($dates as $dateKey => $trash) {
                $body[$deviceId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        return view('reports.bi', ['data' => $result]);
    }

    public function impulse()
    {
        $deviceNames = Device::get(['name', 'device_id'])->pluck('name', 'device_id');
        $allCows = Cow::all()->keyBy('cow_id');

        // TODO Postgres
        $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        // $json = Storage::get('milk-bi.json');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $deviceByCow = [];
        $result = [];

        $result['head'] = ['Устройство', 'Корова', 'Группа'];

        // Начало и конец периода

        // $startPeriod = Carbon::now()->startOfMonth();
        // $endPeriod = Carbon::now()->endOfMonth();
        $startPeriod = Carbon::now();
        $endPeriod = Carbon::now()->subDays(30);

        $currentDay = clone $startPeriod;

        // заполняются даты для шапки
        $dates = [];
        while ($endPeriod->lessThanOrEqualTo($currentDay)) {
            $date = $currentDay->format('d.m.y');
            $dateKey = $currentDay->format('Ymd');
            $dates[$dateKey] = $date;
            $result['head'][] = $date;
            $currentDay = $currentDay->subDay();
        }

        // заполняются литры в день по коровам!

        $litresByDay = [];
        foreach ($data as $rowDirty) {
            $row = $rowDirty[3];

            if (!isset($row['y'], $row['c'])) {
                continue;
            }

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $cowId = $row['c'];
            $litresByDay[$cowId][$date] = ($litresByDay[$cowId][$date] ?? 0) + $row['i'];
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litresByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $deviceNames[$deviceId] ?? $deviceId;
            $cowName = $allCows[$cowId]->calculated_name ?? $cowId;
            $group = $allCows[$cowId]->group->calculated_name ?? 'Неизвестно';
            $body[$cowId] = [$deviceName, $cowName, $group];

            foreach ($dates as $dateKey => $trash) {
                $body[$cowId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        return view('reports.bi', ['data' => $result]);
    }

    public static function getLiters()
    {
        $deviceNames = Device::get(['name', 'device_id'])->pluck('name', 'device_id');
        $allCows = Cow::all()->keyBy('cow_id');

        // TODO Postgres
        $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        // $json = Storage::get('milk-bi.json');
        $json = preg_replace('|\\\\u0003|', '', $json);
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $deviceByCow = [];
        $result = [];

        $result['head'] = ['Устройство', 'Корова', 'Группа'];

        // Начало и конец периода

        // $startPeriod = Carbon::now()->startOfMonth();
        // $endPeriod = Carbon::now()->endOfMonth();
        $startPeriod = Carbon::now();
        $endPeriod = Carbon::now()->subDays(30);

        $currentDay = clone $startPeriod;

        // заполняются даты для шапки
        $dates = [];
        while ($endPeriod->lessThanOrEqualTo($currentDay)) {
            $date = $currentDay->format('d.m.y');
            $dateKey = $currentDay->format('Ymd');
            $dates[$dateKey] = $date;
            $result['head'][] = $date;
            $currentDay = $currentDay->subDay();
        }

        // заполняются литры в день по коровам!

        $litresByDay = [];
        foreach ($data as $rowDirty) {
            $row = $rowDirty[3];

            if (!isset($row['y'], $row['c'])) {
                continue;
            }

            $date = Carbon::parse((int)$row['t'])->format('Ymd');
            $deviceId = $row['l'];
            $cowId = (string)$row['c'];
            $litresByDay[$cowId][$date] = ($litresByDay[$cowId][$date] ?? 0) + $row['y'];
            $deviceByCow[$cowId] = $deviceId;
        }

        $body = [];

        foreach ($litresByDay as $cowId => $volumes) {
            $deviceId = $deviceByCow[$cowId];
            $deviceName = $deviceNames[$deviceId] ?? $deviceId;
            $cowName = $allCows[$cowId]->calculated_name ?? $cowId;
            $group = $allCows[$cowId]->group->calculated_name ?? 'Неизвестно';
            $body[$cowId] = [$deviceName, $cowName, $group];

            foreach ($dates as $dateKey => $trash) {
                $body[$cowId][] = $volumes[$dateKey] ?? 0;
            }

            $result['body'] = $body;
        }

        return $result;
    }
}
