<?php if (count($context)): ?>
<ul>
<?php
foreach ($context as $reset) {
    /* @var $event \AuthNFailures\Reset */
    echo '<li class=""><a href="'.$reset->getURL().'/edit">' . $reset->id . '</a></li>';
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
Could not find any resets!
<?php endif; ?>
<a href="<?php echo $controller->getURL(); ?>resets/new" class="icon-upload-alt button">
    Add A New Reset
</a>