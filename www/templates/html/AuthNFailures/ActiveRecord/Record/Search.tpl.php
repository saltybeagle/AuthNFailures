<?php
$page->pagetitle = '<h1>Advanced Search</h1>';
$url = $controller->addURLParams('./search', $_GET);
?>
<div class="grid3 first">
<form class="form filters" action="./search" method="get">
    <fieldset>
    <legend>Search Parameters</legend>
    <ol>
        <?php
        echo $savvy->render(new $context->options['itemClass'], 'AuthNFailures/ActiveRecord/SearchFieldIterator.tpl.php');
        ?>
    </ol>
    </fieldset>
    <input type="submit" name="submit" value="Submit" />
</form>
</div>
<div class="grid9">
    <h2><?php echo ucwords(str_replace('\\', ' ', $context->options['listClass'])); ?> Search Results</h2>
    <?php
    echo $savvy->render($context, $savvy->getClassToTemplateMapper()->map($context->options['listClass']));
    ?>
    <div class="dataset_options">
        <a href="<?php echo $controller->addURLExtension($url, 'csv'); ?>" class="spreadsheet icon-item">CSV</a>
        <a href="<?php echo $controller->addURLExtension($url, 'tsv'); ?>" class="spreadsheet icon-item">TSV</a>
    </div>
</div>