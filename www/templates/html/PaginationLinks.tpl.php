<div class="wdn_pagination_wrapper">
    <?php
        $limits = array(30 => 30, 60 => 60, 90 => 90, -1 => 'All');
        
        $maxPage = ceil($context->total/$context->limit);
    
        if ($maxPage < 1) {
            $maxPage = 1;
        }

        $currentPage = 1;
        if ($context->offset > 0) {
            $currentPage = ceil($context->offset/$context->limit)+1;
        }
    
        $next = $maxPage;
    
        if ($context->offset/$context->limit == 0) {
            $next = 2;
        } elseif ($currentPage < $maxPage) {
            $next = $currentPage+1;
        }

        // Get all the existing URL querystring parameters
        $existing_params = $controller->getRawObject()->getURLParams(html_entity_decode($context->url, ENT_QUOTES, 'utf-8'));
     ?>
    <form method="get" action="<?php echo $context->url; ?>" class="pagination">
        <label for="page">Go to page:</label>
        <input type="text" name="page" id="page" value="<?php echo $next; ?>" />
        of <?php echo $maxPage; ?>
        <input id="offset" value="<?php echo $context->offset; ?>" type="hidden" name="offset" />
        <input id="limit" value="<?php echo $context->limit; ?>" type="hidden" name="limit" />
        <input type="submit" value="Go" class="button" onclick="WDN.jQuery('#offset').attr('value', buros.getPaginationOffset(<?php echo $context->limit; ?>, WDN.jQuery('#page').attr('value'), <?php echo $maxPage; ?>));" />
        You are currently on page <?php echo $currentPage ?>
        <?php
        $skip_keys = array('offset', 'page', 'limit');
        $form_helper->renderHiddenInputs($savvy, $existing_params, $skip_keys);
        ?>
    </form>
    <form method="get" action="<?php echo $context->url; ?>" class="pagination">
        <div class="group">
            <label for="rlimit">Results per page:</label>
            <select id="rlimit" name="limit">
                <?php
                    foreach ($limits as $limit => $label) {
                        $selected = '';
                        if ($limit == $context->limit) {
                            $selected = ' selected="selected"';
                        }
                        echo '<option value="'.$limit.'"'.$selected.'>'.$label.'</option>';
                    }
                ?>
            </select>
            <?php
            $form_helper->renderHiddenInputs($savvy, $existing_params, $skip_keys);
            ?>
            <input value="0" type="hidden" name="offset" />
            <input type="submit" value="Go" class="button" />
        </div>
    </form>
</div>
