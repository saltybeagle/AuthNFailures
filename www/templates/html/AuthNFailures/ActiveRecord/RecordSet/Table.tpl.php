<?php

if (!count($context)) {
    echo 'No results could be found.';
    return;
}

// Get table headers
$options = $context->getDefaultOptions();
$headers = get_class_vars($options['itemClass']);

?>

<table>
    <thead>
        <tr>
            <?php foreach ($headers as $header=>$value): ?>
            <th><?php echo $header; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($context as $record) : ?>
        <tr>
            <?php
            foreach ($headers as $column=>$value) {
                $value = nl2br($record->$column);
                echo '<td><a href="'.$record->getURL().'/edit">' . $value . '</a></td>';
            }
            ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>