<?php

$title = str_replace('AuthNFailures\\', '', $context->options['model']);

echo str_replace('\\', ' ', preg_replace('/([a-z])([A-Z])/','$1 $2', $title));
