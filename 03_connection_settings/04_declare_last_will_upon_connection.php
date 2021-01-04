<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../shared/config.php';

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Examples\Shared\SimpleLogger;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Psr\Log\LogLevel;

// Create an instance of a PSR-3 compliant logger. For this example, we will also use the logger to log exceptions.
$logger = new SimpleLogger(LogLevel::INFO);

try {
    // Create a new instance of an MQTT client and configure it to use the shared broker host and port.
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_PORT, 'test-publisher', MqttClient::MQTT_3_1, null, $logger);

    // Create and configure the connection settings as required. The provided last will is the message the broker will publish
    // on behalf of the client when the client disconnects abnormally (i.e. no graceful disconnect).
    $connectionSettings = (new ConnectionSettings)
        ->setLastWillTopic('test/client/test-publisher')
        ->setLastWillMessage('offline')
        ->setLastWillQualityOfService(MqttClient::QOS_AT_LEAST_ONCE)
        ->setRetainLastWill(true);

    // Connect to the broker with the configured connection settings and with a clean session.
    $client->connect($connectionSettings, true);

    // Publish the message 'online' on the topic 'test/client/test-publisher' using QoS 1.
    $client->publish('test/client/test-publisher', 'online', MqttClient::QOS_AT_LEAST_ONCE);

    // Do not terminate the connection to the broker gracefully, to trigger publishing of our last will.
    //$client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Connecting with last will or publishing online state with QoS 1 failed. An exception occurred.', ['exception' => $e]);
}
