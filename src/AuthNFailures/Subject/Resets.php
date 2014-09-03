<?php
namespace AuthNFailures\Subject;

use AuthNFailures\Resets as BaseResets;

class Resets extends BaseResets
{
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
		return 'ORDER BY reset_timestamp DESC';
	}
}