<?php


namespace myTests;

use myTests\PageObjects\LoginPage;
use myTests\PageObjects\MarketplacePage;
use myTests\PageObjects\Offer\GeneralOfferPage;
use myTests\PageObjects\Offer\PricingOfferPage;

class Pages
{
    /** @var  LoginPage */
    public $loginPage;
    /** @var HomePage */
    public $homePage;

    public function __construct()
    {
        $this->loginPage = new LoginPage();
        $this->homePage = new HomePage();
    }
}
