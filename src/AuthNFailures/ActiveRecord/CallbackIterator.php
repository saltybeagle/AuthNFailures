<?php
namespace AuthNFailures\ActiveRecord;

class CallbackIterator extends \IteratorIterator
{
    protected $currentCallback;

    public function __construct($iterator, $currentCallback)
    {
        $this->currentCallback = $currentCallback;
        parent::__construct($iterator);
    }

    public function current()
    {
        $callback = $this->currentCallback;

        return $callback(parent::current());
    }
}
