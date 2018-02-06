<?php


namespace myTests\Utilities\data;

use myTests\Utilities\objects\User;

class Users
{
    public $exampleUser;

    public function __construct()
    {
        $this->square = new User("exampleUser", "examplePassword", "exampleName");
    }
}
