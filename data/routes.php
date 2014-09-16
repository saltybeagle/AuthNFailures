<?php

/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes = array();

// Allow format to be specified with a file extension, e.g. .html
$format = '(\.(?P<format>[\w]+))?';

$routes['/^$/']                            = 'AuthNFailures\WelcomeScreen';
$routes['/^manage\/events'.$format.'$/']   = 'AuthNFailures\EventManager';
$routes['/^manage\/counts'.$format.'$/']   = 'AuthNFailures\CountManager';
$routes['/^manage\/resets'.$format.'$/']   = 'AuthNFailures\ResetManager';
$routes['/^manage\/subjects'.$format.'$/'] = 'AuthNFailures\SubjectManager';

$routes['/^reports\/topfailures'.$format.'$/'] = 'AuthNFailures\Reports\Over500FailuresByIPAndSubject';

$routes['/^subjects\/(?P<id>[\w]+)'.$format.'$/'] = 'AuthNFailures\Subject';

// // List of all the active record classes
// // [collectionurl] = array(listClass, itemClass)
// $active_records = array(
//     'events'       => array('Events', 'Event'),
//     'counts'       => array('Counts', 'Count'),
//     'resets'       => array('Resets', 'Reset'),
// );

// Now the fallback edit forms for most other tables
$routes['/^(?P<table>[a-z]+)\/new$/']                 = 'AuthNFailures\ActiveRecord\Record\EditForm';
$routes['/^(?P<table>[a-z]+)\/(?P<id>[\w]+)\/edit$/'] = 'AuthNFailures\ActiveRecord\Record\EditForm';

return $routes;