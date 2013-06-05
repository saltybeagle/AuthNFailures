<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord\Record as Record;

class PostHandler
{
    public $options = array();
    public $post    = array();
    public $files   = array();

    public function __construct($options = array(), $post = array(), $files = array())
    {
        $this->options = $options;
        $this->post    = $post;
        $this->files   = $files;
    }

    /**
     * Filter any pre-populated POST fields to prevent their use.
     *
     * @return void
     */
    public function filterPostValues()
    {

    }

    public function handle()
    {
        $this->filterPostValues();

        if (!isset($this->options['model'])) {
            // Nothing to do here
            return;
        }

        if (is_callable($this->options['model'])) {
            // We cannot determine which model to update
            // Search results do not take action
            return;
        }

        $object = new $this->options['model']($this->options);

        if ($object instanceof ActiveRecord\PostHandlerInterface) {
            $result = $object->handlePost($this->options, $this->post, $this->files);
            return $this->handleActiveRecordResult($result);
        }

        if (is_subclass_of($this->options['model'], __NAMESPACE__ . '\ActiveRecord\Record')) {
            $handler = new Record\PostHandler($this->options['model'], $this->options, $this->post, $this->files);

            $result = $handler->handle();
            return $this->handleActiveRecordResult($result);
        }

        // An error must have occurred
        return false;

    }

    public function handleActiveRecordResult($result)
    {
        $url    = Controller::$url;
        $action = 'deleted';

        //Determine what to say and where to redirect.
        if ($result instanceof Record) {
            $url = $result->getURL();

            //Do not display the edit form if we are being redirected to another new form.
            if (substr($url, -3) != 'new') {
                $url .= '/edit';
            }

            $action = 'saved';
        }

        if ($result) {
            Notifications::notify('success', $this->getUserFriendlyTableName($result->getTable()) . " $action!", 5);

            if (isset($this->options['format'])
                && $this->options['format'] != 'html') {
                $url .= '?format='.$this->options['format'];
            }
            $this->redirect($url);
        } else {
            Notifications::notify('error', "The record was not $action!", 5);
        }

        return $result;
    }

    protected function getUserFriendlyTableName($table)
    {
        $table = str_replace('_', ' ', $table);

        return ucwords($table);
    }

    public static function redirect($url, $exit = true)
    {
        Controller::redirect($url, $exit);
    }
}
