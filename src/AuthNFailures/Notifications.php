<?php
namespace Buros;

class Notifications
{
    public static function notify($type, $message, $duration)
    {
        $result = setcookie('buros_notifications['.$type.']', $message, time()+$duration, "/");
    }
}
