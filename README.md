# php-mqtt/client-examples

This repository contains examples for [`php-mqtt/client`](https://github.com/php-mqtt/client).

## Available Examples

### 1) Publishing

When publishing, there is only a noteworthy difference between QoS 0 and QoS 1/2, since QoS 0 is fire and forget,
while QoS 1/2 require confirmation. For completeness, an example for each QoS level is provided.

- [Publish a message using QoS 0](01_publishing/01_publish_with_qos_0.php) (_unreliable delivery_)
- [Publish a message using QoS 1](01_publishing/02_publish_with_qos_1.php) (_reliable delivery, but probably multiple times_)
- [Publish a message using QoS 2](01_publishing/03_publish_with_qos_2.php) (_reliable delivery, exactly once_)

### 2) Subscribing

When subscribing, there is not really a difference between the QoS levels. All three QoS levels work the same from a library API perspective.
Under the hood, each of the QoS levels uses a slightly different implementation, though, to follow the protocol specification.
Same as with publishing, an example for each QoS level is provided for completeness.

- [Subscribe to a topic using QoS 0](02_subscribing/01_subscribe_with_qos_0.php) (_unreliable delivery_)
- [Subscribe to a topic using QoS 1](02_subscribing/02_subscribe_with_qos_1.php) (_reliable delivery, but probably multiple times_)
- [Subscribe to a topic using QoS 2](02_subscribing/03_subscribe_with_qos_2.php) (_reliable delivery, exactly once_)

### 3) Connection Settings

For the connection to the broker, additional settings can be used. For example a username and password for authorization,
a customized timeout for the connection attempt or TLS settings for a secured connection.
For simplicity, all the following examples will publish a single message using QoS 0 after connecting to the broker.

- [Authorize using username and password](03_connection_settings/01_authorize_with_username_and_password.php)
- [Use TLS without client certificate](03_connection_settings/02_use_tls_without_client_certificate.php)
- [Use TLS with client certificate](03_connection_settings/03_use_tls_with_client_certificate.php)
- [Declare Last Will upon connection](03_connection_settings/04_declare_last_will_upon_connection.php)

### 4) Hooks

To inject logic into the execution path of our MQTT client, it is possible to use so-called hooks.
They are essentially callbacks which can be registered for a specific purpose and which are called upon certain events.
The following gives a few examples and ideas what can be done with hooks, although they are a lot more versatile than can be shown here.

- [Use a loop event handler to perform periodic actions](04_hooks/01_use_a_loop_event_handler_to_perform_periodic_actions.php)
- [Use a publish event handler to log published messages](04_hooks/02_use_a_publish_event_handler_to_log_published_messages.php)
- [Use a message received event handler to log received messages](04_hooks/03_use_a_message_received_event_handler_to_log_received_messages.php)

### 5) Interrupting the Loop

Since the event loop provided by `MqttClient::loop()` is an infinite loop by design, most applications need a way to escape it.
Most often the primary use case is for a graceful shutdown of the application, to avoid forceful termination.

- [Use `pcntl_signal` to interrupt the loop](05_interuppting_the_loop/01_use_pcntl_signal_to_interrupt_the_loop.php)

## How to run the examples?

Simply clone the repository and run `composer install` to install the required dependencies.
You can then run the examples one by one, but please don't forget to change the shared settings like the MQTT broker host and port before.
The shared settings can be found in [`shared/config.php`](shared/config.php). Alternatively, the examples can be altered directly.

### Noteworthy

The examples use a custom logger to give insight about what is happening internally. You can adjust the logging level as needed.

## License

`php-mqtt/client-examples` is open-source software licensed under the [MIT license](LICENSE.md).
