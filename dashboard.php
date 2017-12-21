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
            <iframe src="http://<?php echo $kibana; ?>:5601/app/kibana#/dashboard/50a6aea0-7dd0-11e7-a13b-f3030e4f028e?embed=true&_g=()" height="8500" width="100%"></iframe>
            <?php include('inc/footer.php'); ?>

        </div>
        
    <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>




