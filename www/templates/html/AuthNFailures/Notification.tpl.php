<?php
if (!isset($_COOKIE['authn_notifications'])) {
    return;
}

foreach ($_COOKIE['authn_notifications'] as $key=>$value) : ?>
<script type="text/javascript">
WDN.initializePlugin('notice');
</script>
<div class="wdn_notice affirm duration_2">
    <div class="close">
        <a href="#" title="Close this notice">Close this notice</a>
    </div>
    <div class="message">
        <h4>Saved!:</h4>
        <p><?php echo $value; ?></p>
    </div>
</div>
<?php endforeach;
// Now remove the cookie
setcookie('authn_notifications', '', time() - 3600);
