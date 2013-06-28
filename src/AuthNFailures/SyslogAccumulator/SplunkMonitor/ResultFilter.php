<?php
namespace AuthNFailures\SyslogAccumulator\SplunkMonitor;

class ResultFilter extends \FilterIterator
{
    function accept()
    {
        return is_array(parent::current());
    }
}