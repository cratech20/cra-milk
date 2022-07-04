<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\MqttClient;

class SaveMqttMessagesToDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt-to-db:run';

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
     */
    public function handle()
    {
        $server = '81.163.31.29';
        $port = 1883;
        $clientId = 'device10';
        $username = 'device10';
        $password = 'e8ed0a45a0c3';
        $topicFilter = "milk_device/+/data/+"; // /milk_device/{deviceN}/data
        $connectionSettings = (new ConnectionSettings())
            ->setUsername($username)
            ->setPassword($password);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($connectionSettings);

        pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
            $mqtt->interrupt();
        });

        $mqtt->subscribe($topicFilter, function ($topic, $message) {
            $topicParts = explode('/', $topic);
            $deviceLogin = $topicParts[1] ?? null;
            // logs_enabled_to_file
            echo sprintf("Received message on topic [%s] (%s): %s\n", $topic, $deviceLogin, $message);
            // add to DB
        }, 0);
        $mqtt->loop(true);
        $mqtt->disconnect();

        return 0;
    }
}
