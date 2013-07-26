<?php
namespace AuthNFailures\SyslogAccumulator;

use AuthNFailures\SyslogAccumulator\SplunkMonitor\ResultFilter;

// Import Splunk.php
require_once 'Splunk.php';

class SplunkMonitor
{
    /**
     * The Splunk service object
     * @var \Splunk_Service
     */
    protected $service;

    function __construct($splunk_config)
    {
        // Create an instance of Splunk_Service to connect to a Splunk server
        $this->service = new \Splunk_Service($splunk_config);
        $this->service->login();
    }

    /**
     * Search splunk and apply callback on each result
     *
     * @param string  $searchExpression Splunk search expression, must begin with 'search '
     * @param Closure $resultCallback   Callback function to apply to every result @see iterator_apply
     * @param array   $args             Additional arguments for searching, e.g.
     *     array(
     *         'earliest_time' => '-15m',
     *         'latest_time'   => 'now',
     *     )
     *
     * @return SplunkMonitor
     */
    public function searchAndCallback($searchExpression, $resultCallback, $args = array())
    {
        $job = $this->service->search($searchExpression, $args);
        while (!$job->isDone()) {
            // Wait for job to finish
            usleep(0.5 * 1000000);
            $job->refresh();
        }
        $results = $job->getResults();
        // Filter out only results
        $filtered = new ResultFilter($results);

        // Go through all the results and do something
        iterator_apply($filtered, $resultCallback, array($filtered));

        return $this;
    }

    /**
     * Return the last time from an optionally multi-valued $result['_indextime'] field
     *
     * @param int|array $time The timestamp from a splunk event
     *
     * @return number
     */
    public static function getLastSplunkIndexTimestamp($time)
    {
        // The timestamp can be a range of time!
        if (is_array($time)) {
            // Use the last second
            $time = (int)array_pop($time);
        } else {
            $time = (int)$time;
        }
    
        return $time;
    }
}