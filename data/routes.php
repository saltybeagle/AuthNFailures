<?php

/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes                                             = array();
$routes['/^$/']                                     = 'AuthNFailures\WelcomeScreen';
$routes['/^manage\/events(\.(?P<format>[\w]+))?$/'] = 'AuthNFailures\EventManager';
$routes['/^manage\/counts(\.(?P<format>[\w]+))?$/'] = 'AuthNFailures\CountManager';
$routes['/^manage\/resets(\.(?P<format>[\w]+))?$/'] = 'AuthNFailures\ResetManager';
$routes['/^manage\/subjects(\.(?P<format>[\w]+))?$/'] = 'AuthNFailures\SubjectManager';


$routes['/^subjects\/(?P<id>[\w]+)(\.(?P<format>[\w]+))?$/'] = 'AuthNFailures\Subject';

// // List of all the active record classes
// // [collectionurl] = array(listClass, itemClass)
// $active_records = array(
//     'events'       => array('Events', 'Event'),
//     'counts'       => array('Counts', 'Count'),
//     'resets'       => array('Resets', 'Reset'),
// );

// Now the fallback edit forms for most other tables
$routes['/^(?P<table>[a-z]+)\/new$/']                                = 'AuthNFailures\ActiveRecord\Record\EditForm';
$routes['/^(?P<table>[a-z]+)\/(?P<id>[\w]+)\/edit$/']                = 'AuthNFailures\ActiveRecord\Record\EditForm';

return $routes;