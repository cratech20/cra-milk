<?php

namespace App\Http\Controllers;

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
     * @return Renderable
     */
    public function test()
    {

        $service_account_id = 'aje3fqmmmh5vliohmgg0';
        $key_id = 'ajedrlm7s8re9hsehaoi';

        $pem = '----BEGIN PRIVATE KEY-----
MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQDabwc4H+Qskxe5
OpYLK122l4d/WQkDFKuH7ItLFwmEQCgQbaZcxOrxeDpwp4XRL+Rk20QjlXP1JjdK
8JEsFE2+kt1NWFtT6saQP+qcUN7fYbWmK2GfgrxSXZazRkoyY2u6oyqp0E0JfPZ4
56fE2fLIgLpy0oEZZR0fdOjrwQzADB4eWfTx1bN2PBiCnulHBJw/U2kEz/JJX3US
ZS7nUxWrR1nnjWXlDukTUHXJMZ0uIQnLHmhfM2qDXSSOUZseizQH9kYxmgEKENy7
D7OeucJyri8TI63r+NWrqGW7l/jt0uTAmHD9r7aU/poYPxPesRjn9DEsU8QDMHXQ
YYEOj5znAgMBAAECggEBAIS4KV7EodMjnKqDGl+2/b3P3BhYdM1WZdK0uv3pBXNB
uBYjSY3caBRHxNpmLzynKNLPF1u3lzlA+x+hg9OQkpxUh/pS0Urvv64t59MKoCCC
MejBOxO8T8iL7OEIuFRdbDt+oJGFawl00B0uRBWrh4SkEqVpmd9gPI28bzmvlpuo
occT1zKdhF8bYi2hj3T8tVT36+nirZGklUFkepbT5DuvCYel62gakmAAhRls+m2m
v3oDqED+caDsLkjGUgJjwLoeKxx8cIiOWBsDz7G2HIy9tDZduQWUW5x5qMurAFAu
PXY1SasCaqGzyCtuKPbBQ8d2ISzIbeKKhGsR0jKDikECgYEA8Xs2+XaEZv+ytWrU
qWQwOepWYOePYQZr4cG/TI9jNTnwYibWsn+U57sXOqQh7cjxP2AAlupp/FZDRD/X
I7i3EYK/e5SzHYEvLxdkvd6MRQZ0sOPunyN19o5rsgsOsgvyNNVZeZTv0VjA7JO6
MQpzavK/IXktz6+cgAk/bHhr0ykCgYEA55ES5TjJUgoRv9E6T8mnIp+U5y/AOUk2
Vy3RWAXp5BjbSwdbuYD0N1TCUICEy6PSlvTUaTNSrxzJIkl1vhXxvUP6+AKB8rNY
beGeFa4JWEVaiA8M16CkmlhS3FZt/hyy5xOGWqTsgG3OKZNKZ+2BUkA7xN/BNXH/
QLQJpHAlgY8CgYEA3NyqMk4w2RfDNuSn7uogpcfsCyOfYsmBwHD5a6e1wEfm0Y7e
TVIdOjlHDK50Dcz8wc8nmuB54X5wdarCNfzLC6v/QKOHk1PFeRe3X2V9+9/kuKkw
ZcMEMGagn643WadNdv8vn+EV2u6HPZn78MCzQb0WDSKNQOhIhl5u8OHPFMkCgYEA
rdMFtxFgdfffCPka54e8sU3PLZd3mMjvRJO9IHEX+EP8YR696Mr3B43Vay5+AIsR
6oUP6YRAHfjitp0kCDNayjL8ci0XtMKRr7H7w9RnaV5uLdH2VupchQul74XlfSm3
1aOfZ+2DJojGiAjrgoYTXFWhSBCabO8Mp1o+HrScQtsCgYEAo8KfUh/TzLoDjWEC
akhgLVzyw8ksYUM+NJCENtZbAtuiZsZgd4R/YTGfBGb3hrQR4iTz9j2/CtNNwbX4
vbTmQqv+pwixLdmPHkV89sKS5J3pKUjfc/nSv5ydo3d94JTZn9cZzzI0xE0x0N+4
A8hYb4tLJfPzm2nQn9P5YKK8foQ=
-----END PRIVATE KEY-----';

        // $jsonConverter = new StandardConverter();
        $algorithmManager = new AlgorithmManager([
            new PS256()
        ]);

        $jwsBuilder = new JWSBuilder($algorithmManager);

        $now = time();

        $claims = [
            'aud' => 'https://iam.api.cloud.yandex.net/iam/v1/tokens',
            'iss' => $service_account_id,
            'iat' => $now,
            'exp' => $now + 360
        ];

        $header = [
            'alg' => 'PS256',
            'typ' => 'JWT',
            'kid' => $key_id
        ];

        $file = Storage::path('private.pem');

        $key = JWKFactory::createFromKeyFile($file);

        $payload = json_encode($claims);

// Формирование подписи.
        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($key, $header)
            ->build();

        $serializer = new CompactSerializer();

// Формирование JWT.
        $token = $serializer->serialize($jws);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://iam.api.cloud.yandex.net/iam/v1/tokens");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            '{"jwt": "' . $token . '"}');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        dd($server_output);
    }
}
