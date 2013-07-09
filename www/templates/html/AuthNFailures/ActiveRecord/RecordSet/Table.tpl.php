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
                echo '<td><a href="'.$record->getURL().'/edit">' . $record->$column. '</a></td>';
            }
            ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>