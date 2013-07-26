<?php
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

// Find users that used NTLMv1, an insecure protocol

$searchExpression = 'search sourcetype="WinEventLog:Security" EventCode=4624 AND Message=*V1* AND Account_Name!=*$ AND Account_Name != "ANONYMOUS LOGON" | eval uid=mvfilter(Account_Name != "-") | fields + uid + Source_Network_Address + ComputerName + Workstation_Name';

$resultCallback = function($iterator) {
    $result = $iterator->current();

    $client = '{unknown client machine}';
    if ($result['Source_Network_Address'] != '-') {
        $client = $result['Source_Network_Address'];
    } elseif (isset($result['Workstation_Name'])) {
        $client = $result['Workstation_Name'];
    }

    echo $result['uid'] . ' used NTLMv1 at ' . date('Y-m-d H:i:s', SplunkMonitor::getLastSplunkIndexTimestamp($result['_indextime'])) . ' from ' . $client . PHP_EOL;
};

$monitor->searchAndCallback($searchExpression, $resultCallback, $args);