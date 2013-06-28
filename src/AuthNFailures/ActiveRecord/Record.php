<?php
/**
 * Simple Active Record implementation
 *
 * PHP version 5
 *
 * @category  Publishing
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
namespace AuthNFailures\ActiveRecord;

use AuthNFailures\ActiveRecord\Exception\PreparedStatementException;

use AuthNFailures\Controller as Controller;

abstract class Record
{
    public function __construct()
    {

    }

    /**
     * Prepare the insert SQL for this record
     *
     * @param string &$sql The INSERT SQL query to prepare
     *
     * @return array Associative array of field value pairs
     */
    protected function prepareInsertSQL(&$sql)
    {
        $sql    = 'INSERT INTO '.$this->getTable();
        $fields = get_object_vars($this);
        if (isset($fields['options'])) {
            unset($fields['options']);
        }
        $sql .= '(`'.implode('`,`', array_keys($fields)).'`)';
        $sql .= ' VALUES ('.str_repeat('?,', count($fields)-1).'?)';

        return $fields;
    }

    /**
     * Prepare the update SQL for this record
     *
     * @param string &$sql The UPDATE SQL query to prepare
     *
     * @return array Associative array of field value pairs
     */
    protected function prepareUpdateSQL(&$sql)
    {
        $sql    = 'UPDATE '.$this->getTable().' ';
        $class  = get_class($this);
        $fields = get_class_vars($class);

        // Filter out static vars, leaving only the member vars
        array_walk($fields, function($value, $key) use ($class, &$fields) {
            if (isset($class::$$key)) {
                unset($fields[$key]);
            }
        });

        $sql .= 'SET `'.implode('`=?,`', array_keys($fields)).'`=? ';

        $sql .= 'WHERE ';
        foreach ($this->keys() as $key) {
            $sql .= $key.'=? AND ';
        }

        $sql = substr($sql, 0, -4);

        $fields = $fields + get_object_vars($this);

        return $fields;
    }

    /**
     * Save the record. This automatically determines if insert or update
     * should be used, based on the primary keys.
     *
     * @return bool
     */
    public function save()
    {
        $saveType = 'save';

        foreach ($this->keys() as $key) {
            if (empty($this->$key)) {
                $saveType = 'create';
            }
        }

        if ($saveType == 'create') {
            $result = $this->insert();
        } else {
            $result = $this->update();
        }

        return $result;
    }

    /**
     * Insert a new record into the database
     *
     * @return bool
     */
    public function insert()
    {
        $sql      = '';
        $fields   = $this->prepareInsertSQL($sql);
        $values   = array();
        $values[] = $this->getTypeString(array_keys($fields));
        foreach ($fields as $key=>$value) {
            $values[] =& $this->$key;
        }

        return $this->prepareAndExecute($sql, $values);
    }

    /**
     * Update this record in the database
     *
     * @return bool
     */
    public function update()
    {
        $sql      = '';
        $fields   = $this->prepareUpdateSQL($sql);
        $values   = array();
        $values[] = $this->getTypeString(array_keys($fields));
        foreach ($fields as $key=>$value) {
            $values[] =& $this->$key;
        }
        // We're doing an update, so add in the keys!
        $values[0] .= $this->getTypeString($this->keys());
        foreach ($this->keys() as $key) {
            $values[] =& $this->$key;
        }

        return $this->prepareAndExecute($sql, $values);
    }

    /**
     * Prepare the SQL statement and execute the query
     *
     * @param string $sql    The SQL query to execute
     * @param array  $values Values used in the query
     *
     * @throws Exception
     *
     * @return true
     */
    protected function prepareAndExecute($sql, $values)
    {
        $mysqli = self::getDB();

        if (!$stmt = $mysqli->prepare($sql)) {
            throw new Exception('Error preparing database statement! '.$mysqli->error, 500);
        }

        call_user_func_array(array($stmt, 'bind_param'), $values);
        if ($stmt->execute() === false) {
            $previous = new PreparedStatementException($stmt->error, $stmt->errno);
            throw new Exception($stmt->error, 500, $previous);
        }

        if ($mysqli->insert_id !== 0) {
            $this->id = $mysqli->insert_id;
        }

        return true;

    }

    /**
     * Get the type string used with prepared statements for the fields given
     *
     * @param array $fields Array of field names
     *
     * @return string
     */
    public function getTypeString($fields)
    {
        $types = '';
        foreach ($fields as $name) {
            switch ($name) {
                case 'id':
                    $types .= 'i';
                    break;
                default:
                    $types .= 's';
                    break;
            }
        }

        return $types;
    }

    /**
     * Convert the string given into a usable date for the RDBMS
     *
     * @param string $str A textual description of the date
     *
     * @return string|false
     */
    public function getDate($str)
    {
        if ($time = strtotime($str)) {
            return date('Y-m-d', $time);
        }

        if (strpos($str, '/') !== false) {
            list($month, $day, $year) = explode('/', $str);

            return $this->getDate($year.'-'.$month.'-'.$day);
        }
        // strtotime couldn't handle it
        return false;
    }

    /**
     * Simple method for getting a record by a single primary key
     *
     * @param string $table Table to retrieve record from
     * @param int    $id    The primary key/ID value
     * @param string $field The field that holds the primary key
     *
     * @return false | Record
     */
    public static function getRecordByID($table, $id, $field = 'id')
    {
        $mysqli = self::getDB();
        $sql    = "SELECT * FROM $table WHERE $field = ".intval($id).' LIMIT 1;';
        if ($result = $mysqli->query($sql)) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Delete this record in the database
     *
     * @return bool
     */
    public function delete()
    {
        $mysqli = self::getDB();
        $sql    = "DELETE FROM ".$this->getTable()." WHERE ";
        foreach ($this->keys() as $key) {
            if (empty($this->$key)) {
                throw new Exception('Cannot delete this record.' .
                                    'The primary key, '.$key.' is not set!',
                                    400);
            }
            $value = $this->$key;
            if ($this->getTypeString(array($key)) == 's') {
                $value = '"'.$mysqli->escape_string($value).'"';
            }
            $sql .= $key.'='.$value.' AND ';
        }
        $sql  = substr($sql, 0, -4);
        $sql .= ' LIMIT 1;';
        if ($result = $mysqli->query($sql)) {
            return true;
        }

        return false;
    }

    /**
     * Magic method for static calls
     *
     * @param string $method Tsathod called
     * @param array  $args   Array of arguments passed to the method.
     *                       If retrieving a multi-key table $args[0] (the first param) must be an associative array of
     *                       column name => value pairs.
     *
     * @method getBy[FIELD NAME]
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        switch (true) {
        case preg_match('/getBy([\w]+)/', $method, $matches):
            $class    = get_called_class();
            $field    = strtolower($matches[1]);
            $whereAdd = null;
            if (isset($args[1])) {
                $whereAdd = $args[1];
            }

            //Check if we need to handle multi key tables (return early if we don't)
            if (strtolower($method) != 'getbyid') {
                return self::getByAnyField($class, $field, $args[0], $whereAdd);
            }

            $keys = call_user_func(get_called_class() . "::getKeys");

            //Reformat the args list to make sure it compatible with the following code. (for just one key).
            if (!is_array($args[0])) {
                $args[0] = array($keys[0] => $args[0]);
            }

            //Check for a valid number of keys
            if (count($args[0]) != count($keys)) {
                throw new Exception('Incorrect number of keys provided for table: ' . self::getTable() . '. Expected ' . implode(", ", $keys), 500);
            }

            //Get our connection for escaping
            $mysqli = self::getDB();
            $whereAdd = "";
            $i = 0;

            foreach ($args[0] as $key=>$value) {
                //Non-keys may be passed with data.  Ignore non-keys.
                if (!in_array($key, $keys)) {
                    continue;
                }

                //first value will not be in the whereAdd
                if ($i == 0) {
                    $primaryField = $key;
                    $primaryValue = $value;
                    $i++;

                    continue;
                }

                $whereAdd .= " " . $mysqli->escape_string($key) . " = " . $mysqli->escape_string($value);

                $i++;
            }

            return self::getByAnyField($class, $primaryField, $primaryValue, $whereAdd);
        }
        throw new Exception('Invalid static method called.', 500);
    }

    public static function getByAnyField($class, $field, $value, $whereAdd = '')
    {
        $record = new $class;

        if (!empty($whereAdd)) {
            $whereAdd = $whereAdd . ' AND ';
        }

        $mysqli = self::getDB();
        $sql    = 'SELECT * FROM '
                    . $record->getTable()
                    . ' WHERE '
                    . $whereAdd
                    . $field . ' = "' . $mysqli->escape_string($value) . '"';
        $result = $mysqli->query($sql);

        if (false === $result) {
            throw new Exception($mysqli->errno.':'.$mysqli->error, 500);
        }

        if ($result->num_rows == 0) {
            return false;
        }

        $record->synchronizeWithArray($result->fetch_assoc());

        return $record;
    }

    /**
     * Get the DB
     *
     * @return mysqli
     */
    public static function getDB()
    {
        return Database::getDB();
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
        foreach (get_object_vars($this) as $key=>$default_value) {
            if (
                isset($data[$key])
                && $this->$key != $data[$key]
                && !(is_null($this->$key) && ($data[$key] == '')) // When a field is null and data was POST'ed as the empty string, just keep it as null
                ) {
                $this->$key = $data[$key];
            }
        }
    }

    /**
     * Reload data from the database and refresh member variables
     *
     * @return void
     */
    public function reload()
    {
        $keys = $this->keys();

        if (count($keys) > 1) {
            throw new Exception('You must specify a reload method for a multi-key record class', 500);
        }

        $mysqli = self::getDB();
        $sql    = 'SELECT * FROM '
                    . $this->getTable()
                    . ' WHERE '
                    . $keys[0] .' = "' . $mysqli->escape_string($this->{$keys[0]}) . '" LIMIT 1;';
        $result = $mysqli->query($sql);

        if (false === $result) {
            throw new Exception($mysqli->errno.':'.$mysqli->error, 500);
        }

        if ($result->num_rows == 0) {
            throw new Exception('Invalid reload, no record with that ID exists!', 500);
        }

        $this->synchronizeWithArray($result->fetch_assoc());

        return true;
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Get the primary keys for this table in the database
     *
     * @return array
     */
    abstract public function keys();

    /**
     * Get they primary keys for this table in the database.
     * This is a static method which gets the keys directly from the DB
     *
     * @throws Exception
     *
     * @return array indexed array of keys
     */
    public static function getKeys()
    {
        //Store statically to reduce the number of db calls
        static $keys;

        if (empty($keys)) {
            $keys = array();
        }

        $table = call_user_func(get_called_class() . "::getTable");

        if (isset($keys[$table])) {
            return $keys[$table];
        }

        //SHOW KEYS FROM table WHERE Key_name = 'PRIMARY'
        $mysqli = self::getDB();
        $sql    = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";

        $result = $mysqli->query($sql);

        if (false === $result) {
            throw new Exception($mysqli->errno.':'.$mysqli->error, 500);
        }

        if ($result->num_rows == 0) {
            throw new Exception('Unable to get keys for table: ' . $table, 500);
        }

        while ($row = $result->fetch_assoc()) {
            $keys[$table][] = $row['Column_name'];
        }

        return $keys[$table];
    }

     /**
     * Return a string containing the table name.
     *
     * @return string
     */
    public static function getTable()
    {
        $className = get_called_class();
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        return strtolower($className).'s';
    }

    public function getURL()
    {
        $keys = $this->keys();
        if (count($keys) > 1) {
            throw new Exception('Cannot determine route for multi keys yet!', 500);
        }

        if (!isset($this->{$keys[0]})) {
            return false;
        }

        return Controller::getURL()
               . call_user_func(array(get_called_class(), 'getTable'))
               . '/' . $this->{$keys[0]};
    }

    /**
     * Get information about the table and all the columns
     *
     * @return \AuthNFailures\ActiveRecord\TableInfo
     */
    public function getTableInfo()
    {
        static $info = false;

        if (!$info) {
            $info = new TableInfo($this->getTable());
        }

        return $info;
    }
}
