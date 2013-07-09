<?php if (count($context)): ?>
<?php echo $savvy->render($context, 'AuthNFailures/ActiveRecord/RecordSet/Table.tpl.php'); ?>
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