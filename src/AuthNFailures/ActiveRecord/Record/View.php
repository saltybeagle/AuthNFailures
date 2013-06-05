<?php
namespace AuthNFailures\ActiveRecord\Record;

use AuthNFailures\ActiveRecord\Exception;

class View
{
    public $options = array();

    protected $model_class;

    protected $model;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->model_class = $this->determineModel();
    }

    /**
     * Determine which record class (model) the user wants to view
     *
     * @throws Exception
     *
     * @return string
     */
    public function determineModel()
    {
        if (!isset($this->options['itemClass'])) {
            throw new Exception('Cannot determine model to use!', 500);
        }

        return $this->options['itemClass'];
    }

    /**
     * Get the record requested
     *
     * @return AuthNFailures\ActiveRecord\Record
     */
    public function getRecord()
    {
        $reflection = new \ReflectionClass($this->model_class);

        try {
            $method = $reflection->getMethod('getByID');

            $params   = array();
            $params[] = $this->options['id'];
            foreach ($this->options as $key=>$value) {
                if (strpos($key, '_id') !== false) {
                    $params[] = $value;
                }
            }

            return call_user_func_array(array($this->model_class, 'getByID'), $params);
        } catch (\Exception $e) {
            // Record class must not have getByID defined and is using the default __call magic method
        }

        return call_user_func(array($this->model_class, 'getByID'), $this->options['id']);

    }
}
