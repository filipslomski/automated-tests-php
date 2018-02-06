<?php

namespace myTests\PageObjects;

use myTests\Context;
use myTests\Elements\Button;
use myTests\Elements\Element;
use myTests\Elements\TextField;
use myTests\Utilities\objects\User;

class LoginPage extends BasePage
{
    public $url = "signin";

    public $emailInput;
    public $passwordInput;
    public $signIn;
    public $inactiveSignInFold;
    public $inactiveSignUpFold;
    public $errorMessage;

    public function __construct()
    {
        $this->emailInput = new TextField(\WebDriverBy::id("email"));
        $this->passwordInput = new TextField(\WebDriverBy::id("password"));
        $this->signIn = new Button(\WebDriverBy::id("_submit"));
        $this->errorMessage = new Element(\WebDriverBy::xpath(".//div[contains(@class,'alert-box') and contains(text(),'{}')]"));
        parent::__construct($this->url);
    }

    public function loginAs(User $user, $fillEmail = true, $fillPassword = true)
    {
        if ($fillEmail) {
            $this->emailInput->setValue($user->email);
        }
        if ($fillPassword) {
            $this->passwordInput->setValue($user->password);
        }
        $this->signIn->click();
        Context::$currentUser = $user;
    }

    public function registerAs()
    {
    }

    public function isErrorMessageVisible($text)
    {
        return $this->errorMessage->setLocatorParameters($text)->isElementVisible();
    }
}
