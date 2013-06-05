<?php if (count($context)): ?>
<ul>
<?php
foreach ($context as $count) {
    /* @var $event \AuthNFailures\Count */
    echo '<li class=""><a href="'.$count->getURL().'/edit">' . $count->id . '</a></li>';
}
?>
</ul>
<?php
if (count($context) > $context->options['limit']) {
    $pager = new stdClass();
    $pager->total  = count($context);
    $pager->limit  = $context->options['limit'];
    $pager->offset = $context->options['offset'];
    $pager->url    = './counts';
    echo $savvy->render($pager, 'PaginationLinks.tpl.php');
}
?>
<?php else: ?>
Could not find any counts!
<?php endif; ?>
<a href="<?php echo $controller->getURL(); ?>counts/new" class="icon-upload-alt button">
    Add A New Count
</a>