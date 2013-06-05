<?php
namespace AuthNFailures\ActiveRecord;

class FilteredColumnRecord extends \FilterIterator
{
    protected $whitelist;

    public function __construct($record, $whitelist)
    {
        $this->whitelist = $whitelist;

        parent::__construct(new \ArrayIterator($record->toArray()));
    }

    public function accept()
    {
        return in_array($this->key(), $this->whitelist);
    }
}
