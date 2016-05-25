<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
	<p><?php echo PerchLang::get('Welcome. The dashboard gives you an overview of the content on your website.'); ?></p>
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<div id="dashboard" class="dash">
<?php

    $apps = $Perch->get_apps();
    
    foreach($apps as $app) {
        if ($app['dashboard']) {
            include($app['dashboard']);
        }
    }


?>
</div>
<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>