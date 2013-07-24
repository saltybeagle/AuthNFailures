<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord;

class Reset extends ActiveRecord\Record
{

    public $id;
    public $subject;
    public $reset_timestamp;
    public $external_key;
    public $raw_data;

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

    public function save()
    {
        $this->reset_timestamp = $this->normalizeTimeStamp($this->reset_timestamp);

        return parent::save();
    }

    public function normalizeTimeStamp($timestamp)
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        if (is_int($timestamp)) {
            $timestamp = date('Y-m-d H:i:s', $timestamp);
        }

        return $timestamp;
    }
}
