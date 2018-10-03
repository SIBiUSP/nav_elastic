<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php');             
            include('inc/meta-header.php');
            include('inc/functions.php');
            
            if(!empty($_SESSION['oauthuserdata'])) { 
                users::store_user($_SESSION['oauthuserdata']);
            }
        
            /* Define variables */
            define('authorUSP','authorUSP');
        ?> 
        <title><?php echo $branch; ?></title>
        <!-- Facebook Tags - START -->
        <meta property="og:locale" content="pt_BR">
        <meta property="og:url" content="<?php echo $url; ?>">
        <meta property="og:title" content="<?php echo $branch; ?> - Página Principal">
        <meta property="og:site_name" content="<?php echo $branch; ?>">
        <meta property="og:description" content="<?php echo $branch_description; ?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800"> 
        <meta property="og:image:height" content="600"> 
        <meta property="og:type" content="website">
        <!-- Facebook Tags - END -->
        
    </head>

    <body>     
        
        <!-- < ?php include_once("inc/analyticstracking.php") ?> -->
        
        
        <div class="uk-background-image@s uk-background-cover uk-height-viewport" >
            <div class="uk-container">
                <div class="uk-position-cover uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-height-viewport" style="background-image: url(inc/images/Partitura.jpg);">
                    <?php include('inc/navbar_inverted.php'); ?>
                    <div class="uk-overlay uk-overlay-primary">
                    <h2 style="color:#fcb421"><?php echo $branch; ?></h2>                    
                        <form class="uk-form-stacked" action="result.php">

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Buscar por título, autor, meio de expressão ou gênero e forma - <a href="result.php">ou clique aqui para ver todos os registros</a></label>
                                <div class="uk-form-controls">
                                    <input class="uk-input" style="background-color: #fff; color:#333" id="form-stacked-text" type="text" placeholder="<?php echo $t->gettext('Buscar por título, autor, meio de expressão ou gênero e forma'); ?>" name="search[]" data-validation="required">
                                </div>
                            </div>

                            <button class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom">Buscar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
        
        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span>Sugestões de busca</span></h1>        
            <div class="uk-child-width-expand@s uk-text-center" uk-grid>
                <div class="uk-card">
                    <h3 class="uk-card-title">Meio de expressão (10 termos mais usados na base)</h3>
                    <ul class="uk-list uk-list-divider">
                        <?php paginaInicial::facet_inicio("USP.meio_de_expressao"); ?>
                    </ul>                      
                </div>
                <div class="uk-card">
                    <h3 class="uk-card-title">Gênero e forma (10 termos mais usados na base)</h3>
                    <ul class="uk-list uk-list-divider">
                        <?php paginaInicial::facet_inicio("USP.about.genero_e_forma"); ?>
                    </ul>                      
                </div>
                <div class="uk-card">
                    <h3 class="uk-card-title">Compositores (10 compositores com mais obras na base)</h3>
                    <ul class="uk-list uk-list-divider">
                        <?php paginaInicial::facet_inicio("author.person.name"); ?>
                    </ul>                      
                </div>                                   
            </div>
        </div>  
        
        
        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span>Últimos registros</span></h1>
            <?php paginaInicial::ultimos_registros();?>
        </div>                         
            
        <hr class="uk-grid-divider">
            
        <?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>