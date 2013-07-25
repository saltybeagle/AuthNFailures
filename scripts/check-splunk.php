<?php
/**
 * This script is a sample splunk monitor which should be run every 10 minutes or so
 *
 * The searches and callback iterators should be defined in your configuration as 
 * $splunk_searches = array([search]=>Closure)
 *
 * @author Brett Bieber <brett.bieber@gmail.com>
 *
 */
namespace AuthNFailures;

use AuthNFailures\SyslogAccumulator\SplunkMonitor;

$config_file = __DIR__ . '/../config.sample.php';

if (file_exists(__DIR__ . '/../config.inc.php')) {
    $config_file = __DIR__ . '/../config.inc.php';
}
require_once $config_file;

$monitor = new SplunkMonitor($splunk_config);

$args = array(
    'earliest_time' => '-15m', // find events up to 15 minutes ago
    'latest_time'   => 'now',  // until now
);

foreach ($splunk_searches as $searchExpression => $resultCallback) {
    $monitor->searchAndCallback($searchExpression, $resultCallback, $args);
}
