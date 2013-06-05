<?php
namespace AuthNFailures;

class Notifications
{
    public static function notify($type, $message, $duration)
    {
        $result = setcookie('authn_notifications['.$type.']', $message, time()+$duration, "/");
    }
}
