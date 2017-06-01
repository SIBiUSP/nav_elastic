<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>         
        <title>BDPI USP - Busca Avançada</title>
    </head>

    <body>
        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>

		<?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top">
            <h1>Busca avançada</h1>
                <div class="uk-width-1-1@m">
                    <div class="uk-alert uk-alert-large">
                        <form class="uk-form" role="form" action="result.php" method="get">
                            <fieldset data-uk-margin>
                                <legend>String de busca avançada</legend>
                                <p>Selecionar campos para realizar a busca: </p>
                                <label><input type="checkbox" name="fields[]" value="name" checked> Título</label>
                                <label><input type="checkbox" name="fields[]" value="author.person.name" checked> Autores</label>
                                <label><input type="checkbox" name="fields[]" value="authorUSP.name" checked> Autores USP</label>
                                <label><input type="checkbox" name="fields[]" value="unidadeUSP"> Unidade USP</label>
                                <label><input type="checkbox" name="fields[]" value="authorUSP.departament"> Departamento</label>
                                <label><input type="checkbox" name="fields[]" value="about" checked> Assuntos</label>
                                <label><input type="checkbox" name="fields[]" value="author.person.affiliation.name"> Colaboração institucional</label>
                                <label><input type="checkbox" name="fields[]" value="funder"> Agência de Fomento</label>
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
                                    <label for="amount">Selecionar período de tempo:</label>
                                    <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;" name="search[]">
                                </p>

                                <div id="slider-range"></div>                                
                                <br/>
                                <textarea type="text" class="uk-form-width-large" placeholder="Insira sua string de busca avançada" name="search[]" data-validation="required"></textarea>
                                <button class="uk-button" type="submit"><?php echo $t->gettext('Buscar'); ?></button>
                                <br/><br/><br/><a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html" target="_blank">Consultar referência</a>
                            </fieldset>
                        </form>
                    </div>
                </div>
            <hr class="uk-grid-divider">
            
            <?php include('inc/footer.php'); ?>

        </div>
        
    <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>