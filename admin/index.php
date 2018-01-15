<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            getcwd();
            chdir('../');
            // Include essencial files         
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>
        <title>BDPI USP - Contato</title>
    </head>

    <body>
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>
        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top">
    
            
 <?php if(!empty($_SESSION['oauthuserdata'])): ?>              
            
            <h2>Área de administração e gerenciamento</h2>
            <div class="uk-grid" uk-grid>

                <div class="uk-width-2-4@">

                    <p><a href="admin/autoridades.php">Atualizar autoridades</a></p> 
                    
                </div>

                <div class="uk-width-2-4@m">
                    
                    <p><a href="admin/translate_en.php">Atualizar tradução para o Inglês</a></p>
                    
                </div>
            </div>
        
        
        
        <hr class="uk-grid-divider">
            
<?php else: ?>
            
            <p>Você não está logado</p>
            
<?php endif; ?>            
            
     
                        
<?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>