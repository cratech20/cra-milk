<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Cow;
use App\Models\DeviceMessage;

class reports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cache reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function calc($deviceId, $liters, $impulses, $date)
    {
        if ($impulses === 0 || !isset($this->devices[$deviceId])) {
            return $liters;
        }

        $weight = $this->devices[$deviceId]->weight;
        $weightSetDate = Carbon::parse($this->devices[$deviceId]->weight_set_at);

        if ($weight === null || $weightSetDate === null || $date->lte($weightSetDate)) {
            return $liters;
        }

        return $impulses * $weight / 1000;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Cache::remember('chartDataAr', 3600, function () {
        //     $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
        //         ->whereNotNull('payload->ar')
        //         ->whereNotNull('payload->c')
        //         ->where('event_datetime', 'like', Carbon::now()->format('Y-m-d').'%')
        //         ->orderBy('event_datetime', 'DESC')
        //         ->limit(300)
        //         ->get();

        //     if (count($chartDataAr) > 0) {
        //         foreach ($chartDataAr as $item) {
        //             $payload = json_decode($item->payload);
        //             $cow = Cow::where('cow_id', $payload->c)->first();
        //             if ($cow) {
        //                 $d[] = [
        //                     'date2' => Carbon::now()->format('Y-m-d'),
        //                     'code' => $payload->c,
        //                     'num' => $cow['internal_code'],
        //                     'num5' => $cow->getNumberByCode($cow['cow_id']),
        //                     'date' => Carbon::parse($item->event_datetime)->format('d.m.Y')
        //                 ];
        //             }
        //         };
        //     } else {
        //         $d[] = [
        //             'date2' => Carbon::now()->format('Y-m-d')
        //         ];
        //     }

        //     return $d;
        // });

        Cache::remember('chartDataAr', 3600, function () {
            $chartDataAr = DB::connection('pgsql_milk')->table('iot_events')
                ->whereNotNull('payload->ar')
                ->whereNotNull('payload->c')
                ->whereNotNull('payload->i')
                ->whereNotNull('payload->l')
                ->whereNotNull('payload->t')
                ->whereNotNull('payload->y')
                // ->where('event_datetime', 'like', Carbon::now()->format('Y-m-d').'%')
                ->orderBy('event_datetime', 'DESC')
                ->get();

            if (count($chartDataAr) > 0) {
                foreach ($chartDataAr as $k => $item) {
                    $payload = json_decode($item->payload);
                    $cow = Cow::where('cow_id', $payload->c)->first();
                    $end = Carbon::now()->subDays(7);
                    $today =  Carbon::now();
                    if ($cow) {
                        // $carbonDate = Carbon::parse($row->device_created_at);
                        // $liters = $this->calc($payload->l, $payload->liters, $payload->i, $carbonDate);
                        if (Carbon::parse($item->event_datetime)->diffInDays($today) < 7) {
                            $d[] = [
                                'date2' => Carbon::now()->format('Y-m-d'),
                                'code' => $payload->c,
                                'num' => $cow['internal_code'],
                                'num5' => $cow->getNumberByCode($cow['cow_id']),
                                'date' => Carbon::parse($item->event_datetime)->format('d.m.Y')
                            ];
                        }
                    }
                };
            } else {
                $d[] = [
                    'date2' => Carbon::now()->format('Y-m-d')
                ];
            }

            return $d;
        });
    }
}
