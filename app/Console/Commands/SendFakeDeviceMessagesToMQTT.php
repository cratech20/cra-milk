<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\MqttClient;

class SendFakeDeviceMessagesToMQTT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:send-fake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @throws ConfigurationInvalidException
     * @throws ConnectingToBrokerFailedException
     * @throws DataTransferException
     * @throws ProtocolNotSupportedException
     * @throws RepositoryException
     *
     * Message type:
     *
     */
    public function handle()
    {
        $server = '81.163.31.29';
        $port = 1883;
        $clientId = 'device5';
        $username = 'device5';
        $password = '1d56d6f49779';
        $connectionSettings = (new ConnectionSettings())
            ->setUsername($username)
            ->setPassword($password);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($connectionSettings);
        $topic = sprintf('milk_device/%s/data', 'device4');
        $faker = Factory::create();
        $b = $faker->numberBetween(1, 99);
        $data = [
            'c' => '7DDD5146F0', // 10 - hex 16
            't' => Carbon::now()->format('U'),
//            't' => 1656953507,
            'y' => 13607,
            'i' => 8559,
            'n' => '27',
            'b' => $b,
            'ar' => null,
            'e' => "1",
        ];
        $message = json_encode($data);
        $mqtt->publish($topic, $message);
        $mqtt->disconnect();

        return 0;
    }
}
