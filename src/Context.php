<?php

namespace myTests;

use myTests\Utilities\data\Offers;
use myTests\Utilities\data\Trades;
use myTests\Utilities\data\Users;
use myTests\Utilities\objects\User;
use RemoteWebDriver;


/**
 * Class Context
 * Contains "global" variables that needs to be shared between contexts or pages / elements
 */
class Context
{
    /** @var RemoteWebDriver */
    public static $browser;
    /** @var  string */
    public static $baseUrl;
    /** @var  User */
    public static $currentUser;
    /** @var  Pages */
    public static $pages;
    /** @var  Users */
    public static $users;
    /** @var  Logger*/
    public static $logger;

    public const SCREEN_WIDTH = 1800;
    public const SCREEN_HEIGHT = 1200;

    public static function init()
    {
        self::$users = new Users();
        self::$pages = new Pages();
    }
}
