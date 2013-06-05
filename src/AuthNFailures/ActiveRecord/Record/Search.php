<?php
namespace AuthNFailures\ActiveRecord\Record;

use AuthNFailures\ActiveRecord\RecordList;
use AuthNFailures\ActiveRecord\Exception;

class Search extends RecordList
{
    public $options = array(
            'limit'   => -1,
            'offset'  => 0,
            'order'   => 'ASC',
            'orderBy' => null,
            );

    public function __construct($options = array())
    {
        if (!isset($options['listClass'])) {
            throw new Exception('You must pass a list class "listClass".', 400);
        }
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' => $this->options['listClass'],
            'itemClass' => $this->options['itemClass'],
        );
    }

    public function getSQL()
    {
        $record = new $this->options['itemClass'];
        $table = $record->getTable();

        if (empty($this->options['fields'])) {
            return 'SELECT * FROM '.$table.' WHERE 0';
        }

        $sql = 'SELECT '.implode(',', $record->keys()).' FROM '.$table.' WHERE 1';

        $params = array_keys(get_object_vars(new $this->options['itemClass']));
        foreach ($params as $key=>$field) {

            // Use local vars for readability
            $function = $this->options['func'][$key];
            $search   = $this->options['fields'][$key];

            switch ($function) {
                case 'LIKE':
                case '=':
                case '!=':
                case '>':
                case '<':
                case '>=':
                case '<=':
                    if (empty($search)) {
                        continue;
                    }

                    if ('LIKE' == $function) {
                        $search = '%' . $search . '%';
                    }

                    $sql .= ' AND `'.$this->escapeString($field).'` '.$function.' "'.$this->escapeString($search).'"';
                    break;
                case "= ''":
                case "!= ''":
                case 'IS NULL':
                case 'IS NOT NULL':
                    $sql .= ' AND `'.$this->escapeString($field).'` '.$function;
                    break;
            }
        }

        return $sql;
    }

}
