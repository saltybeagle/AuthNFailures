<h3 class="zenform">Edit <?php echo ucwords($context->getRecord()->__getClass())?></h3>
<form class="zenform" action="<?php echo $form_helper->getAction($context); ?>" method="post">
    <fieldset>
        <legend>Data Fields</legend>
        <ol>
            <?php
            echo $savvy->render($context->getRecord(), 'AuthNFailures/ActiveRecord/FormFieldIterator.tpl.php');
            ?>
        </ol>
    </fieldset>
    <input type="submit" name="submit" value="Submit" />
</form>