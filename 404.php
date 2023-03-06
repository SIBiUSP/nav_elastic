<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
	<?php
            http_response_code(404);
            include('inc/config.php'); 
            include('inc/meta-header.php');
        ?>
            <title>Entre em contato</title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">

        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>
        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-margin" style="position: relative;">
            
	    <h1 class="uk-text-lead uk-align-center uk-text-center" style="color:#1094ab; margin-top: 1em;">Não foi possível encontrar a página solicitada.</h1>
	    <p class="uk-align-center uk-text-center">Realize uma nova pesquisa:</p>
            <div class="uk-width-3-4@s uk-child-width-3-4@m uk-align-center" uk-grid>
                <div class="uk-card uk-card-default uk-card-hover uk-card-small uk-card-body" style="margin-top: 2.5em;">
                    <h2 class="uk-align-center uk-text-center" style="color:#1094ab"><strong><?php echo $t->gettext(''.$branch.''); ?></strong></h2>
                    <form class="uk-form-stacked" action="result.php">
                        <div class="uk-margin">
                            <!--<label class="uk-form-label" for="form-stacked-text"><?php echo $t->gettext('Termos de busca'); ?></label>-->
                            <div class="uk-form-controls uk-margin uk-search uk-search-default" style="width: 100%">
                                <button class="uk-search-icon-flip" style="width: 5em;" uk-search-icon="ratio: 1.25"></button>
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
                                        <option disabled selected value><?php echo $t->gettext('Todas as bases'); ?></option>
                                        <option value="base:&quot;Produção científica&quot;" style="color:#333"><?php echo $t->gettext('Produção Científica'); ?></option>
                                        <option value="base:&quot;Teses e dissertações&quot;" style="color:#333"><?php echo $t->gettext('Teses e Dissertações'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label hidden class="uk-form-label" for="form-stacked-select"><?php echo $t->gettext('Selecione uma Unidade USP para filtrar a busca'); ?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="filter[]">
                                        <option disabled selected value><?php echo $t->gettext('Todas as Unidades USP'); ?></option>
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

