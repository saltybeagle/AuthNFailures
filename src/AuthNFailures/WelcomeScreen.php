<?php
namespace AuthNFailures;

class WelcomeScreen
{
    public function __postConstruct()
    {

    }

    function getDashBoard()
    {
        return new Dashboard($this->options);
    }
}
