<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>         
        <title>BDPI USP - Dashboard</title>
    </head>

    <body>
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>

		<?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top">
            <h1>Dashboard</h1>           
            <iframe src="http://<?php echo $kibana; ?>:5601/app/kibana#/dashboard/7893a2a0-1656-11e8-bf7a-b7437c2dde01?embed=true&_g=()" height="8500" width="100%"></iframe>
        </div>
        <?php include('inc/footer.php'); ?>
        
    <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>




