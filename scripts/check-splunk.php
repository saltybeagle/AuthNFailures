<?php
namespace AuthNFailures;

use AuthNFailures\SyslogAccumulator\SplunkMonitor;

$config_file = __DIR__ . '/../config.sample.php';

if (file_exists(__DIR__ . '/../config.inc.php')) {
    $config_file = __DIR__ . '/../config.inc.php';
}
require_once $config_file;

$monitor = new SplunkMonitor($splunk_config);

foreach ($splunk_searches as $searchExpression => $resultCallback) {
    $monitor->searchAndCallback($searchExpression, $resultCallback);
}
