<?php
namespace AuthNFailures\ActiveRecord;

/**
 * This class is used for dynamic result queries which do not map to a
 * specific table. Normally this is used as the item class for JOINed tables.
 */
class DynamicRecord
{

    protected $__data = array();

    public function __construct()
    {

    }

    /**
     * Synchronize member variables with the values in the array
     *
     * @param array $data Associative array of field=>value pairs
     *
     * @return void
     */
    public function synchronizeWithArray($data)
    {
        $this->__data = $data;
    }

    public function __get($var)
    {
        return $this->__data[$var];
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return $this->__data;
    }

    /**
     * Get the columns for this record
     *
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->__data);
    }

    /**
     * Magic method for checking if a property is set.
     *
     * @param string $var The var
     *
     * @return bool
     */
    public function __isset($var)
    {
        return isset($this->__data[$var]);
    }

}
