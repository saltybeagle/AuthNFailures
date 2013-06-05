<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $controller->getURL();?>css/all.css" />

<title><?php echo $savvy->render($context, 'AuthNFailures/PageTitle.tpl.php'); ?> Authentication Failures | MockU</title>
<script type="text/javascript">
var AUTHNFAILURES_HOME = '<?php echo $controller->getURL(); ?>';
</script>
</head>
<body>
    <div id="breadcrumbs">
        <ul>
        	<li>MockU</li>
            <li><a href="<?php echo $controller->getURL();?>">AuthN Failures</a></li>
            <li><?php echo $savvy->render($context, 'AuthNFailures/PageTitle.tpl.php'); ?></li>
        </ul>
    </div>
    <div id="navigation">
        <?php echo $savvy->render($context, 'AuthNFailures/Navigation.tpl.php'); ?>
    </div>
    <div id="titlegraphic">
        <h1>MockU Authentication Failures</h1>
    </div>
    <div id="pagetitle">
    	<h2><?php echo $savvy->render($context, 'AuthNFailures/PageTitle.tpl.php'); ?></h2>
    </div>
    <div id="maincontent">
        <?php 
        try {
    
            // Render any notifications
            echo $savvy->render(null, 'AuthNFailures/Notification.tpl.php');
        
            // Render requested output
            echo $savvy->render($context->output);
        } catch(Exception $e) {
            echo $savvy->render($e, 'Exception.tpl.php');
        }
        ?>
    </div>
    <div id="footer">
        &copy; <?php echo date('Y'); ?> MockU
    </div>
</body>
</html>
