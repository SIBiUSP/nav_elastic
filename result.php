<!DOCTYPE html>
<?php

    require 'inc/config.php'; 
    require 'inc/functions.php';

    $result_get = get::analisa_get($_GET);
    $limit = $result_get['limit'];
    $page = $result_get['page'];

if (isset($_GET["sort"])) {        
    $result_get['query']["sort"][$_GET["sort"]]["unmapped_type"] = "long";
    $result_get['query']["sort"][$_GET["sort"]]["missing"] = "_last";
    $result_get['query']["sort"][$_GET["sort"]]["order"] = "desc";
    $result_get['query']["sort"][$_GET["sort"]]["mode"] = "max";
} else {
    $result_get['query']['sort']['datePublished.keyword']['order'] = "desc";
}

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = $limit;
    $params["from"] = $result_get['skip'];
    $params["body"] = $result_get['query']; 

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];  

?>
<html>
<head>
    <?php require 'inc/meta-header.php'; ?>       
    <title><?php echo $branch_abrev; ?> - Resultado da busca</title>    

    <?php if ($year_result_graph == true) : ?>
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="inc/jquery/3.2.2/d3.v3.min.js"></script>
        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
    <?php endif; ?>

    <!-- Altmetric Script -->
    <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
    
    <!-- PlumX Script -->
    <script type="text/javascript" src="//d39af2mgp1pqhg.cloudfront.net/widget-popup.js"></script>

    
</head>
    <body>
        <?php require 'inc/navbar.php'; ?>
        <br/><br/><br/>

        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>        
 
        <div class="uk-container">
        <div class="uk-width-1-1@s uk-width-1-1@m">
            <nav class="uk-navbar-container uk-margin" uk-navbar>
                <div class="nav-overlay uk-navbar-left">
                    <a class="uk-navbar-item uk-logo" uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"><?php echo $t->gettext('Clique para uma nova pesquisa'); ?></a>
                </div>
                <div class="nav-overlay uk-navbar-right">
                    <a class="uk-navbar-toggle" uk-search-icon uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
                </div>
                <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
                <div class="uk-navbar-item uk-width-expand">
                <form class="uk-search uk-search-navbar uk-width-1-1">
                    <input type="hidden" name="fields[]" value="name">
                    <input type="hidden" name="fields[]" value="author.person.name">
                    <input type="hidden" name="fields[]" value="authorUSP.name">
                    <input type="hidden" name="fields[]" value="about">
                    <input type="hidden" name="fields[]" value="description"> 	    
                    <input class="uk-search-input" type="search" name="search[]" placeholder="<?php echo $t->gettext('Nova pesquisa...'); ?>" autofocus>
                    </form>
                </div>

                <a class="uk-navbar-toggle" uk-close uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>

                </div>
            </nav>
        </div>
        <div class="uk-width-1-1@s uk-width-1-1@m">

        <!-- List of filters - Start -->
        <?php if (!empty($_SERVER["QUERY_STRING"])) : ?>
        <p class="uk-margin-top" uk-margin>
            <a class="uk-button uk-button-default uk-button-small" href="index.php"><?php echo $t->gettext('Começar novamente'); ?></a>	
            <?php 
            if (!empty($_GET["search"])) {
                foreach ($_GET["search"] as $querySearch) {
                    $querySearchArray[] = $querySearch;
                    $name_field = explode(":", $querySearch);
                    $querySearch = str_replace($name_field[0].":", "", $querySearch);
                    $diff["search"] = array_diff($_GET["search"], $querySearchArray);
                    $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                    echo '<a class="uk-button uk-button-default uk-button-small" href="http://'.$url_push.'">'.$querySearch.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                    unset($querySearchArray); 	
                }
            }
                
            if (!empty($_GET["filter"])) {
                foreach ($_GET["filter"] as $filters) {
                    $filters_array[] = $filters;
                    $name_field = explode(":", $filters);
                    $filters = str_replace($name_field[0].":", "", $filters);
                    $diff["filter"] = array_diff($_GET["filter"], $filters_array);
                    $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                    echo '<a class="uk-button uk-button-primary uk-button-small" href="http://'.$url_push.'">Filtrado por: '.$filters.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                    unset($filters_array);
                }
            }
            
            if (!empty($_GET["notFilter"])) {
                foreach ($_GET["notFilter"] as $notFilters) {
                    $notFiltersArray[] = $notFilters;
                    $name_field = explode(":", $notFilters);
                    $notFilters = str_replace($name_field[0].":", "", $notFilters);
                    $diff["notFilter"] = array_diff($_GET["notFilter"], $notFiltersArray);
                    $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                    echo '<a class="uk-button uk-button-danger uk-button-small" href="http://'.$url_push.'">Ocultando: '.$notFilters.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                    unset($notFiltersArray);
                }
            }                 
            ?>
            
        </p>
        <?php endif;?> 
        <!-- List of filters - End -->
        </div>

        <div class="uk-grid-divider" uk-grid>
            <div class="uk-width-1-4@s uk-width-2-6@m">
                    <!-- Facetas - Início -->
                    <h3><?php echo $t->gettext('Refinar busca'); ?></h3>
                        <hr>
                        <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <?php
                                $facets = new Facets();
                                $facets->query = $result_get['query'];
                            
                            if (!isset($_GET["search"])) {
                                $_GET["search"] = null; 
                            }                            

                                $facets->facet("base", 10, $t->gettext('Bases'), null, "_term", $_GET["search"]);
                                $facets->facet("type", 100, $t->gettext('Tipo de material'), null, "_term", $_GET["search"]);
                                $facets->facet("unidadeUSP", 200, $t->gettext('Unidades USP'), null, "_term", $_GET["search"]);
                                $facets->facet("authorUSP.departament", 50, $t->gettext('Departamento'), null, "_term", $_GET["search"]);
                                $facets->facet("author.person.name", 30, $t->gettext('Autores'), null, "_term", $_GET["search"]);
                                $facets->facet("authorUSP.name", 50, $t->gettext('Autores USP'), null, "_term", $_GET["search"]);
                                $facets->facet("datePublished", 120, $t->gettext('Ano de publicação'), "desc", "_term", $_GET["search"]);
                                $facets->facet("about", 50, $t->gettext('Assuntos'), null, "_term", $_GET["search"]);
                                $facets->facet("language", 40, $t->gettext('Idioma'), null, "_term", $_GET["search"]);
                                $facets->facet("isPartOf.name", 50, $t->gettext('Título da fonte'), null, "_term", $_GET["search"]);
                                $facets->facet("publisher.organization.name", 50, $t->gettext('Editora'), null, "_term", $_GET["search"]);
                                $facets->facet("releasedEvent", 50, $t->gettext('Nome do evento'), null, "_term", $_GET["search"]);
                                $facets->facet("country", 200, $t->gettext('País de publicação'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.grupopesquisa", 100, "Grupo de pesquisa", null, "_term", $_GET["search"]);
                                $facets->facet("funder.name", 50, $t->gettext('Agência de fomento'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.indexacao", 50, $t->gettext('Indexado em'), null, "_term", $_GET["search"]);
                            ?>
                            <li class="uk-nav-header"><?php echo $t->gettext('Colaboração institucional'); ?></li>
                            <?php 
                                $facets->facet("author.person.affiliation.name", 50, $t->gettext('Afiliação dos autores externos normalizada'), null, "_term", $_GET["search"]);
                                $facets->facet("author.person.affiliation.name_not_found", 50, $t->gettext('Afiliação dos autores externos não normalizada'), null, "_term", $_GET["search"]);                                    
                                $facets->facet("author.person.affiliation.location", 50, $t->gettext('País das instituições de afiliação dos autores externos'), null, "_term", $_GET["search"]);  
                                $facets->facet("author.person.affiliation.locationTematres", 50, $t->gettext('País Tematres'), null, "_term", $_GET["search"]);
                            ?>
                            <li class="uk-nav-header"><?php echo $t->gettext('Métricas do periódico'); ?></li>
                            <?php 
                                $facets->facet("USP.qualis.qualis.2016.area", 50, $t->gettext('Qualis 2013/2016 - Área'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.qualis.qualis.2016.nota", 50, $t->gettext('Qualis 2013/2016 - Nota'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.qualis.qualis.2016.area_nota", 50, $t->gettext('Qualis 2013/2016 - Área / Nota'), null, "_term", $_GET["search"]);
                            ?>
                            <?php
                                //$facets->facet("USP.WOS.coverage", 50, $t->gettext('Índices da Web of Science'), null, "_term", $_GET["search"]);
                                //$facets->facet_range("USP.JCR.JCR.2016.Journal_Impact_Factor", 100, "JCR - Journal Impact Factor - 2016");
                                //$facets->facet_range("USP.JCR.JCR.2016.IF_without_Journal_Self_Cites", 100, "JCR - Journal Impact Factor without Journal Self Cites - 2016");
                                //$facets->facet_range("USP.JCR.JCR.2016.Eigenfactor_Score", 100, "JCR - Eigenfactor Score - 2016");
                                $facets->facet_range("USP.citescore.citescore.2017.citescore", 100, "Scopus - Citescore - 2017");
                                $facets->facet_range("USP.citescore.citescore.2017.SJR", 100, "Scopus - SJR - 2017");
                                $facets->facet_range("USP.citescore.citescore.2017.SNIP", 100, "Scopus - SNIP - 2017");
                                //$facets->facet("USP.citescore.citescore.2016.open_access", 50, $t->gettext('Acesso aberto'), null, "_term", $_GET["search"]);
                                
                            ?>
                            <!--
                            <li class="uk-nav-header">< ?php echo $t->gettext('Métricas no nível do artigo'); ?></li> 
                            < ?php
                                $facets->facet_range("USP.aminer.num_citation",100,$t->gettext('Citações no AMiner'),"INT");
                                $facets->facet_range("USP.opencitation.num_citations",100,$t->gettext('Citações no OpenCitations'),"INT");
                            ?>
                            -->                                   
                            <li class="uk-nav-header"><?php echo $t->gettext('Teses e Dissertações'); ?></li>    
                            <?php
                                $facets->facet("inSupportOf", 30, $t->gettext('Tipo de tese'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.areaconcentracao", 100, "Área de concentração", null, "_term", $_GET["search"]);
                                $facets->facet("USP.programa_pos_sigla", 100, "Sigla do Departamento/Programa de Pós Graduação", null, "_term", $_GET["search"]);
                                $facets->facet("USP.programa_pos_nome", 100, "Departamento/Programa de Pós Graduação", null, "_term", $_GET["search"]);
                                $facets->facet("USP.about_BDTD", 50, $t->gettext('Palavras-chave do autor'), null, "_term", $_GET["search"]);
                            ?>
                        </ul>
                        <?php if (!empty($_SESSION['oauthuserdata'])) : ?> 
                            <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
                            <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <hr>
                            <?php
                                $facets->facet("USP.internacionalizacao", 10, "Internacionalização", null, "_term", $_GET["search"]);                            
                                $facets->facet("authorUSP.regime_de_trabalho", 50, $t->gettext('Regime de trabalho'), null, "_term", $_GET["search"]);
                                $facets->facet("authorUSP.funcao", 50, $t->gettext('Função'), null, "_term", $_GET["search"]);
                                $facets->facet("USP.CAT.date", 100, "Data de registro e alterações", "desc", "_term", $_GET["search"]);
                                $facets->facet("USP.CAT.cataloger", 100, "Catalogador", "desc", "_count", $_GET["search"]);
                                $facets->facet("authorUSP.codpes", 100, "Número USP", null, "_term", $_GET["search"]);
                                $facets->facet("isPartOf.issn", 100, "ISSN", null, "_term", $_GET["search"]);
                                $facets->facet("doi", 100, "DOI", null, "_term", $_GET["search"]);
                            ?>
                            </ul>
                        <?php endif; ?>
                        <!-- Facetas - Fim -->

                        <hr>

                        <!-- Limitar por data - Início -->
                        <form class="uk-text-small">
                            <fieldset>
                                <legend><?php echo $t->gettext('Limitar por data'); ?></legend>
                                <script>
                                    $( function() {
                                    $( "#limitar-data" ).slider({
                                    range: true,
                                    min: 1900,
                                    max: 2030,
                                    values: [ 1900, 2030 ],
                                    slide: function( event, ui ) {
                                        $( "#date" ).val( "datePublished:[" + ui.values[ 0 ] + " TO " + ui.values[ 1 ] + "]" );
                                    }
                                    });
                                    $( "#date" ).val( "datePublished:[" + $( "#limitar-data" ).slider( "values", 0 ) +
                                    " TO " + $( "#limitar-data" ).slider( "values", 1 ) + "]");
                                    } );
                                </script>
                                <p>
                                <label for="date"><?php echo $t->gettext('Selecionar período de tempo'); ?>:</label>
                                <input class="uk-input" type="text" id="date" readonly style="border:0; color:#f6931f;" name="search[]">
                                </p>        
                                <div id="limitar-data" class="uk-margin-bottom"></div>
                                <?php if (!empty($_GET["search"])) : ?>
                                    <?php foreach($_GET["search"] as $search_expression): ?>
                                        <input type="hidden" name="search[]" value="<?php echo str_replace('"', '&quot;', $search_expression); ?>">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (!empty($_GET["filter"])) : ?>
                                    <?php foreach($_GET["filter"] as $filter_expression): ?>
                                        <input type="hidden" name="filter[]" value="<?php echo str_replace('"', '&quot;', $filter_expression); ?>">
                                    <?php endforeach; ?>
                                <?php endif; ?>                                
                                <button class="uk-button uk-button-primary uk-button-small"><?php echo $t->gettext('Limitar datas'); ?></button>
                            </fieldset>        
                        </form>
                        <!-- Limitar por data - Fim -->

                        <hr>
                    <!--
                        <h3 class="uk-panel-title">< ?php echo $t->gettext('Visualização em rede'); ?></h3>
                    <p>< ?php echo $t->gettext('Os gráficos demoram 15 segundos para carregar'); ?></p>
                    <hr>
                    <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">                    

                    < ?php 
                        $value = ''.$_SERVER["QUERY_STRING"].'&gexf_field=unidadeUSP';
                        $sha1_unidade = sha1($value);
                    ?>
                    <script>                    
                    function gexf_unidadeUSP() {
                        $.get("tools/gexf/update_bdpi.php?< ?=$value?>");
                        setTimeout(function(){
                            document.getElementById("ifr").src="tools/gexf/index.html#data/bdpi-< ?=$sha1_unidade?>.gexf";
                        }, 15000);                        
                    }
                    </script>
                    <li><a class="" href="#modal-full-network" onClick='gexf_unidadeUSP()' uk-toggle>Rede de Coautoria entre Unidades USP</a></li>

                    <div id="modal-full-network" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                            <div class="uk-modal-header">
                                <h4>Rede de Coautoria entre Unidades USP</h4>
                            </div>
                            <div class="uk-modal-body">
                                <div class="uk-grid-collapse uk-child-width-1-1@s uk-flex-middle" uk-grid>                            
                                    <iframe id='ifr' height="800px" width="80vh"></iframe> 
                                </div>
                            </div>
                            <div class="uk-modal-footer">
                            </div>
                        </div>
                    </div>
                   
                    < ?php 
                        $value_authors = ''.$_SERVER["QUERY_STRING"].'&gexf_field=authorUSP.name';
                        $sha1_authors = sha1($value_authors);
                    ?>

                    <script>                    
                    function gexf_authors() {
                        $.get("tools/gexf/update_bdpi.php?< ?=$value_authors?>");
                        setTimeout(function(){
                            document.getElementById("ifr-authors").src="tools/gexf/index.html#data/bdpi-< ?=$sha1_authors?>.gexf";
                        }, 15000);                        
                    }
                    </script>
                    <li><a class="" href="#modal-authors" onClick='gexf_authors()' uk-toggle>Rede de Coautoria entre Autores com vínculo com a USP</a></li>

                    <div id="modal-authors" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>

                            <div class="uk-modal-header">
                                <h4>Rede de Coautoria entre Autores com vínculo com a USP</h4>
                            </div>
                            <div class="uk-modal-body">
                                <div class="uk-grid-collapse uk-child-width-1-1@s uk-flex-middle" uk-grid>                            
                                    <iframe id='ifr-authors' height="800px" width="100vh"></iframe> 
                                </div>
                            </div>
                            <div class="uk-modal-footer">
                            </div>
                        </div>
                    </div>
                    
                    < ?php 
                        $value_about = ''.$_SERVER["QUERY_STRING"].'&gexf_field=about';
                        $sha1_about = sha1($value_about);
                    ?>
                    <script type="text/javascript">                    
                        function gexf_about() {
                            $.get("tools/gexf/update_bdpi.php?< ?=$value_about?>");
                            setTimeout(function(){
                                document.getElementById("ifr-about").src="tools/gexf/index.html#data/bdpi-< ?=$sha1_about?>.gexf";
                            }, 15000);                        
                        }
                    </script>
                    <li><a class="" href="#modal-about" onClick='gexf_about()' uk-toggle>Rede de Assuntos</a></li>

                    <div id="modal-about" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>

                            <div class="uk-modal-header">
                                <h4>Rede de Assuntos</h4>
                            </div>
                            <div class="uk-modal-body">                            
                                <div class="uk-grid-collapse uk-child-width-1-1@s uk-flex-middle" uk-grid>
                                    <iframe id='ifr-about' height="800px" width="100vh"></iframe> 
                                </div>
                            </div>
                            <div class="uk-modal-footer">
                            </div>                                
                        </div>
                    </div>
                    
                    -->
                    </ul> 
                <hr>                                

                <!-- Gerar relatório - Início -->
                <fieldset>
                    <legend>Gerar relatório</legend>                  
                    <div class="uk-form-row"><a href="<?php echo 'report.php?'.$_SERVER["QUERY_STRING"].''; ?>" class="uk-button uk-button-primary">Gerar relatório</a>
                    </div>
                </fieldset>
                <!-- Gerar relatório - Fim -->
                        
                <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                <hr>
                <!-- Exportar resultados -->
                <h3 class="uk-panel-title"><?php echo $t->gettext('Exportar'); ?></h3>
                <p>Limitado aos primeiros 10000 resultados</p>
                <ul>
                    <li><a class="" href="tools/export.php?<?php echo ''.$_SERVER["QUERY_STRING"].'&format=table'; ?>">Exportar resultados em formato tabela</a></li>
                    <li><a class="" href="tools/export.php?<?php echo ''.$_SERVER["QUERY_STRING"].'&format=ris'; ?>">Exportar resultados em formato RIS</a></li>
                </ul>
                <!-- Exportar resultados - Fim -->        
                                                    
                <?php endif; ?>
                                        
            </div>
            
            <div class="uk-width-3-4@s uk-width-4-6@m">
            
            <!-- Gráfico do ano - Início -->
            <?php if ($year_result_graph == true && $total > 0 ) : ?>
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <?php $ano_bar = Results::generateDataGraphBar($result_get['query'], 'datePublished', "_term", 'desc', 'Ano', 10); ?>
                    <div id="ano_chart" class="uk-visible@l"></div>
                    <script type="text/javascript">
                        var graphdef = {
                            categories : ['<?= $t->gettext('Ano') ?>'],
                            dataset : {
                                '<?= $t->gettext('Ano') ?>' : [<?= $ano_bar; ?>]
                            }
                        }
                        var chart = uv.chart ('Bar', graphdef, {
                            meta : {
                                position: '#ano_chart',
                                caption : '<?= $t->gettext('Ano de publicação') ?>',
                                hlabel : '<?= $t->gettext('Ano') ?>',
                                vlabel : '<?= $t->gettext('registros') ?>'
                            },
                            graph : {
                                orientation : "Vertical"
                            },
                            dimension : {
                                width: 650,
                                height: 110
                            }
                        })
                    </script>                        
                    </div>
            <?php endif; ?>
                <!-- Gráfico do ano - Fim -->    
            
            <!-- Vocabulário controlado - Início -->
            <?php if(isset($_GET["search"])) : ?>    
                <?php foreach ($_GET["search"] as $expressao_busca) : ?>    
                    <?php if (preg_match("/\babout\b/i", $expressao_busca, $matches)) : ?>
                        <div class="uk-alert-primary" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <?php $assunto = str_replace("about:", "", $expressao_busca); USP::consultar_vcusp(str_replace("\"", "", $assunto)); ?>
                        </div>   
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>    
            <!-- Vocabulário controlado - Fim -->
                
            <!-- Navegador de resultados - Início -->
            <?php ui::pagination($page, $total, $limit, $t); ?>
            <!-- Navegador de resultados - Fim -->                    
                
            <hr class="uk-grid-divider">

                <!-- Resultados -->
                <div class="uk-width-1-1 uk-margin-top uk-description-list-divider">                        
                    <ul class="uk-list uk-list-divider">   
                        <?php 
                        foreach ($cursor["hits"]["hits"] as $r) {
                            $record = new Record($r, $show_metrics);
                            $record->simpleRecordMetadata($t);
                        }
                        ?>
                    </ul> 
                    
                <hr class="uk-grid-divider">

                <!-- Navegador de resultados - Início -->
                <?php ui::pagination($page, $total, $limit, $t); ?>
                <!-- Navegador de resultados - Fim --> 
                
            </div>
        </div>
        <hr class="uk-grid-divider">
        </div>
        <?php require 'inc/footer.php'; ?>          
        </div>

        <script>
        $('[data-uk-pagination]').on('select.uk.pagination', function(e, pageIndex){
            var url = window.location.href.split('&page')[0];
            window.location=url +'&page='+ (pageIndex+1);
        });
        </script> 
        <script async src="https://badge.dimensions.ai/badge.js" charset="utf-8"></script>   

    <?php require 'inc/offcanvas.php'; ?> 
        
    </body>
</html>