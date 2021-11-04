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

// Before initializing the MQTT client, we prepare a request and a default result. This example will try to call a fictitious echo command,
// which is supposed to respond with the payload sent to it.
// To simplify things, we assume failure of the remote procedure call at this point. Therefore, the result does not indicate success just yet.
$request = [
    'id' => base64_encode(random_bytes(10)),
    'command' => 'echo',
    'payload' => 'Hello World!',
];
$result = ['success' => false];

try {
    // Create a new instance of an MQTT client and configure it to use the shared broker host and port.
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_PORT, 'test-client', MqttClient::MQTT_3_1, null, $logger);

    // Connect to the broker without specific connection settings but with a clean session.
    $client->connect(null, true);

    // Register a loop event handler which interrupts the loop after 10 seconds without response to our request.
    $client->registerLoopEventHandler(function (MqttClient $client, float $elapsedTime) {
        if ($elapsedTime >= 10) {
            $client->interrupt();
        }
    });

    // Subscribe to the response topic. This is where the receiver of the remote procedure call is supposed to answer with a response.
    // Since the result of the remote procedure call is needed outside the callback, it is important to pass the variable by reference.
    $client->subscribe('rpc/response/#', function (string $topic, string $message) use ($client, $request, &$result) {
        // The entire callback logic is just an example. Parsing and processing the response depends on the individual project.
        $json = json_decode($message, true);

        // In our fictitious example, the response json contains an array with the related command id as well as a response payload.
        // If the command id in the response matches our request id, this is the response we are looking for.
        if ($json['relates_to'] === $request['id']) {
            // We can therefore set the result now to success and pass the payload.
            $result['success'] = true;
            $result['payload'] = $json['payload'];

            // In this example, we interrupt the loop upon receiving the response to our remote procedure call.
            $client->interrupt();
        }
    });

    // Here we publish the remote procedure call on the topic where the other client, which will respond to it, is listening for requests.
    $client->publish('rpc/request', json_encode($request));

    // Since subscribing requires waiting for messages, we need to start the client loop which takes care of receiving,
    // parsing and delivering messages to the registered callbacks. The loop will run until a response to our request is received
    // or a timeout occurs after 10 seconds. In this case, the registered loop event handler will interrupt the loop for us.
    $client->loop(true);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Subscribing to a topic failed. An exception occurred.', ['exception' => $e]);
}

// Finally, the result can be used for whatever it is intended.
if ($result['success'] === true) {
    $logger->info('Remote procedure call ended with success. Response payload: {payload}', ['payload' => $result['payload']]);
} else {
    $logger->info('Remote procedure call failed. A timeout occurred. No response was received within 10 seconds.');
}
