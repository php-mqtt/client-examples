<?php

/*
    There might be cases where you need to send messages over the socket and wait for a response only for a cerain
    amount of time. To achieve this you can use a Loop Event Handler
*/

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../shared/config.php';

use PhpMqtt\Client\Examples\Shared\SimpleLogger;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;


// Set a variable to know if the operation was successful, set it to false by default
$outcome = false;

// Create a dummy object to make message handling easier
$msg = new stdClass();
$msg->payload = 'foo';

// Setting a random id can be useful in case many different messages are published, so that you code doesn't take another command's ack
$command_id = random_int(1, 10000);
$msg->command_id = $command_id;

// Set the desired timeout for the acknowlegement
$timeout = 10;

try {
    // Create a new instance of an MQTT client and configure it to use the shared broker host and port.
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_PORT, 'test-subscriber', MqttClient::MQTT_3_1, null);

    // Connect to the broker without specific connection settings but with a clean session.
    $client->connect(null, true);

    // Create a loop event handler, this function will run each time the loop starts
    $client->registerLoopEventHandler(function ($client, float $elapsedTime) use ($timeout) {

            // Check if timeout has expired
            if ($elapsedTime >= $timeout) {

                // Interrupt the loop 
                $client->interrupt();
            }
        });

    // Subscribe to foo/#
    // Since the outcome is needed outside the callback we will pass a pointer to it
    $client->subscribe('foo/#', function ($topic, $message) use ($client, $command_id, &$outcome) {

            // Get an object from the message string
            $message = json_decode($message);

            // Check if recived message is the ACK you were waiting for
            if ($message->payload == 'ACK' && $message->command_id == $command_id){
                
                // Do what you need to do when ack is successful
                echo 'ACK Received';

                // Now that we have our ACK we can switch our outcome variable
                $outcome = true;

                // Interrupt the loop
                $client->interrupt();
            }
            
        });

    // Publish the message to the desired topic
    $client->publish('foo/bar', json_encode($msg));

    // Since subscribing requires to wait for messages, we need to start the client loop which takes care of receiving,
    // parsing and delivering messages to the registered callbacks. The loop will run indefinitely, until a message containing an ACK
    // is received or the timeout expires, which will interrupt the loop.
    $client->loop(true);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();

} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Subscribing to a topic failed. An exception occurred.', ['exception' => $e]);
}


// Handle the result as you like
if ($outcome === true)
    echo 'Operation completed successfully';
else
    echo 'Operation failed, no acknowledgement received';