<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YaCloud\YaCloud;
use App\Models\Gate;

class gatedevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gatedevice:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh gatedevices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $yacloud = new YaCloud();
        $clouds = $yacloud->getCloudId();
        foreach($clouds as $cloud) {
            $folders = $yacloud->getFolders($cloud->id);
        };
        foreach($folders as $folder) {
            $registries = $yacloud->getRegistryList($folder->id);
        };
        foreach($registries as $registry) {
            $devicesList = $yacloud->getDevicesList($registry->id);
        };
        foreach($devicesList as $device) {
            if (isset($device->description) && (strpos($device->description, 'Gatedevice') !== false)) {
                $gates[] = $device;
            };
        };

        foreach ($gates as $gate) {
            $gateDb = Gate::count();
            // dd($gateDb);
            if ($gateDb === 0) {
                Gate::create([
                    'name' => $gate->name,
                    'device_id' => $gate->id,
                    'registry_id' => $gate->registryId,
                    'description' => $gate->description,
                    'created_at' => $gate->createdAt
                ]);
            } else {
                $gateItem = Gate::where('device_id', $gate->id)->first();
                if (!$gateItem) {
                    Gate::create([
                        'name' => $gate->name,
                        'device_id' => $gate->id,
                        'registry_id' => $gate->registryId,
                        'description' => $gate->description,
                        'created_at' => $gate->createdAt
                    ]);
                };
            };

        };
        return 0;
    }
}
