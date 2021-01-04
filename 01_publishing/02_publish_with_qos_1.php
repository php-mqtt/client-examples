<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../shared/config.php';

use PhpMqtt\Client\Examples\Shared\SimpleLogger;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Psr\Log\LogLevel;

// Create an instance of a PSR-3 compliant logger. For this example, we will also use the logger to log exceptions.
$logger = new SimpleLogger(LogLevel::INFO);

try {
    // Create a new instance of an MQTT client and configure it to use the shared broker host and port.
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_PORT, 'test-publisher', MqttClient::MQTT_3_1, null, $logger);

    // Connect to the broker without specific connection settings but with a clean session.
    $client->connect(null, true);

    // Publish the message 'Hello world!' on the topic 'foo/bar/baz' using QoS 1.
    $client->publish('foo/bar/baz', 'Hello world!', MqttClient::QOS_AT_LEAST_ONCE);

    // Since QoS 1 requires the publisher to await confirmation and resend the message if no confirmation is received,
    // we need to start the client loop which takes care of that. By passing `true` as second parameter,
    // we allow the loop to exit as soon as all confirmations have been received.
    $client->loop(true, true);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Publishing a message using QoS 1 failed. An exception occurred.', ['exception' => $e]);
}
