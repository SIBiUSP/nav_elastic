<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            require 'inc/config.php'; 
            require 'inc/functions.php';
            require 'inc/meta-header.php'; 
        ?>         
        <title>BDPI USP - Sobre</title>
    </head>

    <body>
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>

        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-margin-large-top">
            <h1>Em breve</h1>
            <hr class="uk-grid-divider">
            
            <?php require 'inc/footer.php'; ?>

        </div>
        
    <?php require 'inc/offcanvas.php'; ?>
        
    </body>
</html>