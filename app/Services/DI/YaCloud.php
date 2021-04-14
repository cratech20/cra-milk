<?php


namespace App\Services\DI;


use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class YaCloud
{
    private $token;

    private function auth(): void
    {
        $this->saveToken($this->getToken($this->getJWT()));
    }

    private function getJWT(): string
    {
        $config = config('services.ya_cloud');
        $serviceAccountId = $config['account_id'];
        $keyId = $config['key_id'];

        $algorithmManager = new AlgorithmManager([
            new PS256()
        ]);

        $jwsBuilder = new JWSBuilder($algorithmManager);

        $now = time();

        $claims = [
            'aud' => 'https://iam.api.cloud.yandex.net/iam/v1/tokens',
            'iss' => $serviceAccountId,
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $header = [
            'alg' => 'PS256',
            'typ' => 'JWT',
            'kid' => $keyId
        ];

        $file = Storage::path($config['private_pem']);

        $key = JWKFactory::createFromKeyFile($file);

        $payload = json_encode($claims, JSON_THROW_ON_ERROR);

        // Формирование подписи.
        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($key, $header)
            ->build();

        $serializer = new CompactSerializer();

        // Формирование JWT.
        return $serializer->serialize($jws);
    }

    private function getToken($jwt)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://iam.api.cloud.yandex.net/iam/v1/tokens");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            '{"jwt": "' . $jwt . '"}');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }

    private function saveToken($result): void
    {
        settings()->set('ya_cloud_iam_token', $result['iamToken']);
        settings()->set('ya_cloud_expires', Carbon::parse($result['expiresAt'])->timestamp);
        $this->token = $result['iamToken'];
    }

    public function registerServiceProvider(): void
    {
        $log = new Logger('ya.cloud');
        $log->pushHandler(new StreamHandler(storage_path() . '/logs/ya-cloud.log', Logger::DEBUG));
        // $log->error('log');

        try {
            $this->token = settings('ya_cloud_iam_token');

            if ($this->token !== null) {
                if (time() >= (settings('ya_cloud_expires', 0) - 300)) {
                    $this->auth();
                }
            } else {
                $this->auth();
            }

        } catch (QueryException $e) {
            echo 'QueryException settings table';
        }
    }

    public function createDevice()
    {
        $test = '{
                      "registryId": "string",
                      "name": "string",
                      "description": "string",
                      "certificates": [
                        {
                          "certificateData": "string"
                        }
                      ],
                      "topicAliases": "object",
                      "password": "string"
                    }';

        $data = [
            'registryId' => 'arerbdnuk54prrjanos2',
            'name' => 'new'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://iot-devices.api.cloud.yandex.net/iot-devices/v1/devices");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        dd($result);
    }

    public function call($url, $data, $method = 'GET')
    {

        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
        } else {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}