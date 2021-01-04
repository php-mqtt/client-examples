<?php

declare(strict_types=1);

namespace PhpMqtt\Client\Examples\Shared;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class SimpleLogger extends AbstractLogger implements LoggerInterface
{
    /** @var string */
    private $logLevel;

    /** @var int */
    private $logLevelNumeric;

    /**
     * SimpleLogger constructor.
     *
     * @param string|null $logLevel
     */
    public function __construct(string $logLevel = null)
    {
        if ($logLevel === null) {
            $logLevel = LogLevel::DEBUG;
        }

        $this->logLevel        = $logLevel;
        $this->logLevelNumeric = $this->mapLogLevelToInteger($logLevel);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        if ($this->mapLogLevelToInteger($level) < $this->logLevelNumeric) {
            return;
        }

        echo $this->interpolate($message, $context) . PHP_EOL;
    }

    /**
     * Interpolates the given message with variables from the given context.
     * Replaced are placeholder of the form {foo} with variables of the same
     * name without curly braces in the context.
     *
     * @param       $message
     * @param array $context
     * @return string
     */
    private function interpolate($message, array $context = [])
    {
        // Build a replacement array with braces around the context keys.
        $replace = [];
        foreach ($context as $key => $val) {
            // Ensure that the value can be cast to string.
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // Interpolate replacement values into the message and return the result.
        return strtr($message, $replace);
    }

    /**
     * Maps the string representation of a log level to the numeric level.
     *
     * @param string $level
     * @return int
     */
    private function mapLogLevelToInteger(string $level): int
    {
        $map = $this->getLogLevelMap();

        if (!array_key_exists($level, $map)) {
            return $map[LogLevel::DEBUG];
        }

        return $map[$level];
    }

    /**
     * Returns a log level map.
     *
     * @return array
     */
    private function getLogLevelMap(): array
    {
        return [
            LogLevel::DEBUG     => 0,
            LogLevel::INFO      => 1,
            LogLevel::NOTICE    => 2,
            LogLevel::WARNING   => 3,
            LogLevel::ERROR     => 4,
            LogLevel::CRITICAL  => 5,
            LogLevel::ALERT     => 6,
            LogLevel::EMERGENCY => 7,
        ];
    }
}
