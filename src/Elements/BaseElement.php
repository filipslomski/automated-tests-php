<?php

namespace myTests\Elements;

use myTests\Context;
use PHPUnit\Framework\Exception;

class BaseElement
{
    /** @var  \WebDriverBy */
    protected $locator;
    /** @var  \WebDriverBy */
    protected $originalLocator;
    protected $selector;
    protected $elementsCollection;
    protected $element = null;

    public function __construct($locator)
    {
        $this->locator = $locator;
        $this->originalLocator = $locator;
        $this->selector = $this->locator->getValue();
    }

    /**
     * @param bool $forceGetting
     *
     * @return \RemoteWebElement
     * @throws
     */
    public function getElement($forceGetting = false, $waitForPageLoad = true)
    {
        if ($waitForPageLoad) {
            $this->waitForPageLoad();
        }
        if (is_null($this->element) || $forceGetting) {
            Context::$logger->debug("Getting element: " . $this->selector);
            $this->element = Context::$browser->wait(7)->until(
                \WebDriverExpectedCondition::presenceOfElementLocated($this->locator)
            );
        }

        return $this->element;
    }

    public function getElements()
    {
        $this->waitForPageLoad();
        $this->elementsCollection = Context::$browser->findElements($this->locator);
    }

    /**
     * Allows to set locator parameters while we do not know them on element initialisation.
     *
     * @param string|string[] $parameters
     * @return $this
     */
    public function setLocatorParameters($parameters)
    {
        $parametersAsString = is_string($parameters) ? $parameters : implode(",", $parameters);
        Context::$logger->debug(
            "Setting locator parameters: " . $parametersAsString . " for element: " . $this->selector
        );
        $this->locator = $this->originalLocator;
        $this->selector = $this->originalLocator->getValue();
        $pattern = '/{}/';
        if (is_array($parameters)) {
            foreach ($parameters as $parameter) {
                $this->selector = preg_replace($pattern, $parameter, $this->selector, 1);
            }
        } else {
            $this->selector = preg_replace($pattern, $parameters, $this->selector, 1);
        }
        $mechanism = $this->locator->getMechanism();
        $this->locator = \WebDriverBy::$mechanism($this->selector);

        return $this;
    }

    public function setElementVisibility()
    {
        Context::$browser->executeScript(
            "arguments[0].style.height='auto'; arguments[0].style.visibility='visible';",
            [$this->getElement()]
        );

        return $this;
    }

    public function click(bool $onlyVisible = true, bool $waitForPageLoad = true)
    {
        if (!$onlyVisible || $this->isElementVisible()) {
            Context::$logger->debug("Clicking visible element:" . $this->selector);
            try {
                $this->getElement(true, $waitForPageLoad)->click();
            } catch (\UnknownServerException $e) {
                Context::$logger->notice("Clicking failed, scrolling to element:" . $this->selector);
                $this->scrollTo();
                $this->getElement(true, $waitForPageLoad)->click();
            }
        } else {
            throw new Exception("Element with selector " . $this->selector . " is not visible and cannot be clicked\n");
        }
    }

    public function scrollTo()
    {
        Context::$browser->executeScript("arguments[0].scrollIntoView(true);", [$this->getElement()]);
    }

    public function getText($trim = false)
    {
        Context::$logger->debug("Getting text for element: " . $this->selector);
        $text = $this->getElement(true)->getText();

        return $trim ? trim($text) : $text;
    }

    public function getAttribute(string $name) : string
    {
        Context::$logger->debug("Getting attribute for element: " . $this->selector);
        return $this->getElement()->getAttribute($name) ?? '';
    }

    public function mouseHover($delayInMicroSeconds = 700000)
    {
        Context::$logger->debug("Moving mouse to element: " . $this->selector);
        Context::$browser->getMouse()->mouseMove($this->getElement()->getCoordinates());
        usleep($delayInMicroSeconds);

        return $this;
    }

    public function setValue($value, $reset=true)
    {
        Context::$logger->debug("Setting value:" . $value . "for element: " . $this->selector);
        if ($reset) {
            $this->getElement(true)->clear();
        }
        $this->getElement()->sendKeys($value);
    }

    public function isElementPresent($timeout = 7)
    {
        Context::$logger->debug("Is element present? " . $this->selector);
        try {
            Context::$browser->wait($timeout)->until(
                \WebDriverExpectedCondition::presenceOfElementLocated($this->locator)
            );
            Context::$logger->debug("Element is present");
            return true;
        } catch (\NoSuchElementException | \TimeOutException $ex) {
            Context::$logger->debug("Element is not present");
            return false;
        }
    }

    public function isElementVisible($timeout = 7)
    {
        Context::$logger->debug("Is element visible? " . $this->selector);
        try {
            Context::$browser->wait($timeout)->until(
                \WebDriverExpectedCondition::visibilityOfElementLocated($this->locator)
            );
            Context::$logger->debug("Element is visible");
            return true;
        } catch (\NoSuchElementException | \TimeOutException $ex) {
            Context::$logger->debug("Element is not visible");
            return false;
        }
    }

    public function isElementVisibleNow()
    {
        Context::$logger->debug("Is element visible now? " . $this->selector);
        return $this->isElementVisible(1);
    }

    public function isElementPresentNow()
    {
        Context::$logger->debug("Is element present now? " . $this->selector);
        return $this->isElementPresent(1);
    }

    public function isElementNotVisible($timeout = 5)
    {
        Context::$logger->debug("Is element not visible? " . $this->selector);
        try {
            Context::$browser->wait($timeout)->until(
                \WebDriverExpectedCondition::invisibilityOfElementLocated($this->locator)
            );
            Context::$logger->debug("Element is not visible");
            return true;
        } catch (\NoSuchElementException | \TimeOutException $ex) {
            Context::$logger->debug("Element is visible");
            return false;
        }
    }

    public function isElementNotPresent($timeout = 5)
    {
        Context::$logger->debug("Is element not present? " . $this->selector);
        try {
            Context::$browser->wait($timeout)->until(
                \WebDriverExpectedCondition::not(\WebDriverExpectedCondition::presenceOfElementLocated($this->locator))
            );
            Context::$logger->debug("Element is not present");
            return true;
        } catch (\NoSuchElementException | \TimeOutException $ex) {
            Context::$logger->debug("Element is present");
            return false;
        }
    }

    private function waitForPageLoad()
    {
        Context::$logger->debug("Waiting for page load");
        $waitCode = [
            'jquery' => "return jQuery.active;",
            'prototype' => "return Ajax.activeRequestCount;",
            'dojo' => "return dojo.io.XMLHTTPTransport.inFlight.length;",
            'angular' => "return angular.element(document).injector().get('\$http').pendingRequests.length === 0"
        ];

        while (
            Context::$browser->executeScript($waitCode['jquery'])
            || !Context::$browser->executeScript($waitCode['angular'])
        ) {
            Context::$logger->debug("Sleeping 0.5 seconds");
            usleep(500000);
        };
        Context::$logger->debug("Page is loaded");
    }
}
