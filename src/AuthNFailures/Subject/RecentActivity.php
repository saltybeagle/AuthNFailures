<?php
namespace AuthNFailures\Subject;

use AuthNFailures\Events;
use AuthNFailures\Exception;


class RecentActivity extends Events
{
    public $options = array(
        'limit'  		  => 30,
        'offset'          => 0,
        'reset_timestamp' => null,
    );

    public function __construct($options = array())
    {
        if (!isset($options['subject_id'])) {
            throw new Exception('subject_id is required', 400);
        }
        parent::__construct($options);
    }
    
    public function getWhereClause()
    {
        $where = 'WHERE subject = "'.$this->escapeString($this->options['subject_id']).'"';

        if (isset($this->options['reset_timestamp'])) {
            ' AND timestamp > '.(int)$this->options['reset_timestamp'];
        }

        return $where;
    }

    public function getOrderByClause()
    {
        return 'ORDER BY timestamp DESC';
    }
}