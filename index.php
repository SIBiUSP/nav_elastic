<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php';
	    require 'inc/meta-header.php';
        ?>
        <title><?php echo $branch; ?></title>
        <!-- Facebook Tags - START -->
        <!--<meta property="og:locale" content="pt_BR">-->
	<meta property="og:locale" content="<?php echo $locale ?>">
        <meta property="og:url" content="//bdpi.usp.br">
        <meta property="og:title" content="<?php echo $t->gettext(''.$branch.''); ?> - <?php echo $t->gettext('Página Principal'); ?>">
        <meta property="og:site_name" content="<?php echo $t->gettext(''.$branch.''); ?>">
        <meta property="og:description" content="<?php echo $t->gettext(''.$branch_description.''); ?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
        <meta property="og:type" content="website">
        <!-- Facebook Tags - END -->

    </head>

    <body style="height: 100vh; min-height: 40em; position: relative;">

        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>
        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-margin" style="position: relative;">

            <div class="uk-width-3-4@s uk-child-width-3-4@m uk-align-center" uk-grid>
                <div class="uk-card uk-card-default uk-card-hover uk-card-small uk-card-body" style="margin-top: 2.5em;">
                    <h2 class="uk-align-center uk-text-center" style="color:#1094ab"><strong><?php echo $t->gettext(''.$branch.''); ?></strong></h2>
                    <p class="uk-form-label uk-text-right"><?php echo Homepage::totalProducao($t); ?></p>
                    <form class="uk-form-stacked" action="result.php">
                        <div class="uk-margin">
                            <!--<label class="uk-form-label" for="form-stacked-text"><?php echo $t->gettext('Termos de busca'); ?></label>-->
                            <div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
                                <button class="uk-search-icon-flip " style="width: 5em;" uk-search-icon></button>
                                <input class="uk-input" id="form-stacked-text" type="search" placeholder="<?php echo $t->gettext('Pesquise por termo ou autor'); ?>" name="search[]">
                            </div>
                        </div>
            		    <ul uk-accordion>
            		    <li>
            			<a class="uk-accordion-title uk-text-small uk-text-right" href="#"><?php echo $t->gettext('Filtros'); ?></a>
            		    <div class="uk-accordion-content">
                            <div class="uk-margin">
                                <label hidden class="uk-form-label" for="form-stacked-select"><?php echo $t->gettext('Selecione a base'); ?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="filter[]">
                                        <option selected value><?php echo $t->gettext('Todas as bases'); ?></option>
                                        <option value="base:&quot;Produção científica&quot;" style="color:#333"><?php echo $t->gettext('Produção Científica'); ?></option>
                                        <option value="base:&quot;Teses e dissertações&quot;" style="color:#333"><?php echo $t->gettext('Teses e Dissertações'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label hidden class="uk-form-label" for="form-stacked-select"><?php echo $t->gettext('Selecione uma Unidade USP para filtrar a busca'); ?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="filter[]">
                                        <option selected value><?php echo $t->gettext('Todas as Unidades USP'); ?></option>
                                        <?php foreach($unidades as $key => $value ): ?>
                                            <option value="<?php echo $key; ?>" style="color:#333"><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
            		    </div><!-- FIM - uk-accordion-content -->
            		    </li>
            		    </ul>
                    </form>
                </div>
            </div>
            
        </div>
        <?php require 'inc/footer.php'; ?>





<?php require 'inc/offcanvas.php'; ?>


    </body>
</html>
