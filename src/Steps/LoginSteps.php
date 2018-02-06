<?php

namespace myTests\Steps;

use Behat\Behat\Context\Context as BehatContext;
use myTests\Context;
use PHPUnit\Framework\Assert as Assert;

class LoginSteps implements BehatContext
{
    public function __construct()
    {
    }

    /**
     * @Given /^I am on signin page$/
     */
    public function iAmOnSiginPage()
    {
        Context::$pages->loginPage->open();
    }

    /**
     * @Given /^I login as (.*)$/
     * @Given /^I am logged in as (.*)$/
     */
    public function iAmLoggedInAsProvider($userType)
    {
        Context::$pages->loginPage->open();
        Context::$pages->loginPage->loginAs(Context::$users->$userType);
    }
}
