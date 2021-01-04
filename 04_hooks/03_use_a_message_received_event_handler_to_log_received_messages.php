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
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_PORT, 'test-subscriber', MqttClient::MQTT_3_1, null, $logger);

    // Connect to the broker without specific connection settings but with a clean session.
    $client->connect(null, true);

    // Register an event handler which is called whenever a message is received. In our case, we log every received message.
    $handler = function (MqttClient $client, string $topic, string $message, int $qualityOfService, bool $retained) use ($logger) {
        $logger->info('Received message on topic [{topic}] with QoS {qos}: {message}', [
            'topic' => $topic,
            'message' => $message,
            'qos' => $qualityOfService,
        ]);

        // In this example, we interrupt the loop upon receiving the first message to avoid infinite blocking.
        $client->interrupt();
    };
    $client->registerMessageReceivedEventHandler($handler);

    // Subscribe to all topics starting with 'foo/' using QoS 0.
    $client->subscribe('foo/#');

    // Start the event loop to receive messages on subscribed topics.
    $client->loop(true);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Subscribing to a topic using QoS 0 failed. An exception occurred.', ['exception' => $e]);
}
