<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            require 'inc/config.php'; 
            require 'inc/functions.php';
            require 'inc/meta-header.php'; 
        ?>         
        <title>BDPI USP - <?php echo $t->gettext('Busca avançada'); ?></title>
    </head>

    <body>
        <?php
        if (file_exists('inc/analyticstracking.php')) {
            include_once 'inc/analyticstracking.php';
        }
        require 'inc/navbar.php';
        ?>
        <div class="uk-container uk-margin-large-top">
            <h1><?php echo $t->gettext('Busca avançada'); ?></h1>
                <div class="uk-width-1-1@m">
                    <div class="uk-alert uk-alert-large">
                        <form class="uk-form" role="form" action="result.php" method="get">
                            <fieldset data-uk-margin>
                                <legend><?php echo $t->gettext('String de busca avançada'); ?></legend>
                                <p><?php echo $t->gettext('Selecionar os campos para realizar a busca:'); ?></p>
                                <label><input type="checkbox" name="fields[]" value="name" checked> <?php echo $t->gettext('Título'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="author.person.name" checked> <?php echo $t->gettext('Autores'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="authorUSP.name" checked> <?php echo $t->gettext('Autores USP'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="unidadeUSP"> <?php echo $t->gettext('Unidades USP'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="authorUSP.departament"> <?php echo $t->gettext('Departamento'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="about" checked> <?php echo $t->gettext('Assuntos'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="author.person.affiliation.name"> <?php echo $t->gettext('Colaboração institucional'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="funder"> <?php echo $t->gettext('Agência de fomento'); ?></label>
                                <label><input type="checkbox" name="fields[]" value="sysno"> Sysno</label>
                                <br/>
                                <script>
                                    $( function() {
                                    $( "#slider-range" ).slider({
                                        range: true,
                                        min: 1900,
                                        max: 2030,
                                        values: [ 1900, 2030 ],
                                        slide: function( event, ui ) {
                                        $( "#amount" ).val( "datePublished:[" + ui.values[ 0 ] + " TO " + ui.values[ 1 ] + "]" );
                                        }
                                    });
                                    $( "#amount" ).val( "datePublished:[" + $( "#slider-range" ).slider( "values", 0 ) +
                                        " TO " + $( "#slider-range" ).slider( "values", 1 ) + "]");
                                    } );
                                </script>
                                <p>
                                    <label for="amount"><?php echo $t->gettext('Selecionar período de tempo'); ?>:</label>
                                    <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;" name="search[]">
                                </p>

                                <div id="slider-range"></div>                                
                                <br/>
                                <textarea type="text" class="uk-form-width-large" placeholder="Insira sua string de busca avançada" name="search[]" data-validation="required"></textarea>
                                <button class="uk-button" type="submit"><?php echo $t->gettext('Buscar'); ?></button>
                                <br/><br/><br/><a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html" target="_blank">Consultar referência</a>
                            </fieldset>
                        </form>
                        <form role="form" action="result.php" method="get">
                            <fieldset>
                                <legend class="uk-legend"><?php echo $t->gettext('Número USP'); ?></legend>
                                <input class="uk-input" id='searchCodpes' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                <button class="uk-button uk-button-default" type="submit" onclick="document.getElementById('searchCodpes').value = 'authorUSP.codpes:' + String.fromCharCode(34) + document.getElementById('searchCodpes').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
                            </fieldset>
                        </form>
                        <form role="form" action="autor.php" method="get">
                            <fieldset>
                                <legend class="uk-legend"><?php echo $t->gettext('Resultados em ABNT por autor USP'); ?></legend>
                                <input class="uk-input" id='searchCodpesABNT' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                <input type="hidden" name="format" value="abnt">
                                <button class="uk-button uk-button-default" type="submit" onclick="document.getElementById('searchCodpesABNT').value = 'authorUSP.codpes:' + String.fromCharCode(34) + document.getElementById('searchCodpesABNT').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
                            </fieldset>
                        </form>
                        <form role="form" action="autor.php" method="get">
                            <fieldset>
                                <legend class="uk-legend"><?php echo $t->gettext('Resultados em Formato Tabela por autor USP'); ?></legend>
                                <input class="uk-input" id='searchCodpesTable' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                <input type="hidden" name="format" value="table">
                                <button class="uk-button uk-button-default" type="submit" onclick="document.getElementById('searchCodpesTable').value = 'authorUSP.codpes:' + String.fromCharCode(34) + document.getElementById('searchCodpesTable').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
                            </fieldset>
                        </form>
                        <form role="form" action="autor.php" method="get">
                            <fieldset>
                                <legend class="uk-legend"><?php echo $t->gettext('Resultados em Formato RIS por autor USP'); ?></legend>
                                <input class="uk-input" id='searchCodpesRIS' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                <input type="hidden" name="format" value="RIS">
                                <button class="uk-button uk-button-default" type="submit" onclick="document.getElementById('searchCodpesRIS').value = 'authorUSP.codpes:' + String.fromCharCode(34) + document.getElementById('searchCodpesRIS').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
                            </fieldset>
                        </form>                                                                                 
                        <form role="form" action="result.php" method="get" name="searchIBox">
                            <fieldset>
                                <legend class="uk-legend">Assunto do Vocabulário Controlado</legend>


                                <label><a href="#" data-popterms-server="inc/popterms/server/"
                                            data-popterms-vocabulary="VOCAB"
                                            data-popterms-target="#vocab"> Selecione um termo do Vocabulário Controlado da USP</a>
                                </label>            
                                <br/>
                                <input id="vocab" class="uk-input" type="text" name="search[]" data-validation="required">
                                <button class="uk-button uk-button-default" type="submit" onclick="document.getElementById('vocab').value = 'about:' + String.fromCharCode(34) + document.getElementById('vocab').value.trim() + String.fromCharCode(34)" ><?php echo $t->gettext('Buscar'); ?></button>
                                                                            
                            </fieldset>
                        </form>                            
                    </div>
                </div>
            <hr class="uk-grid-divider">
            
            <?php require 'inc/footer.php'; ?>

        </div>
        
    <?php require 'inc/offcanvas.php'; ?>

    <!-- PopTerms Client JavaScript -->
    <script src="inc/popterms/client/js/popterms.js"></script>
    <script>
        PopTerms.size(400, 500);
        PopTerms.separator = " - ";
    </script>    
        
    </body>
</html>