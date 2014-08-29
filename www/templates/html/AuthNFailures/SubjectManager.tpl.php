<?php if (count($context)): ?>
<ul>
<?php
foreach ($context as $subject) {
    /* @var $event \AuthNFailures\Subject */
    echo '<li class=""><a href="'.$subject->getURL().'">' . $subject->id . '</a></li>';
}
?>
</ul>
<?php
if (count($context) > $context->options['limit']) {
    $pager = new stdClass();
    $pager->total  = count($context);
    $pager->limit  = $context->options['limit'];
    $pager->offset = $context->options['offset'];
    $pager->url    = './resets';
    echo $savvy->render($pager, 'PaginationLinks.tpl.php');
}
?>
<?php else: ?>
Could not find any subjects!
<?php endif; ?>
