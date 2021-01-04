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

    // Register an event handler which is run once per loop iteration. For demonstration purposes, we will fetch
    // a random user object from the random-data-api.com API and publish it. We only do so once every five minutes though.
    $lastPublish = 0;
    $client->registerLoopEventHandler(function (MqttClient $client, float $elapsedTime) use ($logger, &$lastPublish) {
        // If we fetched a user within the last five minutes, we skip this execution.
        if ($lastPublish + 300 > time()) {
            return;
        }

        // We fetch a random user JSON object as string.
        $randomUser = file_get_contents('https://random-data-api.com/api/users/random_user');

        // Before publishing the details, we make sure the fetching actually worked.
        if ($randomUser === false) {
            $logger->error('Fetching random user from the random-data-api.com API failed.');

            return;
        }

        // The user details are published on the topic 'random/user' as json object, just like we fetched it.
        $client->publish('random/user', $randomUser, MqttClient::QOS_AT_MOST_ONCE);

        // Also, we need to update the timestamp of our last random user fetch.
        $lastPublish = time();

        // After half an hour, we will interrupt the client to stop publishing random user details.
        if ($elapsedTime >= 1800) {
            $client->interrupt();
        }
    });

    // Now, all we have to do is start the event loop. It will run our registered callback once per iteration.
    $client->loop(true);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Running the loop with a loop event handler failed. An exception occurred.', ['exception' => $e]);
}
