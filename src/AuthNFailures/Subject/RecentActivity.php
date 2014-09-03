<?php
namespace AuthNFailures\Subject;

use AuthNFailures\Events;
use AuthNFailures\Exception;


class RecentActivity extends Events
{
    public $options = array(
        'limit'  => 30,
        'offset' => 0,
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
        return 'WHERE subject = "'.$this->escapeString($this->options['subject_id']).'"';
    }

    public function getOrderByClause()
    {
        return 'ORDER BY timestamp DESC';
    }
}