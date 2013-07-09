<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord;

class Reset extends ActiveRecord\Record
{

    public $id;
    public $subject;
    public $reset_timestamp;

    public static function getTable()
    {
        return 'resets';
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

        return Controller::$url . 'resets/' . $this->id;
    }
}
