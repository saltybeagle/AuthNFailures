
<select <?php echo $savvy->render($context->getAttributes(), 'Form/Attributes.tpl.php'); ?>>
    <?php if ($context->allowEmpty()): ?>
    <option value=""></option>
    <?php endif; ?>
    <?php foreach ($context->getIterator() as $key=>$value):
        if ($context->getKeyField() != null && is_object($value)) {
            $key = $context->getKeyField();
            $key = $value->$key;
        }
    ?>
    <option value="<?php echo $key; ?>" <?php echo ($key == $context->getSelectedKey())?'selected="selected"':'';?>><?php echo $value; ?></option>
    <?php endforeach; ?>
</select>