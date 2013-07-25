<?php
namespace AuthNFailures\Subjects;

use AuthNFailures\Subjects;

class AboveThreshold extends Subjects
{
    protected $threshold;

    function __construct($threshold)
    {
        $this->threshold = (int)$threshold;

        parent::__construct();
    }

    public function getWhereClause()
    {
        return 'WHERE current_count > '.(int)$this->threshold;
    }
}