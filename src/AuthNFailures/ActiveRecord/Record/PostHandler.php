<?php
namespace AuthNFailures\ActiveRecord\Record;

use AuthNFailures\ActiveRecord\Exception as Exception;

class PostHandler
{
    /**
     * Class name for the record to handle
     *
     * @var string
     */
    protected $class;

    /**
     * Posted array data! e.g. $_POST
     *
     * @var array
     */
    protected $post = array();

    /**
     * Get/querystring options e.g. $_GET
     *
     * @var array
     */
    protected $get = array();

    /**
     * The record we're handling
     *
     * @var \AuthNFailures\ActiveRecord\Record
     */
    protected $record;

    public function __construct($class, $get = array(), $post = array(), $files = array())
    {
        if (!is_subclass_of($class, __NAMESPACE__)) {
            throw new Exception('Invalid record class.', 400);
        }

        $this->class = $class;
        $this->post  = $post;
        $this->get   = $get;
    }

    /**
     * Handle the data posted and save the record
     *
     * @return \AuthNFailures\ActiveRecord\Record | false on error | true on deleted
     */
    public function handle()
    {
        $this->record = new $this->class;

        $this->record->synchronizeWithArray($this->post);

        //Discover the actions and do the proper actions (delete or save).
        if (isset($this->post['delete'])) {
            return $this->record->delete();
        }

        if (isset($this->get[0])
            && preg_match('/\/new$/', $this->get[0])) {

            // New record route, so insert a record
            if ($result = $this->record->insert()) {
                return $this->record;
            }

            return $result;
        }

        if ($result = $this->record->save()) {
            return $this->record;
        }

        return $result;

    }
}
