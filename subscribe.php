<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/SimpleLogger.php';

use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\UnexpectedAcknowledgementException;
use PhpMqtt\Client\MQTTClient;
use Psr\Log\LogLevel;

pcntl_async_signals(true);

$server = "host.docker.internal";
$port = 1883;
$client_id = "test-subscriber";

$mqtt = new MQTTClient($server, $port, $client_id, null, null, new SimpleLogger(LogLevel::INFO));

$counter = 0;

connect($mqtt);
subscribe($mqtt);
pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
    $mqtt->interrupt();
});
try {
    $mqtt->loop(true);
} catch (UnexpectedAcknowledgementException $e) {
    echo 'Unexpected acknowledgement received:' . PHP_EOL . '  ' . $e->getMessage();
} catch (DataTransferException $e) {
    echo 'A data transfer error occured:' . PHP_EOL . '  ' . $e->getMessage();
}
close($mqtt);

function connect(MQTTClient $mqtt): void
{
    try {
        $mqtt->connect(null, null, null, true);
    } catch (ConnectingToBrokerFailedException $e) {
        echo 'Connecting to broker failed.';
    }
}

function close(MQTTClient $mqtt): void
{
    try {
        $mqtt->close();
    } catch (DataTransferException $e) {
        echo 'Closing the connection failed due to a data transfer error:' . PHP_EOL . '  ' . $e->getMessage();
    }
}

function subscribe(MQTTClient $mqtt): void
{
    try {
        $mqtt->subscribe('php-mqtt/client/#', function ($topic, $message) use (&$counter) {
            $counter++;
            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);

            if ($counter >= 50) {
                echo "Counter reached {$counter} messages.\n";
            }
        }, MQTTClient::QOS_AT_MOST_ONCE);
    } catch (DataTransferException $e) {
        echo 'Subscribing to a topic failed due to a data transfer error:' . PHP_EOL . '  ' . $e->getMessage();
    }
}
