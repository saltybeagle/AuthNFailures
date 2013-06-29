<?php
/* @var $context \AuthNFailures\ActiveRecord\Record */
$keys = $context->getRawObject()->keys();
$i = 0;
$len = count(get_object_vars($context->getRawObject()));
$hidden = '';
foreach (get_object_vars($context->getRawObject()) as $key => $unused) {

    // Set default input field type
    $type = 'text';
    $method = 'getValue';

    if (in_array($key, $keys)) {
        $type = 'hidden';
    }

    if (false !== strpos($key, 'date_')) {
        $type = 'date';
        $method = 'getDate';
    }

    $value = $form_helper->$method($context, $key);

    if ($type == 'hidden') {
        $hidden .= '<input id="'.$key.'" name="'.$key.'" type="hidden" value="'.$value.'" />';
    } else {
?>
            <li>
                <label for="<?php echo $key; ?>"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                <?php if (false === strpos($value, "\n")) : ?>
                <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" type="<?php echo $type; ?>" value="<?php echo $value; ?>" />
                <?php else : ?>
                <textarea id="<?php echo $key; ?>" name="<?php echo $key; ?>"><?php echo $value; ?></textarea>
                <?php endif; ?>
                <?php 
                    if ($i == $len - 1) {
                        echo $hidden;
                    }
                ?>
            </li>
<?php
    }
    
    $i++; 
}