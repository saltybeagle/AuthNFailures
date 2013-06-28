<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord;

class Event extends ActiveRecord\Record
{

    public $id;
    public $subject;
    public $service;
    public $ip_address;
    public $timestamp;
    public $external_key;
    public $raw_data;

    public static function getTable()
    {
        return 'events';
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

        return Controller::$url . 'events/' . $this->id;
    }

    public function save()
    {
        if (!$this->timestamp) {
            $this->timestamp = time();
        }

        return parent::save();
    }
}
