<?php

namespace myTests\Utilities;

use Exception;
use myTests\Context;

class Screenshot
{
    private static $testStartDate = false;

    /**
     * @param \Behat\Behat\Hook\Scope\AfterStepScope $scope
     */
    public static function TakeScreenshot($scope)
    {
        $pathToScreenshotDir = __DIR__ . "/../screenshots/";

        $step = $scope->getStep();
        if (!self::$testStartDate) {
            self::$testStartDate = date("Y-m-d_H-i");
        }
        $path = array(
            'feature' => $scope->getFeature()->getTitle(),
            'step' => $step->getKeyword() . ' ' . $step->getText()
        );
        $path = preg_replace('/[^\-\.\w]/', '_', $path);
        if (!is_dir($pathToScreenshotDir . self::$testStartDate)) {
            mkdir($pathToScreenshotDir . self::$testStartDate, 0777, true);
        }

        $filename = $pathToScreenshotDir . self::$testStartDate . "/" . implode('-', $path) . '.png';

        Context::$browser->takeScreenshot($filename);
    }
}
