<?php

/* @var $context \Buros\ActiveRecord\Record */
$keys = $context->getRawObject()->keys();
$i = 0;
foreach (get_object_vars($context->getRawObject()) as $key => $unused) {

    // Set default input field type
    $type = 'text';

    if (false !== strpos($key, 'date')) {
        $type = 'date';
    }

    $value = '';
    if (isset($parent->context->options['fields'], $parent->context->options['fields'][$i])) {
        $value = $parent->context->options['fields'][$i];
    }

?>
            <li>
                <label for="fields_<?php echo $i; ?>"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                <div class="grouped_controls">
                <select name="func[]" class="operator">
                    <?php
                    $functions = array(
                            'LIKE'        => 'LIKE',
                            '='           => '=',
                            '!='          => '!=',
                            "= ''"        => "= ''",
                            "!= ''"       => "!= ''",
                            '>'           => '>',
                            '<'           => '<',
                            '>='          => '≥',
                            '<='          => '≤',
                            'IS NULL'     => 'IS NULL',
                            'IS NOT NULL' => 'IS NOT NULL',
                            );
                    foreach ($functions as $key=>$label):
                        $selected = '';
                        if (isset($parent->context->options['func'])
                            && $parent->context->options['func'][$i] == htmlentities($key, ENT_QUOTES, 'UTF-8')) {
                            $selected = 'selected="selected"';
                        }
                    ?>
                    <option value="<?php echo htmlentities($key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>><?php echo htmlentities($label, null, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <input id="field_<?php echo $i; ?>" name="fields[<?php echo $i; ?>]" type="<?php echo $type; ?>" value="<?php echo $value; ?>" />
                </div>
            </li>
<?php
    
    $i++; 
}