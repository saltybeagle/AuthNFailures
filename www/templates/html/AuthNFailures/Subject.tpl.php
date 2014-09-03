<?php
/* @var $context AuthNFailures\Subject */
echo '<h2>'.$context->getId().'</h2>';

echo 'Current authentication failure count: '.$context->getCurrentCount();

echo $savvy->render($context->getRecentActivity());

?>
<h3>Your Recent Activity</h3>