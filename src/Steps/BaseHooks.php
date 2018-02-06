<?php

namespace myTests\Steps;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use myTests\Environments;
use myTests\Utilities\logging\Logger;
use myTests\Utilities\Screenshot;
use PHPUnit\Framework\Exception;
use RemoteWebDriver;
use DesiredCapabilities;
use myTests\Context;

class BaseHooks implements BehatContext
{
    public static $seleniumHost = 'http://selenium:4444/wd/hub';
    public static $currentFeature;
    public static $currentScenario;
    public static $currentStep;

    /** @BeforeSuite */
    public static function initData()
    {
        Context::$logger = new Logger("TestLogger");
        Context::$baseUrl = Environments::$environments[$_ENV['TEST_ENV'] ?? 'preprod'];
        Context::$logger->info("Base URL is:" . Context::$baseUrl);
        $seleniumConnection = false;
        $startTime = time();
        while (!$seleniumConnection) {
            Context::$logger->info("Testing selenium connection");
            try {
                $seleniumSession = RemoteWebDriver::create(self::$seleniumHost, DesiredCapabilities::chrome());
                $seleniumSession->close();
                $seleniumConnection = true;
                Context::$logger->info("Selenium connection successful");
            } catch (\WebDriverCurlException $e) {
                Context::$logger->info("Selenium connection failed");
                if (time() - $startTime > 20) {
                    throw new Exception("Could not connect to selenium within 20 seconds. Error: " . $e);
                }
            }
        }
    }

    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public static function startBrowserAndPageObjects($scope)
    {
        self::$currentFeature = $scope->getFeature()->getTitle();
        self::$currentScenario = $scope->getScenario()->getTitle();
        Context::$browser = RemoteWebDriver::create(self::$seleniumHost, DesiredCapabilities::chrome());
        Context::$logger->info("Starting browser");
        Context::$browser->manage()->window()->setSize(new \WebDriverDimension(Context::SCREEN_WIDTH, Context::SCREEN_HEIGHT));
        Context::$logger->info("Setting browser dimension");
        Context::init();
    }

    /**
     * @BeforeStep
     * @param BeforeStepScope
     */
    public static function getStepName($scope)
    {
        self::$currentStep = $scope->getStep()->getText();
    }

    /** @AfterScenario */
    public static function closeBrowser()
    {
        Context::$browser->close();
        Context::$logger->info("Closing browser");
    }

    /**
     * @param \Behat\Behat\Hook\Scope\AfterStepScope $scope
     *
     * @AfterStep
     */
    public static function takeScreenshotForFailedStep($scope)
    {
        if ($scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::FAILED) {
            Context::$logger->debug("Taking screenshot of failed step");
            Screenshot::TakeScreenshot($scope);
        }
    }

    /** @AfterSuite */
    public static function tearDownWebDriver()
    {
    }
}
