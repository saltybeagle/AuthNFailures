<?php
namespace AuthNFailures;

class Resets extends ActiveRecord\RecordList
{
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' => __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Reset',
        );
    }
}
