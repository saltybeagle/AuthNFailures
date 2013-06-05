<?php

namespace AuthNFailures\ActiveRecord\Record;

use AuthNFailures\ActiveRecord\Record;
use AuthNFailures\Exception;
use AuthNFailures\ActiveRecord\PostHandlerInterface;

/**
 * This class is used to generate a generic edit form for a Record
 *
 *
 * @author Brett Bieber
 */
class EditForm extends Record implements PostHandlerInterface
{
    /**
     * The Record object
     *
     * @var Record $record
     */
    protected $record;

    public function __construct($options = array())
    {
        if (isset($options['table'])) {
            // Try and guess the model name from the table

            // Set the base namespace
            $options['class'] = substr(__NAMESPACE__, 0, strpos(__NAMESPACE__, '\\', 1));

            // Remove intermediate parent object ids
            $options['table'] = preg_replace('/[\d]+\//', '', $options['table']);

            // Convert table name to singular model name
            $options['class'] .= '\\'.trim(str_replace(' ', '\\', ucwords(str_replace('/', ' ', $options['table']))), 's');

        }

        if (!isset($options['class'])) {
            throw new Exception('You must specify a class name', 400);
        }

        if (!is_subclass_of($options['class'], __NAMESPACE__)) {
            throw new Exception('Invalid record class.', 400);
        }

        if (isset($options['id'])) {
            $this->record = call_user_func_array(array($options['class'], 'getById'), array($options['id']));
        } else {
            // Must be creating a new record
            $this->record = new $options['class'];
        }

        if (!$this->record) {
            throw new Exception('Invalid record id specified', 404);
        }

    }

    public function handlePost($options = array(), $post = array(), $files = array())
    {
        $handler = new PostHandler(get_class($this->record), $options, $post, $files);
        return $handler->handle();
    }

    function keys()
    {
        return $this->getRecord()->keys();
    }

    function getURL()
    {
        return $this->getRecord()->getURL();
    }

    /**
     * Get the Record object we're editing
     *
     * @return Record
     */
    public function getRecord()
    {
        return $this->record;
    }
}
