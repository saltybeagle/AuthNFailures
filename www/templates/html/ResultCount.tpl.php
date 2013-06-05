<?php
$total = count($context);
echo $total . ' result';
if ($total > 1 || $total == 0) {
    // Pluralize 'results'
    echo 's';
}
echo ' found.';
