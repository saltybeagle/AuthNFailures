<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord;

class Count extends ActiveRecord\Record
{

    public $id;
    public $subject;
    public $current_count;

    public static function getTable()
    {
        return 'counts';
    }

    public function keys()
    {
        return array('id');
    }

    public function getURL()
    {
        if (!isset($this->id)) {
            return false;
        }

        return Controller::$url . 'counts/' . $this->id;
    }
}
