<?php
namespace AuthNFailures;

class Subjects extends ActiveRecord\DynamicRecordList
{
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' => __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Subject',
        );
    }

    public function getColumns()
    {
        return array('DISTINCT subject AS id');
    }

    public function getFromClause()
    {
        return ' FROM counts';
    }
}
