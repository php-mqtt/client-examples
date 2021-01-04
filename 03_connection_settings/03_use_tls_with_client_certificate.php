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
    $client = new MqttClient(MQTT_BROKER_HOST, MQTT_BROKER_TLS_PORT, 'test-publisher', MqttClient::MQTT_3_1, null, $logger);

    // Create and configure the connection settings as required.
    $connectionSettings = (new ConnectionSettings)
        ->setUseTls(true)
        ->setTlsClientCertificateFile(TLS_CLIENT_CERTIFICATE_FILE)
        ->setTlsClientCertificateKeyFile(TLS_CLIENT_CERTIFICATE_KEY_FILE)
        ->setTlsClientCertificateKeyPassphrase(TLS_CLIENT_CERTIFICATE_KEY_PASSPHRASE);

    // Connect to the broker with the configured connection settings and with a clean session.
    $client->connect($connectionSettings, true);

    // Publish the message 'Hello world!' on the topic 'foo/bar/baz' using QoS 0.
    $client->publish('foo/bar/baz', 'Hello world!', MqttClient::QOS_AT_MOST_ONCE);

    // Gracefully terminate the connection to the broker.
    $client->disconnect();
} catch (MqttClientException $e) {
    // MqttClientException is the base exception of all exceptions in the library. Catching it will catch all MQTT related exceptions.
    $logger->error('Connecting with TLS or publishing with QoS 0 failed. An exception occurred.', ['exception' => $e]);
}
