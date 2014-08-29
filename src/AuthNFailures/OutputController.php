<?php
namespace AuthNFailures;

class OutputController extends \Savvy
{
    public function __construct($options = array())
    {
        parent::__construct();
        $this->initialize($options);
    }

    public function initialize($options = array())
    {

        switch ($options['format']) {
            case 'json':
                header('Content-type:application/json');
                $this->setTemplateFormatPaths($options['format']);
                break;

            case 'csv':
                if (!isset($this->options['delimiter'])) {
                    $this->options['delimiter'] = ',';
                }

                $this->addGlobal('delimiter', $this->options['delimiter']);
                $this->addGlobal('delimitArray', function($delimiter, $array){
                    $out = fopen('php://output', 'w');
                    fputcsv($out, $array, $delimiter);
                });

                $filename = $this->getAttachmentFilename($options);
                header('Content-disposition: attachment; filename=' . $filename);

            case 'txt':
                header('Content-type:text/plain;charset=UTF-8');
                $this->setTemplateFormatPaths($options['format']);
                break;

            case 'partial':
                \Savvy_ClassToTemplateMapper::$output_template['AuthNFailures\Controller'] = 'AuthNFailures/Controller-partial';
                // intentional no-break
            case 'html':
                // Always escape output, use $context->getRaw('var'); to get the raw data.
                $this->setEscape(function($data) {
                    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8', false);
                });
                header('Content-type:text/html;charset=UTF-8');
                $this->setTemplateFormatPaths('html');
                break;
            default:
                throw new Exception('Invalid/unsupported output format', 500);
        }
    }

    /**
     * Get the filename which should be used when exporting a downloadable file
     * 
     * @return string
     */
    public function getAttachmentFilename($options)
    {
        if (is_string($options['model'])) {
            // Use the model as the filename
            $filename = $options['model'];
        } else {
            // Use the full regular expression matched route
            $filename = $options['0'];

            // Trim off any existing file extension
            $filename = preg_replace('/(.*)\..*$/', '$1', $filename);
        }

        // Replace directory and namespace separators with underscores
        $filename = str_replace(array('\\', '/'), '_', $filename);

        // Append the correct file extension for the requested format
        switch ($options['format']) {
            case 'tsv':
                $extension = 'txt';
                break;
            default:
                $extension = $options['format'];
        }

        return $filename . '.' . $extension;
        
    }

    /**
     * Set the array of template paths necessary for this format
     *
     * @param string $format Format to use
     */
    public function setTemplateFormatPaths($format)
    {
        $web_dir = dirname(dirname(__DIR__)) . '/www';

        $paths = array();
        $paths[] = $web_dir . '/templates/' . $format;

        $this->setTemplatePath($paths);
        
    }

    public function setReplacementData($field, $data)
    {
        foreach ($this->getConfig('filters') as $filter) {
            $filter[0]->setReplacementData($field, $data);
        }
    }

}
