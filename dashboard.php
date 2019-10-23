<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            include('inc/config.php');
            include('inc/meta-header.php');
        ?>
        <title>BDPI USP - Dashboard</title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>

		<?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top" style="position: relative; padding-bottom: 15em;>
            <h1>Dashboard</h1>
               <iframe src="/app/kibana#/dashboard/<?php echo $dashboardHash; ?>?embed=true&_g=()" height="11500" width="100%"></iframe>
        </div>
        <?php include('inc/footer.php'); ?>

    <?php include('inc/offcanvas.php'); ?>

    </body>
</html>
