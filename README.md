# php-mqtt/client-examples

This repository contains examples for [`php-mqtt/client`](https://github.com/php-mqtt/client).

## Available Examples

### 1) Publishing

- [Publish a message using QoS 0](01_publishing/01_publish_with_qos_0/) (_unreliable delivery_)
- [Publish a message using QoS 1](01_publishing/02_publish_with_qos_1/) (_reliable delivery, but probably multiple times_)
- [Publish a message using QoS 2](01_publishing/03_publish_with_qos_2/) (_reliable delivery, exactly once_)

### 2) Subscribing

### 3) Secure Connection using TLS

### 4) Hooks

## How to run the examples?

Simply clone the repository and run `composer install` to install the required dependencies.
You can then run the examples one by one, but please don't forget to change the shared settings like the MQTT broker host and port before.
The shared settings can be found in [`shared/config.php`](shared/config.php). Alternatively, the examples can be altered directly.

### Noteworthy

The examples use a custom logger to give insight about what is happening internally. You can adjust the logging level as needed.

## License

`php-mqtt/client-examples` is open-source software licensed under the [MIT license](LICENSE.md).
