<?php
/**
 * This script is a sample notification script for finding users which have
 * exceeded the threshold of failed authentication attempts for InCommon Bronze
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

$threshold = 100;

$subjectCallback = function($iterator) {
    /* @var $subject AuthNFailures\Subject */
    $subject = $iterator->current();

    echo $subject->id. ' has exceeded the bronze threshold. Their count is at '.$subject->getCurrentCount().PHP_EOL;

    return true;
};

$monitor = new Monitor();
$monitor->searchAboveThresholdCallback($threshold, $subjectCallback);
