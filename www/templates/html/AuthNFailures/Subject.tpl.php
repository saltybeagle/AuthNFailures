<?php
/* @var $context AuthNFailures\Subject */
echo '<h2>'.$context->getId().'</h2>';

echo 'Current authentication failure count: '.$context->getCurrentCount();

echo $savvy->render($context->getRecentActivity());
echo $savvy->render($context->getResets());
?>