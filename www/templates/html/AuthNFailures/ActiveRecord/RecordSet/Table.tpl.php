<?php

if (!count($context)) {
    echo 'No results could be found.';
    return;
}

// Get table headers
$options = $context->getDefaultOptions();
$headers = get_class_vars($options['itemClass']);
if (count($headers) == 0) {
    $headers = array_fill_keys($context->getRawObject()->getColumns(), null);
}

?>

<table>
    <thead>
        <tr>
            <?php foreach ($headers as $header=>$value): ?>
            <th class="col_<?php echo $header; ?>"><?php echo $header; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($context as $record) : ?>
        <tr>
            <?php
            foreach ($headers as $column=>$value) {
                $value = nl2br($record->$column);
                echo '<td class="col_'.$column.'">';
                if (method_exists($record->getRawObject(), 'getURL')) {
                   echo '<a href="'.$record->getURL().'/edit">'.$value.'</a>';
                } else {
                   echo $value;
                }
                echo '</td>';
            }
            ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>