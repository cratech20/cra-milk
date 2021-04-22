<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $data = json_decode(Storage::get('milk.json'), true, 512, JSON_THROW_ON_ERROR);
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
}
