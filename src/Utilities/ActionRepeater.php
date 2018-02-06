<?php

namespace myTests\Utilities;



use myTests\Context;
//potentially can be used to support stability but should be used as a last resort as it makes debugging more difficult
class ActionRepeater
{
    /**
     * @param $action
     * @param int $timeout
     * @param bool $falseIsFail will fail if the repeatAction result is false
     * @param int $rate
     * @param bool $refresh
     * @return null
     * @throws
     */
    public static function repeatAction($action, $timeout = 5, $falseIsFail = false, $rate = 300000, $refresh = false)
    {
        Context::$logger->info("RepeatAction: started.");
        $result = null;
        $startTime = time();
        $rate = $refresh ? 5000000 : $rate; //if site is refreshed wait 5 seconds in order to give it time to load
        do {
            Context::$logger->info("RepeatAction: performing action");
            try {
                $exception = null;

                if ($result = $action()) {
                    return $result;
                }
            } catch (\Exception $e) {
                Context::$logger->info("RepeatAction: exception occured", [$e]);
                $exception = $e;
            }
            if (time() - $startTime >= $timeout) {
                break;
            }
            usleep($rate);
        } while (true);

        if (!empty($exception)) {
            throw $exception;
        }
        if ($falseIsFail && !$result) {
            throw new Exception("Action repeated resulted in false within given time");
        }

        return $result;
    }
}
