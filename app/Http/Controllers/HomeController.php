<?php

namespace App\Http\Controllers;

use App\Services\DI\YaCloud;
use App\Services\YaCloud\Device;
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

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param YaCloud $yaCloud
     * @return void
     */
    public function test(YaCloud $yaCloud)
    {
        $data = [
            'registryId' => 'arerbdnuk54prrjanos2',
        ];

        $device = new Device($yaCloud);
        dd($device->list($data));
    }
}
