<?php

namespace App\Http\Controllers;

use App\Services\DI\YaCloud;
use App\Services\YaCloud\Device;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Easy\Build;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Facades\DaDataCompany;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param YaCloud $yaCloud
     * @return void
     */
    public function testCreateDevice(YaCloud $yaCloud)
    {
        // $data = [
        //     'registryId' => 'arerbdnuk54prrjanos2',
        // ];
        //
        // $device = new Device($yaCloud);
        // dd($device->list($data));

        $data = [
            'registryId' => 'arerbdnuk54prrjanos2',
            "name" => "obj_id_2",
            "description" => "auto register from milk.cra",
            // "password" => "Obj_id_1_Obj_id_1_Obj_id_1_Obj_id_1_",
        ];

        $device = new Device($yaCloud);
        dd($device->create($data));
    }

    public function testINN()
    {
        $dadata = DaDataCompany::id('4312145337', 1, null, BranchType::MAIN, CompanyType::LEGAL);

        $result = $dadata['suggestions'][0]['data'] ?? null;
        dd($result);
        /**
         * [
         * 'inn' => $data['inn'],
         * 'district_id' => $data['district_id'],
         * 'phone' => $data['phone'],
         * 'name' => $innInfo['value'],
         * 'kpp' => $innInfo['data']['kpp'] ?? 0,
         * 'ogrn' => $innInfo['data']['ogrn'],
         * 'full_with_opf' => $innInfo['data']['name']['full_with_opf'],
         * 'management_name' => $innInfo['data']['management']['name'] ?? '',
         * 'management_post' => $innInfo['data']['management']['post'] ?? '',
         * ]
         */
    }

    public function testJSON()
    {
        $data = [];

        $devices = [];

        for ($i = 0; $i < 15; $i++) {
            $devices[] = substr(str_shuffle(MD5(microtime())), 0, 20);
        }

        for ($j = 0; $j < 2; $j++) {
            for ($i = 0; $i < 30; $i++) {
                foreach ($devices as $device) {
                    $date = Carbon::now()->add($i, 'day')->add($j * 6, 'hour')->add(random_int(0, 59), 'second');
                    $milk = random_int(50, 400) / 10;
                    $data[] = ['l' => $device, 'd' => $date->timestamp, 'y' => $milk];
                    // $data[] = ['l' => $device, 'd' => $date->format('Y-m-d H:i:s'), 'y' => $milk];
                }
            }
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
