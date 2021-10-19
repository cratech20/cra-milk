<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YaCloud\YaCloud;
use App\Models\Device;

class milkdevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'milkdevice:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh milkdevices';

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
            if (isset($device->description) && (strpos($device->description, 'Milkdevice') !== false)) {
                $devices[] = $device;
            };
        };

        foreach ($devices as $device) {
            $gateDb = Device::count();
            // dd($gateDb);
            if ($gateDb === 0) {
                Device::create([
                    'name' => $device->name,
                    'device_id' => $device->id,
                    'registry_id' => $device->registryId,
                    'password' => $device->description,
                    'serial_number' => str_replace('Milkdevice', '', $device->description),
                    'created_at' => $device->createdAt
                ]);
            } else {
                $gateItem = Device::where('device_id', $device->id)->first();
                if (!$gateItem) {
                    Device::create([
                        'name' => $device->name,
                        'device_id' => $device->id,
                        'registry_id' => $device->registryId,
                        'password' => $device->description,
                        'serial_number' => str_replace('Milkdevice', '', $device->description),
                        'created_at' => $device->createdAt
                    ]);
                };
            };

        };
        return 0;
    }
}
