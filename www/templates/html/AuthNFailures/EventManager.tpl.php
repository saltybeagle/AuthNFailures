<?php if (count($context)): ?>
<ul>
<?php
foreach ($context as $event) {
    /* @var $event \AuthNFailures\Event */
    echo '<li class=""><a href="'.$event->getURL().'/edit">' . $event->id . '</a></li>';
}
?>
</ul>
<?php
if (count($context) > $context->options['limit']) {
    $pager = new stdClass();
    $pager->total  = count($context);
    $pager->limit  = $context->options['limit'];
    $pager->offset = $context->options['offset'];
    $pager->url    = './events';
    echo $savvy->render($pager, 'PaginationLinks.tpl.php');
}
?>
<?php else: ?>
Could not find any events!
<?php endif; ?>
<a href="<?php echo $controller->getURL(); ?>events/new" class="icon-upload-alt button">
    Add A New Event
</a>