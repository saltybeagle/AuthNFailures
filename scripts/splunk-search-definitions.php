<?php

namespace AuthNFailures;

// Set of Splunk searches and callbacks
$splunk_searches = array();

// LDAP authentication failures
$splunk_searches['search source="unl-is-idm" | transaction conn maxpause=35s | where err=49 | rex field=_raw "(?<source_ip>\d{1,3}.\d{1,3}\.\d{1,3}\.\d{1,3}(?!\d))" | fields + uid + source_ip + host'] =  function ($iterator) {
	$result = $iterator->current();

	if (!isset($result['uid'])) {
		// User ID is unknown, nothing to do with this record
		return true;
	}

	$subject = new Subject();
	$subject->setId($result['uid']);
	try {

		$time = SyslogAccumulator\SplunkMonitor::getLastSplunkIndexTimestamp($result['_indextime']);

		$source_ip = null;
		if (isset($result['source_ip'])) {
			$source_ip = $result['source_ip'];
		}

		$subject->addEvent(
				$result['host'],       // e.g. ldap-test-1.unl.edu
				$source_ip,            // e.g. 129.93.1.1
				$time,                 // timestamp of event
				sha1($result['_raw']), // unique key for this event
				$result['_raw']        // store raw data for auditing
		);
	} catch (ActiveRecord\Exception $exception) {
		if (($previous = $exception->getPrevious())
				&& (1062 == $previous->getCode())) {
					// Duplicate key failure, this event has already been logged, end here we're caught up
					return false;
				}
				// Re-throw
				throw $exception;
	}
	return true;
};

// Active Directory authentication failures
$splunk_searches['search sourcetype="WinEventLog:Security" (EventCode="4771" AND Account_Name !=*$ AND Account_Name != - ) OR (EventCode="4776" AND Failure AND Logon_Account != *$) | eval uid=coalesce(Logon_Account,Account_Name) | eval client = coalesce(Client_Address,Source_Workstation) | fields + uid + client + ComputerName'] = function($iterator) {
	$result = $iterator->current();

	if (!isset($result['uid'])) {
		// Could not get the username, do not save this result
		return true;
	}

	$subject = new Subject();
	$subject->setId($result['uid']);

	try {

		$time = SyslogAccumulator\SplunkMonitor::getLastSplunkIndexTimestamp($result['_indextime']);

		$raw = implode($result['_raw']);
		$raw = str_replace('\n', PHP_EOL, $raw);

		$client = null;

		if (isset($result['client'])) {
			$client = $result['client'];
		}

		$subject->addEvent(
				$result['ComputerName'], // e.g. WSECDC2.unl.edu
				$client,                 // Workstation name or ipv6 IP
				$time,                   // timestamp of event
				sha1($raw),              // unique key for this event
				$raw                     // store raw data for auditing
		);
	} catch (ActiveRecord\Exception $exception) {
		if (($previous = $exception->getPrevious())
				&& (1062 == $previous->getCode())) {
					// Duplicate key failure, this event has already been logged, end here we're caught up
					return false;
				}
				// Re-throw
				throw $exception;
	}
	return true;
};

// Password changes
$splunk_searches['search source="unl-is-idm" MOD OR RESULT | transaction conn op maxpause=35s | search "MOD attr=userPassword" err=0 | fields + uid'] = function($iterator) {
	$result = $iterator->current();
	try {

		$time = SyslogAccumulator\SplunkMonitor::getLastSplunkIndexTimestamp($result['_indextime']);

		$raw = implode($result['_raw']);
		$raw = str_replace('\n', PHP_EOL, $raw);

		$subject = new Subject();
		$subject->setId($result['uid']);

		$subject->addReset(
				$time,      // timestamp of reset
				sha1($raw), // unique key for this event
				$raw        // store raw data for auditing
		);

		// If that succeeded, reset the counter
		$subject->resetCounter($time);

	} catch (ActiveRecord\Exception $exception) {
		if (($previous = $exception->getPrevious())
				&& (1062 == $previous->getCode())) {
					// Duplicate key failure, this event has already been logged
					return false;
				}
				// Re-throw
				throw $exception;
	}
	return true;
};
