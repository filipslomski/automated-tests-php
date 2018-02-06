<?php

namespace myTests\PageObjects;

use myTests\Context;
use myTests\Elements\Button;
use myTests\Elements\Element;
use myTests\Elements\SideMenu;
use PHPUnit\Exception;
use WebDriverBy;
use WebDriverElement;

class BasePage
{
    protected $url;
    protected $sideMenu;
    protected $profileIcon;
    protected $signOutButton;
    protected $accountName;

    protected function __construct($url)
    {
        $this->url = $url;
    }

    public function open()
    {
        Context::$browser->get(Context::$baseUrl . $this->url);
    }

    public function refresh()
    {
        Context::$browser->navigate()->refresh();
    }

    /**
     * @param string $frameSelector
     */
    protected function switchToFrame($frameSelector)
    {
        if ($frameSelector == 'default') {
            Context::$browser->switchTo()->defaultContent();
            return;
        }

        $frame = $this->frameWithSelector($frameSelector);
        Context::$browser->switchTo()->frame($frame);
    }

    /**
     * @param frameSelector
     *
     * @return WebDriverElement
     */
    protected function frameWithSelector($frameSelector)
    {
        $frameFullSelector = "//iframe[contains(@class,'$frameSelector')]
                            | //iframe[@id='$frameSelector']
                            | //iframe[@name='$frameSelector']";
        return Context::$browser->findElement(WebDriverBy::xpath($frameFullSelector));
    }

    public function selectFromSideMenu($option)
    {
    }

    public function signOut()
    {
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
    }
}
