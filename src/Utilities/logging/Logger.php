<?php

namespace myTests\Utilities\logging;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use myTests\Steps\BaseHooks;

class Logger
{
    private $log;

    public function __construct($channel)
    {
        $this->log = new MonoLogger($channel);
        $stream = new StreamHandler(
            __DIR__ . '/../../logs/log_' . date("Y-m-d_H-i"),
            MonoLogger::DEBUG
        );
        $this->log->pushHandler($stream);
    }

    /**
     * System is unusable.
     */
    public function emergency(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    public function alert(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    public function critical(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public function error(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public function warning(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->warning($message, $context);
    }

    /**
     * Normal but significant events.
     */
    public function notice(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    public function info(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->info($message, $context);
    }

    /**
     * Detailed debug information.
     */
    public function debug(string $message, array $context = array())
    {
        $message = $this->addScenarioInfo($message);
        $this->log->debug($message, $context);
    }

    private function addScenarioInfo(string $message)
    {
        $scenarioInfo =
            "[" . BaseHooks::$currentFeature .
            "][" . BaseHooks::$currentScenario .
            "][" . BaseHooks::$currentStep .
            "]";

        return $scenarioInfo . $message;
    }
}
