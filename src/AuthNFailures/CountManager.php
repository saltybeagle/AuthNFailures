<?php
namespace AuthNFailures;

class CountManager extends Counts
{

    public $options = array('limit'=>30, 'offset'=>0);

    public function __construct($options)
    {
        $this->options = $options+$this->options;
        parent::__construct($this->options);
    }

    public function getOrderByClause()
    {
        return 'ORDER BY subject ASC';
    }
}
