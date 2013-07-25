# AuthNFailures

Application for storing and monitoring aggregated authentication failures.

Potential use cases are for progressive-assurance-degradation after subjects have exceeded specific thresholds.
E.G. Upon password reset, place the user in the Bronze assurance group, after exceeding `X` authentication failures,
remove from the Bronze assurance group.

## Installation

* Create a database for storing the AuthNFailures
* Add a database user with write permission to the database
* Copy `config.sample.php` to `config.inc.php` and substitute your parameters
* Copy `www/sample.htaccess` to `www/.htaccess` and substitute your parameters

## Setting Up the Accumulator

The syslog accumulator is currently out of scope. Recommended solutions are rsyslog and Splunk.

## Setting Up the Monitors

*Failure monitors* typically need to be customized for your environment.
Samples are provided for Splunk+OpenLDAP.

*Reset monitors* typically need to be customized for your environment.
Samples are provided for Splunk+OpenLDAP.

*Threshold monitors* perform an action once a user has exceeded a specific threshold.
See the `scripts/bronze-threshold-monitor.php` for a sample.
