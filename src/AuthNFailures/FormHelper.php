<?php
namespace AuthNFailures;

class FormHelper
{
    public function getValue($object, $field)
    {
        if (isset($object->$field)) {
            if ($object instanceof \Savvy_ObjectProxy) {
                $value = $object->getRaw($field);
            } else {
                $value = $object->$field;
            }

            return $value;
        }

        if (isset($_POST[$field])) {
            return $_POST[$field];
        }

        return '';
    }

    /**
     * Retrieve a friendly date in mysql date format
     *
     * @param mixed  $object
     * @param string $field
     *
     * @return string
     */
    public function getDate($object, $field, $format = 'Y-m-d')
    {
        $value = $this->getValue($object, $field);

        if (!empty($value)) {
            return date($format, strtotime($value));
        }

        return '';
    }

    /**
     * Get the value for the form action (POST target)
     *
     * @param \Buros\ActiveRecord\Recrod $object
     *
     * @return string
     */
    public function getAction($object)
    {
        if ($post_action = $object->getURL()) {
            if ((count($object->keys()) > 1)
                && preg_match('/\/new$/', $post_action)) {
                // New route for multi-key records
                return $post_action;
            }

            return  $post_action . '/edit';
        }

        return './new';
    }

    public function renderHiddenInputs($savvy, $values, $skip_keys)
    {
        $template = $savvy->findTemplateFile('Form/HiddenInput.tpl.php');
        echo $savvy->renderAssocArray($values, $skip_keys, function($key, $value, $skip_keys) use ($template) {
            if (in_array($key, $skip_keys)) {
                return;
            }
            if (is_array($value)) {
                $key = $key.'[]';
                $values = $value;
                foreach ($values as $value) {
                    include $template;
                }
                return;
            }
            include $template;
        });
    }
}
