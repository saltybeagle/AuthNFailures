<?php
namespace AuthNFailures\ActiveRecord;

abstract class DynamicRecordList extends RecordList
{

    /**
     * Get the SELECT portion of the query
     *
     * @return string
     */
    protected function getSelectClause()
    {
        return 'SELECT '.implode(',', $this->getColumns());
    }

    abstract protected function getColumns();

    /**
     * Get the list of tables used in the SELECT query
     *
     * @return string
     */
    protected function getFromClause()
    {
        throw new Exception('You must define your JOIN');
    }

    public function current()
    {
        $class = $this->options['itemClass'];
        $obj = new $class();
        $obj->synchronizeWithArray($this->getInnerIterator()->current());

        return $obj;
    }

}
