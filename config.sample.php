<?php
namespace AuthNFailures;

function autoload($class)
{
    $class = str_replace(array('_', '\\'), '/', $class);
    include $class . '.php';
}

spl_autoload_register('AuthNFailures\autoload');

set_include_path(
        __DIR__ . '/src'
        . PATH_SEPARATOR . __DIR__ . '/vendor/pyrus/php'
        . PATH_SEPARATOR . __DIR__ . '/vendor/splunk-sdk-master'
);

ini_set('display_errors', true);
error_reporting(E_ALL);

Controller::$url = 'http://localhost/workspace/AuthNFailures/www/';

//Database config
ActiveRecord\Database::setDbSettings(array(
    'host'     => 'localhost',
    'user'     => 'authn_events',
    'password' => 'authn_events',
    'dbname'   => 'authn_events'
));

$splunk_config = array(
    'host'     => 'localhost',
    'port'     => '8089',
    'username' => 'user',
    'password' => 'changeme',
);

// Set of Splunk searches and callbacks
$splunk_searches = array();

/*
// Sample Splunk search & callback configuration
$splunk_searches['search authentication failure | head 100'] = function($iterator) {
    $result = $iterator->current();
    $subject = new Subject();
    $subject->setId($result['uid']);
    $subject->addEvent(
        $result['service'],
        $result['ip_address'],
        $result['_idxtime'],
        sha1($result['_raw']),
        $result['_raw']
    );
    // return true for iteration to continue
    return true;
}
*/
