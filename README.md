# php-mqtt/client-examples

This repository contains examples for [`php-mqtt/client`](https://github.com/php-mqtt/client).

## Available Examples

### 1) Publishing

When publishing, there is only a noteworthy difference between QoS 0 and QoS 1/2, since QoS 0 is fire and forget, while QoS 1/2 require confirmation.
For completeness, an example for each QoS level is provided.

- [Publish a message using QoS 0](01_publishing/01_publish_with_qos_0/publish.php) (_unreliable delivery_)
- [Publish a message using QoS 1](01_publishing/02_publish_with_qos_1/publish.php) (_reliable delivery, but probably multiple times_)
- [Publish a message using QoS 2](01_publishing/03_publish_with_qos_2/publish.php) (_reliable delivery, exactly once_)

### 2) Subscribing

When subscribing, there is not really a difference between the QoS levels. All three QoS levels work the same from a library API perspective.
Under the hood, each of the QoS levels uses a slightly different implementation, though, to follow the protocol specification. 

- [Subscribe to a topic using QoS 0](02_subscribing/01_subscribe_with_qos_0/subscribe.php) (_unreliable delivery_)
- [Subscribe to a topic using QoS 1](02_subscribing/02_subscribe_with_qos_1/subscribe.php) (_reliable delivery, but probably multiple times_)
- [Subscribe to a topic using QoS 2](02_subscribing/03_subscribe_with_qos_2/subscribe.php) (_reliable delivery, exactly once_)

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
