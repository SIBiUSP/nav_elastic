<!DOCTYPE html>
<?php
/**
 * PHP version 7
 * Result page
 *
 * The page for display results of search.
 *
 * @category Search
 * @package  Nav_Elastic
 * @author   Tiago Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link     https://github.com/SIBiUSP/nav_elastic
 */
require 'inc/config.php';

if(isset($_GET["search"])){
	$_GET["search"] = search_sanitize($_GET["search"]);
}
array_walk_recursive($_GET, function (&$item, $key){
	$item = htmlspecialchars(strip_tags($item),ENT_NOQUOTES);
});


if (isset($_GET["search"])) {
    foreach ($_GET["search"] as $getSearch) {
        $getCleaned[] = input_sanitize(htmlspecialchars($getSearch, ENT_QUOTES));
    }
    unset($_GET["search"]);
    $_GET["search"] = $getCleaned;
}

if (isset($fields)) {
    $_GET["fields"] = $fields;
}

if(isset($_GET["page"]) && $_GET['page'] < 0){
	$_GET['page'] = 1;
	header('Location: //'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET));
}

$result_get = get::analisa_get($_GET);

$limit = $result_get['limit'];
$page = $result_get['page'];

if (isset($_GET["sort"])) {
    $result_get['query']["sort"][$_GET["sort"]]["unmapped_type"] = "long";
    $result_get['query']["sort"][$_GET["sort"]]["missing"] = "_last";
    $result_get['query']["sort"][$_GET["sort"]]["order"] = "desc";
    $result_get['query']["sort"][$_GET["sort"]]["mode"] = "max";
} 
if(empty($_GET["search"][0])){
    $result_get['query']['sort']['datePublished.keyword']['order'] = "desc";
}

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["size"] = $limit;
$params["from"] = $result_get['skip'];
$params["body"] = $result_get['query'];

//$params["body"]["query"]["bool"]["must"]["query_string"]["query"]="*";

$cursor = $client->search($params);
$total = $cursor["hits"]["total"];
$total_pages = ceil($total/$limit);

if($total_pages > 0 && $page > $total_pages){
	$_GET["page"] = $total_pages;
	header('Location: //'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET));
} else if ($total_pages == 0 && $page > 1){
	$_GET["page"] = 1;
        header('Location: //'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET));
}

?>
<html>
<head>
    <?php require 'inc/meta-header.php'; ?>
    <title><?php echo $branch_abrev; ?> - Resultado da busca</title>

    <?php if ($year_result_graph == true) : ?>
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="inc/jquery/d3.v3.min.js"></script>
        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
    <?php endif; ?>

</head>
<body style="min-height: 45em; position: relative;">
    <?php require 'inc/navbar.php'; ?>

    <?php
    if (file_exists("inc/analyticstracking.php")) {
        include_once "inc/analyticstracking.php";
    }
    ?>

    <div class="uk-container" style="position: relative; padding-bottom: 15em;">
        <div class="uk-width-1-1@s uk-width-1-1@m">
            <form class="uk-search uk-search-navbar uk-width-1-1" action="result.php">
                <div class="search uk-form-controls uk-margin uk-search uk-search-default uk-width-1-1@s uk-width-1-1@m uk-align-center">
                    <input type="hidden" name="fields[]" value="name">
                    <input type="hidden" name="fields[]" value="author.person.name">
                    <input type="hidden" name="fields[]" value="authorUSP.name">
                    <input type="hidden" name="fields[]" value="about">
                    <input type="hidden" name="fields[]" value="description">
                    <button class="search-button uk-search-icon-flip" uk-search-icon="ratio: 1"></button>
                    <input class="search-input uk-input" id="form-stacked-text" type="search" placeholder="<?php echo $t->gettext('Pesquise por termo ou autor'); ?>" name="search[]" value="<?php echo !empty($_GET['search'][0]) ? $_GET['search'][0] : ""; ?>">
                </div>
            </form>
        </div>
        <div class="uk-width-1-1@s uk-width-1-1@m">
            <p class="uk-margin-top" uk-margin>
                <a href="#offcanvas-slide" class="uk-button uk-button-small uk-text-small filtros" uk-toggle>
                    <?php echo $t->gettext('Filtros'); ?>
                </a> :
            <!-- List of filters - Start -->
            <?php if (!empty($_SERVER["QUERY_STRING"])) : ?>
                <?php
                if (!empty($_GET["search"][0])) {
			foreach ($_GET["search"] as $querySearch) {
                        $querySearchArray[] = $querySearch;
                        $name_field = explode(":", $querySearch);
                        $querySearch = str_replace($name_field[0].":", "", $querySearch);
                        $diff["search"] = array_diff($_GET["search"], $querySearchArray);
                        $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                        echo '<a class="uk-button uk-button-default uk-button-small uk-text-small" href="http://'.$url_push.'">'.$querySearch.' <span uk-icon="icon: close; ratio: 1"></span></a>';

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
                        echo '<a class="uk-button uk-button-default uk-button-small uk-text-small" href="http://'.$url_push.'">'.$filters.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                        unset($filters_array);
                    }
                }

                if (!empty($_GET["notFilter"])) {
                    $notFilterText = sizeof($_GET["notFilter"]) > 1 ? $t->gettext('Removidos') : $t->gettext('Removido');
                    echo '<span class="not-filter"> '. $notFilterText . ': </span>';
                    foreach ($_GET["notFilter"] as $notFilters) {
                        $notFiltersArray[] = $notFilters;
                        $name_field = explode(":", $notFilters);
                        $notFilters = str_replace($name_field[0].":", "", $notFilters);
                        $diff["notFilter"] = array_diff($_GET["notFilter"], $notFiltersArray);
                        $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                        echo '<a class="uk-button uk-button-default uk-button-small uk-text-small not-filter" href="http://'.$url_push.'">'.$notFilters.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                        unset($notFiltersArray);
                    }
                }
                ?>
                <a class="uk-text-small" href="index.php" style="float: right;"><?php echo "Limpar"/*$t->gettext('Começar novamente')*/; ?></a>
            
            <?php endif;?>
            <!-- List of filters - End -->
            </p>
        </div>
        <div class="uk-grid-divider" uk-grid>
            <div id="offcanvas-slide" uk-offcanvas>
                <div class="uk-offcanvas-bar uk-background-muted uk-text-secondary">
                    <h3 class="title"><?php echo $t->gettext('Filtros'); ?></h3>
            <!--<div class="uk-width-1-4@s uk-width-2-6@m">-->
                    <!-- Facetas - Início -->
                        <hr>
                        <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <?php
                                $facets = new Facets();
                                $facets->query = $result_get['query'];

                                if (!isset($_GET["search"])) {
                                    $_GET["search"] = null;
                                }
				if($sourcesFacet)
					$facets->facet("base", 10, $t->gettext('Bases'), null, "_term", $_GET["search"]);
				if($documentTypesFacet)
					$facets->facet("type", 100, $t->gettext('Tipo de material'), null, "_term", $_GET["search"]);
				if($degreeFacet)
					$facets->facet("inSupportOf", 100, $t->gettext('Grau'), null, "_term", $_GET["search"]);
				if($uspSchoolsFacet)
					$facets->facet("unidadeUSP", 200, $t->gettext('Unidades USP'), null, "_term", $_GET["search"], "uppercase");
				if($departamentFacet)
					$facets->facet("authorUSP.departament", 100, $t->gettext('Departamento'), null, "_term", $_GET["search"], "uppercase");
				if($authorsFacet)
					$facets->facet("author.person.name", 150, $t->gettext('Autores'), null, "_term", $_GET["search"]);
				if($authorsUSPFacet)
					$facets->facet("authorUSP.name", 150, $t->gettext('Autores USP'), null, "_term", $_GET["search"]);
				if($authorOccupationFacet && is_staffUser())
					$facets->facet("author.person.potentialAction", 100, $t->gettext('Função do autor'), null, "_term", $_GET["search"]);
				if($datePublishedFacet)
					$facets->facet("datePublished", 120, $t->gettext('Ano de publicação'), "desc", "_term", $_GET["search"]);
				if($subjectsFacet)
					$facets->facet("about", 50, $t->gettext('Assuntos'), null, "_term", $_GET["search"]);
				if($languageFacet)
					$facets->facet("language", 40, $t->gettext('Idioma'), null, "_term", $_GET["search"]);
				if($sourceTitleFacet)
					$facets->facet("isPartOf.name", 50, $t->gettext('Título da fonte'), null, "_term", $_GET["search"]);
				if($publisherFacet)
					$facets->facet("publisher.organization.name", 50, $t->gettext('Editora'), null, "_term", $_GET["search"], "uppercase");
				if($conferenceTitleFacet)
					$facets->facet("releasedEvent", 50, $t->gettext('Nome do evento'), null, "_term", $_GET["search"]);
				if($contriesFacet)
					$facets->facet("country", 200, $t->gettext('País de publicação'), null, "_term", $_GET["search"]);
				if($searchGroupFacet)
					$facets->facet("USP.grupopesquisa", 100, "Grupo de pesquisa", null, "_term", $_GET["search"]);
				if($fundingAgenciesFacet)
					$facets->facet("funder.name", 50, $t->gettext('Agência de fomento'), null, "_term", $_GET["search"]);
				if($indexedInFacet)
					$facets->facet("USP.indexacao", 50, $t->gettext('Indexado em'), null, "_term", $_GET["search"]);

			    ?>
			    <?php if($normalizedAffiliationFacet || $nonNormalizedAffiliationFacet || $contriesInstitutionsCollaborationFacet):?>
			    <li class="uk-nav-header"><?php echo $t->gettext('Colaboração institucional'); ?></li>
			    <?php endif; ?>
			    <?php
				if($normalizedAffiliationFacet)
					$facets->facet("author.person.affiliation.name", 50, $t->gettext('Afiliação dos autores externos normalizada'), null, "_term", $_GET["search"]);
				if($nonNormalizedAffiliationFacet)
					$facets->facet("author.person.affiliation.name_not_found", 50, $t->gettext('Afiliação dos autores externos não normalizada'), null, "_term", $_GET["search"]);
				if($contriesInstitutionsCollaborationFacet)
					$facets->facet("author.person.affiliation.location", 50, $t->gettext('País das instituições de afiliação dos autores externos'), null, "_term", $_GET["search"]);
			    
			    if($qualisAreaFacet || $qualisGradeFacet  || $qualisAreaGradeFacet):
                            ?>
                            <!--<li class="uk-nav-header"><?php echo $t->gettext('Métricas do periódico'); ?></li>-->
                            <?php
			    endif;
				if($qualisAreaFacet)
					$facets->facet("USP.qualis.qualis.2016.area", 50, $t->gettext('Qualis 2013/2016 - Área'), null, "_term", $_GET["search"]);
				if($qualisGradeFacet)
					$facets->facet("USP.qualis.qualis.2016.nota", 50, $t->gettext('Qualis 2013/2016 - Nota'), null, "_term", $_GET["search"]);
				if($qualisAreaGradeFacet)
					$facets->facet("USP.qualis.qualis.2016.area_nota", 50, $t->gettext('Qualis 2013/2016 - Área / Nota'), null, "_term", $_GET["search"]);
                            ?>
			    <?php if(($typeThesisFacet || $concentrationAreaFacet || $initialsDepartmentPostgraduateFacet || $departmentPostgraduateFacet || $keywordsFacet) && $branch_abrev == "ReP USP" ): ?>
                            <li class="uk-nav-header"><?php echo $t->gettext('Teses e Dissertações'); ?></li>-
			    <?php
			    endif;	    
				if($typeThesisFacet)
					$facets->facet("inSupportOf", 30, $t->gettext('Tipo de tese'), null, "_term", $_GET["search"]);
				if($concentrationAreaFacet)
					$facets->facet("USP.areaconcentracao", 100, "Área de concentração", null, "_term", $_GET["search"]);
				if($initialsDepartmentPostgraduateFacet)
					$facets->facet("USP.programa_pos_sigla", 100, "Sigla do Departamento/Programa de Pós Graduação", null, "_term", $_GET["search"]);
				if($departmentPostgraduateFacet)
					$facets->facet("USP.programa_pos_nome", 100, "Departamento/Programa de Pós Graduação", null, "_term", $_GET["search"]);
				if($keywordsFacet)
					$facets->facet("files.", 50, $t->gettext('Palavras-chave do autor'), null, "_term", $_GET["search"]);

                            ?>
                        </ul>
                        <?php if (!empty($_SESSION['oauthuserdata'])) : ?>
                            <h3 class="uk-panel-title uk-margin-top title">Informações administrativas</h3>
                            <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <hr>
			<?php
				if($contriesTematresFacet)
					$facets->facet("author.person.affiliation.locationTematres", 50, $t->gettext('País Tematres'), null, "_term", $_GET["search"]);
				if($internacionalizationFacet)
					$facets->facet("USP.internacionalizacao", 10, "Internacionalização", null, "_term", $_GET["search"]);
				if($impactFactorFacet)
					$facets->facet("USP.fatorimpacto", 100, "Fator de impacto - 590m", null, "_term", $_GET["search"]);
				if($workRegimeFacet)
					$facets->facet("authorUSP.regime_de_trabalho", 50, $t->gettext('Regime de trabalho'), null, "_term", $_GET["search"]);
				if($changeDateFacet)
					$facets->facet("USP.CAT.date", 100, "Data de registro e alterações", "desc", "_term", $_GET["search"]);
				if($cataloguerFacet)
					$facets->facet("USP.CAT.cataloger", 100, "Catalogador", "desc", "_count", $_GET["search"]);
				if($numberUSPFacet)
					$facets->facet("authorUSP.codpes", 100, "Número USP", null, "_term", $_GET["search"]);
				if($ISSNFacet)
					$facets->facet("isPartOf.issn", 100, "ISSN", null, "_term", $_GET["search"]);
				if($DOIFacet)
					$facets->facet("doi", 100, "DOI", null, "_term", $_GET["search"]);
				if($promotionAgencyCrossRefFacet)
					$facets->facet("USP.crossref.message.funder.name", 50, $t->gettext('Agência de fomento obtida na CrossRef'), null, "_term", $_GET["search"]);
				if($nonStandardExternalAffiliationFacet)
					$facets->rebuild_facet("author.person.affiliation.name_not_found", 50, $t->gettext('Afiliação dos autores externos não normalizada'), null, "_exists", $_GET["search"]);


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
                                <input class="uk-input" type="text" id="date" readonly style="border:0; color:#f6931f;" name="range[]">
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

                <?php if (!empty($_SESSION['oauthuserdata'])) : ?>
                <hr>
                <!-- Exportar resultados -->
                <h3 class="uk-panel-title title"><?php echo $t->gettext('Exportar'); ?></h3>
                <p>Limitado aos primeiros 10000 resultados</p>
                <ul>
                    <li><a class="" href="tools/export.php?<?php echo ''.$_SERVER["QUERY_STRING"].'&format=table'; ?>">Exportar resultados em formato tabela</a></li>
                    <li><a class="" href="tools/export.php?<?php echo ''.$_SERVER["QUERY_STRING"].'&format=ris'; ?>">Exportar resultados em formato RIS</a></li>
                    <li><a class="" href="tools/export.php?<?php echo ''.$_SERVER["QUERY_STRING"].'&format=bibtex'; ?>">Exportar resultados em formato Bibtex</a></li>
                </ul>
                <!-- Exportar resultados - Fim -->

                <?php endif; ?>

            </div>
        </div>

            <div class="uk-width-1-1">

            <!-- Vocabulário controlado - Início -->
            <?php if(isset($_GET["search"])) : ?>
                <?php foreach ($_GET["search"] as $expressao_busca) : ?>
                    <?php if (preg_match("/\babout\b/i", $expressao_busca, $matches)) : ?>
                        <div class="uk-alert-primary" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <?php $assunto = str_replace("about:", "", $expressao_busca); //USP::consultar_vcusp(str_replace("\"", "", $assunto)); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(isset($_GET["filter"])) : ?>
                <?php foreach ($_GET["filter"] as $expressao_busca) : ?>
                    <?php if (preg_match("/\babout\b/i", $expressao_busca, $matches)) : ?>
                        <div class="uk-alert-primary" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <?php $assunto = str_replace("about:", "", $expressao_busca); //USP::consultar_vcusp(str_replace("\"", "", $assunto)); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- Vocabulário controlado - Fim -->

            <!-- Informações sobre autores USP - Início
            < ?php if(isset($_GET["search"])) : ?>
                < ?php foreach ($_GET["search"] as $expressao_busca_codpes) : ?>
                    < ?php if (preg_match("/\bcodpes\b/i", $expressao_busca_codpes, $matches)) : ?>
                        <div class="uk-alert-primary" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        < ?php USP::consultar_codpes($expressao_busca_codpes); ?>
                        </div>
                    < ?php endif; ?>
                < ?php endforeach; ?>
            < ?php endif; ?>
            Informações sobre autores USP - Fim -->

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
            </div>
        </div>
        <!--<hr class="uk-grid-divider">-->


    </div>

    </div>
    <div style="position: relative; max-width: initial;">
        <?php require 'inc/footer.php'; ?>
    </div>

    <script>
    $('[data-uk-pagination]').on('select.uk.pagination', function(e, pageIndex){
        var url = window.location.href.split('&page')[0];
        window.location=url +'&page='+ (pageIndex+1);
    });
    </script>

<?php require 'inc/offcanvas.php'; ?>

</body>
</html>
