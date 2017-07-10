<!DOCTYPE html>
<?php
    include('inc/config.php'); 
    include('inc/functions.php');

    $result_get = get::analisa_get($_GET);
    $query = $result_get['query'];  
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $skip = $result_get['skip'];
    
    $query['sort'] = [
	['datePublished.keyword' => ['order' => 'desc']],
    ];    

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = $limit;
    $params["from"] = $skip;
    $params["body"] = $query;
    
    
    

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];

    /*pagination - start*/
    $get_data = $_GET;    
    /*pagination - end*/    

    /* Citeproc-PHP*/

    include 'inc/citeproc-php/CiteProc.php';
    $csl_abnt = file_get_contents('inc/citeproc-php/style/abnt.csl');
    $csl_apa = file_get_contents('inc/citeproc-php/style/apa.csl');
    $csl_nlm = file_get_contents('inc/citeproc-php/style/nlm.csl');
    $csl_vancouver = file_get_contents('inc/citeproc-php/style/vancouver.csl');
    $lang = "br";
    $citeproc_abnt = new citeproc($csl_abnt,$lang);
    $citeproc_apa = new citeproc($csl_apa,$lang);
    $citeproc_nlm = new citeproc($csl_nlm,$lang);
    $citeproc_vancouver = new citeproc($csl_nlm,$lang);
    $mode = "reference";

?>
<html>
    <head>
        <?php
            include('inc/meta-header.php'); 
        ?>        
        <title><?php echo $branch_abrev; ?> - Resultado da busca</title>
        <script src="inc/uikit/js/components/accordion.min.js"></script>
        <script src="inc/uikit/js/components/pagination.min.js"></script>
        <script src="inc/uikit/js/components/datepicker.min.js"></script>
        <script src="inc/uikit/js/components/tooltip.min.js"></script>
        

        <?php if ($year_result_graph == true) : ?>
            <!-- D3.js Libraries and CSS -->
            <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.2.2/d3.v3.min.js"></script>

            <!-- UV Charts -->
            <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
        <?php endif; ?>

        <!-- Altmetric Script -->
        <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
        
        <!-- PlumX Script -->
        <script type="text/javascript" src="//d39af2mgp1pqhg.cloudfront.net/widget-popup.js"></script>

        
    </head>
    <body>
        <?php include('inc/navbar.php'); ?>
        <br/><br/><br/>

        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
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
	    
		    <?php if (!empty($_SERVER["QUERY_STRING"])) : ?>
		    				    
			<p class="uk-margin-top" uk-margin>
				<a class="uk-button uk-button-default uk-button-small" href="index.php"><?php echo $t->gettext('Começar novamente'); ?></a>	
				<?php 
				
					if (!empty($_GET["search"])){
                        foreach($_GET["search"] as $filters) {
                            $filters_array[] = $filters;
                            $name_field = explode(":",$filters);	
                            $filters = str_replace($name_field[0].":","",$filters);				
                            $diff["search"] = array_diff($_GET["search"],$filters_array);						
                            $url_push = $_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($diff);
                            echo '<a class="uk-button uk-button-default uk-button-small" href="http://'.$url_push.'">'.$filters.' <span uk-icon="icon: close; ratio: 1"></span></a>';
                            unset($filters_array); 	
                        }
                    }	
	
				?>
				
			</p>
		    <?php endif;?> 
	    
	    
	    </div>	

            <div class="uk-grid-divider" uk-grid>
                <div class="uk-width-1-4@s uk-width-2-6@m">
                    <div class="uk-panel uk-panel-box">

                        <!-- Facetas - Início -->
                        <h3 class="uk-panel-title"><?php echo $t->gettext('Refinar busca'); ?></h3>
                            <hr>
                            <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                                <?php
                                    $facets = new facets();
                                    $facets->query = $query;
                                
                                    if (!isset($_GET["search"])) {
                                        $_GET["search"] = null;                                    
                                    }                            

                                    $facets->facet("base",10,$t->gettext('Bases'),null,"_term",$_GET["search"]);
                                    $facets->facet("type",100,$t->gettext('Tipo de material'),null,"_term",$_GET["search"]);
                                    $facets->facet("unidadeUSP",100,$t->gettext('Unidades USP'),null,"_term",$_GET["search"]);
                                    $facets->facet("authorUSP.departament",50,$t->gettext('Departamento'),null,"_term",$_GET["search"]);
                                    $facets->facet("author.person.name",30,$t->gettext('Autores'),null,"_term",$_GET["search"]);
                                    $facets->facet("authorUSP.name",50,$t->gettext('Autores USP'),null,"_term",$_GET["search"]);
                                    $facets->facet("datePublished",120,$t->gettext('Ano de publicação'),"desc","_term",$_GET["search"]);
                                    $facets->facet("about",50,$t->gettext('Assuntos'),null,"_term",$_GET["search"]);
                                    $facets->facet("language",40,$t->gettext('Idioma'),null,"_term",$_GET["search"]);
                                    $facets->facet("isPartOf.name",50,$t->gettext('É parte de ...'),null,"_term",$_GET["search"]);
                                    $facets->facet("publisher.organization.name",50,$t->gettext('Editora'),null,"_term",$_GET["search"]);
                                    $facets->facet("releasedEvent",50,$t->gettext('Nome do evento'),null,"_term",$_GET["search"]);
                                    $facets->facet("country",200,$t->gettext('País de publicação'),null,"_term",$_GET["search"]);
                                    $facets->facet("grupopesquisa",100,"Grupo de pesquisa",null,"_term",$_GET["search"]);
                                    $facets->facet("USP.internacionalizacao",10,"Internacionalização",null,"_term",$_GET["search"]);                                    
                                    $facets->facet("funder",50,$t->gettext('Agência de fomento'),null,"_term",$_GET["search"]);
                                    $facets->facet("USP.indexacao",50,$t->gettext('Indexado em'),null,"_term",$_GET["search"]);
                                ?>
                                <li class="uk-nav-header"><?php echo $t->gettext('Colaboração institucional'); ?></li>
                                <?php 
                                    $facets->facet("author.person.affiliation.name",50,"Afiliação dos autores externos normalizada",null,"_term",$_GET["search"]);
                                    $facets->facet("author.person.affiliation.name_not_found",50,"Afiliação dos autores externos não normalizada",null,"_term",$_GET["search"]);                                    
                                    $facets->facet("author.person.affiliation.location",50,"País dos autores externos",null,"_term",$_GET["search"]);   
                                ?>
                                <li class="uk-nav-header"><?php echo $t->gettext('Métricas'); ?></li>
                                <?php 
                                    $facets->facet("USP.serial_metrics.qualis.2012.area",50,$t->gettext('Qualis 2010/2012 - Área'),null,"_term",$_GET["search"]);
                                    $facets->facet("USP.serial_metrics.qualis.2012.nota",50,$t->gettext('Qualis 2010/2012 - Nota'),null,"_term",$_GET["search"]);                                    
                                    $facets->facet("USP.serial_metrics.qualis.2012.area_nota",50,$t->gettext('Qualis 2010/2012 - Área / Nota'),null,"_term",$_GET["search"]);
                                ?>                                
                                <?php 
                                    $facets->facet("USP.serial_metrics.qualis.2016.area",50,$t->gettext('Qualis 2013/2016 - Área'),null,"_term",$_GET["search"]);
                                    $facets->facet("USP.serial_metrics.qualis.2016.nota",50,$t->gettext('Qualis 2013/2016 - Nota'),null,"_term",$_GET["search"]);                                    
                                    $facets->facet("USP.serial_metrics.qualis.2016.area_nota",50,$t->gettext('Qualis 2013/2016 - Área / Nota'),null,"_term",$_GET["search"]);
                                ?>
                                <?php
                                    $facets->facet("USP.WOS.coverage",50,$t->gettext('Cobertura na Web of Science'),null,"_term",$_GET["search"]);
                                    $facets->facet_range("USP.JCR.JCR.2016.Journal_Impact_Factor",100,"JCR - Journal Impact Factor - 2016",$_GET["search"]);
                                    $facets->facet_range("USP.JCR.JCR.2016.IF_without_Journal_Self_Cites",100,"JCR - Journal Impact Factor without Journal Self Cites - 2016",$_GET["search"]);
                                    $facets->facet_range("USP.JCR.JCR.2016.Eigenfactor_Score",100,"JCR - Eigenfactor Score - 2016",$_GET["search"]);
                                    
                                ?>                                    
                                <li class="uk-nav-header"><?php echo $t->gettext('Teses e Dissertações'); ?></li>    
                                <?php
                                    $facets->facet("inSupportOf",30,"Tipo de tese",null,"_term",$_GET["search"]);
                                    $facets->facet("USP.areaconcentracao",100,"Área de concentração",null,"_term",$_GET["search"]);
                                    $facets->facet("USP.programa_pos_sigla",100,"Sigla do Departamento/Programa de Pós Graduação",null,"_term",$_GET["search"]);
                                    $facets->facet("USP.programa_pos_nome",100,"Departamento/Programa de Pós Graduação",null,"_term",$_GET["search"]);
                                    $facets->facet("USP.about_BDTD",50,$t->gettext('Assuntos provenientes das teses'),null,"_term",$_GET["search"]);
                                ?>
                            </ul>
                            <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                                <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
                                <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                                <hr>
                                <?php
                                    $facets->facet("authorUSP.regime_de_trabalho",50,$t->gettext('Regime de trabalho'),null,"_term",$_GET["search"]);
                                    $facets->facet("authorUSP.funcao",50,$t->gettext('Função'),null,"_term",$_GET["search"]);
                                    $facets->facet("USP.CAT.date",100,"Data de registro e alterações","desc","_term",$_GET["search"]);
                                    $facets->facet("USP.CAT.cataloger",100,"Catalogador","desc","_count",$_GET["search"]);                                
                                    $facets->facet("authorUSP.codpes",100,"Número USP",null,"_term",$_GET["search"]);
                                ?>
                                </ul>
                            <?php endif; ?>
                            <!-- Facetas - Fim -->

                            <hr>

                            <!-- Limitar por data - Início -->
                            <form class="uk-form uk-text-small">
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
                                    <input type="text" class="uk-form-width-medium" id="date" readonly style="border:0; color:#f6931f; font-size:bold;" name="search[]">
                                    </p>        
                                    <div id="limitar-data" class="uk-margin-bottom"></div>        
                                    <?php if(!empty($_GET["search"])): ?>
                                        <?php foreach($_GET["search"] as $search_expression): ?>
                                            <input type="hidden" name="search[]" value="<?php echo str_replace('"','&quot;',$search_expression); ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <div class="uk-form-row"><button class="uk-button-primary"><?php echo $t->gettext('Limitar datas'); ?></button></div>
                                </fieldset>        
                            </form>
                            <!-- Limitar por data - Fim -->

                            <hr>

                            <!-- Gerar relatório - Início -->
                            <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                                    <fieldset>
                                        <legend>Gerar relatório</legend>                  
                                        <div class="uk-form-row"><a href="<?php echo 'report.php?'.$_SERVER["QUERY_STRING"].''; ?>" class="uk-button-primary">Gerar relatório</a>
                                        </div>
                                    </fieldset>        
                            <?php endif; ?>
                            <!-- Gerar relatório - Fim -->                
                    </div>
                </div>
                
                <div class="uk-width-3-4@s uk-width-4-6@m">
                
                <!-- Gráfico do ano - Início -->
                <?php if ($year_result_graph == true) : ?>
                    <div class="uk-alert-primary" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <?php $ano_bar = processaResultados::generateDataGraphBar($query, 'datePublished', "_term", 'desc', 'Ano', 10); ?>
                        <div id="ano_chart" class="uk-visible@l"></div>
                        <script type="application/javascript">
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
                        <?php if (preg_match("/\babout.keyword\b/i",$expressao_busca,$matches)) : ?>
                            <div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <?php $assunto = str_replace("about.keyword:","",$expressao_busca); USP::consultar_vcusp(str_replace("\"","",$assunto)); ?>
                            </div>   
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>    
                <!-- Vocabulário controlado - Fim -->
                    
                    <!-- Navegador de resultados - Início -->
                    <div class="uk-child-width-expand@s uk-grid-divider" uk-grid>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($page == 1) :?>
                                    <li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> <?php echo $t->gettext('Anterior'); ?></a></li>
                                <?php else :?>
                                    <?php $get_data["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($get_data); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> <?php echo $t->gettext('Anterior'); ?></a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> <?php echo $t->gettext('registros'); ?></p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $get_data["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($get_data); ?>"><?php echo $t->gettext('Próxima'); ?> <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#"><?php echo $t->gettext('Próxima'); ?> <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php endif; ?>
                            </ul>                            
                        </div>
                    </div>
                    <!-- Navegador de resultados - Fim -->                    
                    
                    <hr class="uk-grid-divider">

                    <!-- Resultados -->
                    <div class="uk-width-1-1 uk-margin-top uk-description-list-line">                        
                        <ul class="uk-list uk-list-divider">   
                            <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                                <li>                        
                                    <div class="uk-grid-divider uk-padding-small" uk-grid>
                                        <div class="uk-width-1-5@m">
                                            <p><a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($t->gettext($r["_source"]['type'])));?></a></p>
                                            <p><?php echo $t->gettext('Unidades USP'); ?>:
                                                <?php if (!empty($r["_source"]['unidadeUSP'])) : ?>
                                                <?php $unique =  array_unique($r["_source"]['unidadeUSP']); ?>
                                                <?php foreach ($unique as $unidadeUSP) : ?>
                                                    <a href="result.php?search[]=unidadeUSP.keyword:&quot;<?php echo $unidadeUSP;?>&quot;"><?php echo $unidadeUSP;?></a>
                                                <?php endforeach;?>
                                                <?php endif; ?>                                   
                                            </p>
                                            
                                        </div>
                                        <div class="uk-width-4-5@m">    
                                            <article class="uk-article">
                                                <p class="uk-text-lead uk-margin-remove" style="font-size:115%"><a class="uk-link-reset" href="single.php?_id=<?php echo  $r['_id'];?>"><?php echo $r["_source"]['name'];?><?php if (!empty($r["_source"]['datePublished'])) { echo ' ('.$r["_source"]['datePublished'].')'; } ?></a></p>
                                                <?php if (!empty($r["_source"]['author'])) : ?>
                                                    <p class="uk-article-meta uk-margin-remove"><?php echo $t->gettext('Autores'); ?>: 
                                                    <?php foreach ($r["_source"]['author'] as $authors) {
                                                        if (!empty($authors["person"]["potentialAction"])) {
                                                            $authors_array[]='<a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' ('.$authors["person"]["potentialAction"].')</a>';
                                                        } else {
                                                            $authors_array[]='<a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a>';
                                                        }
                                                    } 
                                                    $array_aut = implode("; ",$authors_array);
                                                    unset($authors_array);
                                                    print_r($array_aut);
                                                    ?>
                                                    </p>        
                                                <?php endif; ?>                                                       
                                                <?php if (!empty($r["_source"]['isPartOf'])) : ?>
                                                    <p class="uk-text-small uk-margin-remove">In: <a href="result.php?search[]=isPartOf.name.keyword:&quot;<?php echo $r["_source"]['isPartOf']["name"];?>&quot;"><?php echo $r["_source"]['isPartOf']["name"];?></a>
                                                    </p>
                                                <?php endif; ?> 
                                                <p class="uk-text-small uk-margin-remove">
                                                    <?php echo $t->gettext('Assuntos'); ?>:
                                                    <?php if (!empty($r["_source"]['about'])) : ?>
                                                    <?php foreach ($r["_source"]['about'] as $subject) : ?>
                                                        <a href="result.php?search[]=about.keyword:&quot;<?php echo $subject;?>&quot;"><?php echo $subject;?></a>
                                                    <?php endforeach;?>
                                                    <?php endif; ?>
                                                </p>
                                                <?php if (!empty($r["_source"]["USP"]["about_BDTD"])) : ?>
                                                <p class="uk-text-small uk-margin-remove">
                                                    <?php echo $t->gettext('Assuntos provenientes das teses'); ?>:
                                                    <?php foreach ($r["_source"]["USP"]["about_BDTD"] as $subject_BDTD) : ?>
                                                        <a href="result.php?search[]=USP.about_BDTD.keyword:&quot;<?php echo $subject_BDTD; ?>&quot;"><?php echo $subject_BDTD;?></a>
                                                    <?php endforeach;?>
                                                </p>
                                                <?php endif; ?>                                                
                                                <?php if (!empty($r["_source"]['fatorimpacto'])) : ?>
                                                <p class="uk-text-small uk-margin-remove">Fator de impacto da publicação: <?php echo $r["_source"]['fatorimpacto'][0]; ?></p>
                                                <?php endif; ?>
                                                <!-- Acesso ao texto completo - Começo -->
                                                        <?php if (!empty($r["_source"]['url'])||!empty($r["_source"]['doi'])) : ?>
                                                <div class="uk-alert-primary" uk-alert>
                                                    <p class="uk-text-small"><?php echo $t->gettext('Acesso ao documento'); ?>:</p>                                                        
                                                        <p>     
                                                            <?php if (!empty($r["_source"]['url'])) : ?>
                                                            <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                            <?php if ($url != '') : ?>
                                                            <a class="uk-button uk-button-primary uk-button-small" href="<?php echo $url;?>" target="_blank"><?php echo $t->gettext('Acesso online à fonte'); ?></a>
                                                            <?php endif; ?>
                                                            <?php endforeach;?>
                                                            <?php endif; ?>
                                                            <?php if (!empty($r["_source"]['doi'])) : ?>
                                                            <a class="uk-button uk-button-primary uk-button-small" href="http://dx.doi.org/<?php echo $r["_source"]['doi'];?>" target="_blank">DOI</a>
                                                            <?php endif; ?>

                                                            <?php
                                                                $sfx_array[] = 'rft.atitle='.$r["_source"]['name'].'';
                                                                $sfx_array[] = 'rft.year='.$r["_source"]['datePublished'].'';
                                                                if (!empty($r["_source"]['isPartOf'])) {
                                                                    $sfx_array[] = 'rft.jtitle='.$r["_source"]['isPartOf']["name"].'';
                                                                }
                                                                if (!empty($r["_source"]['doi'])) {
                                                                    $sfx_array[] = 'rft_id=info:doi/'.$r["_source"]['doi'].'';
                                                                }
                                                                if (!empty($r["_source"]['issn'][0])) {
                                                                    $sfx_array[] = 'rft.issn='.$r["_source"]['issn'][0].'';
                                                                }
                                                                if (!empty($r["_source"]['ispartof_data'][0])) {
                                                                    $sfx_array[] = 'rft.volume='.trim(str_replace("v.","",$r["_source"]['ispartof_data'][0])).'';
                                                                }                                             
                                                            ?>
                                                            <a class="uk-text-small" href="http://143.107.154.66:3410/sfxlcl41?<?php echo implode("&",$sfx_array); unset($sfx_array); ?>" target="_blank"> <?php echo $t->gettext('ou pesquise este registro no'); ?> <img src="http://143.107.154.66:3410/sfxlcl41/sfx.gif"></a>
                                                        </p>
                                                </div>
                                                        <?php endif; ?>
                                                        <?php
                                                            if ($dedalus == true) {
                                                                processaResultados::load_itens_aleph($r['_id']);
                                                            } 
                                                        ?>
                                                    
                                                        <?php 
                                                            if(empty($_SESSION['oauthuserdata'])){
                                                                $_SESSION['oauthuserdata']="";
                                                            } 
                                                            $full_links = processaResultados::get_fulltext_file($r['_id'],$_SESSION['oauthuserdata']);
                                                            if (!empty($full_links)){
                                                                echo '<p class="uk-text-small">Download do texto completo</p><div class="uk-grid">';
                                                                        foreach ($full_links as $links) {
                                                                            print_r($links);
                                                                        }                                  
                                                                echo '</div><br/>';
                                                            }

                                                        ?> 
                                                    

                                                <!-- Acesso ao texto completo - Fim -->
                                                
                                                <!-- Métricas - Início -->
                                                <?php if ($show_metrics == true) : ?>
                                                    <?php if (!empty($r["_source"]['doi'])) : ?>
                                                    <div class="uk-alert-warning" uk-alert>
                                                        <p><?php echo $t->gettext('Métricas'); ?>:</p>
                                                        <div uk-grid>
                                                            <div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $r["_source"]['doi'];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                                            <div><a href="https://plu.mx/plum/a/?doi=<?php echo $r["_source"]['doi'];?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true"></a></div>
                                                            <div><object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r["_source"]['doi'];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object></div>
                                                            <div>
                                                                <!--
                                                                < ?php 
                                                                    $citations_scopus = get_citations_elsevier($r["_source"]['doi'][0],$api_elsevier);
                                                                    if (!empty($citations_scopus['abstract-citations-response'])) {
                                                                        echo '<a href="https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp='.$citations_scopus['abstract-citations-response']['identifier-legend']['identifier'][0]['scopus_id'].'&origin=inward">Citações na SCOPUS: '.$citations_scopus['abstract-citations-response']['citeInfoMatrix']['citeInfoMatrixXML']['citationMatrix']['citeInfo'][0]['rowTotal'].'</a>';
                                                                        echo '<br/><br/>';
                                                                    } 
                                                                ? >
                                                                -->                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <!-- Métricas - Fim -->                                         

                                                <div class="uk-grid-small uk-child-width-auto" uk-grid>
                                                    <div>
                                                        <a class="uk-button uk-button-text" href="single.php?_id=<?php echo  $r['_id'];?>"><?php echo $t->gettext('Ver registro completo'); ?></a>
                                                    </div>
                                                    <div>
                                                        <a class="uk-button uk-button-text" href="#" uk-toggle="target: #citacao<?php echo  $r['_id'];?>; animation: uk-animation-fade"><?php echo $t->gettext('Como citar'); ?></a>
                                                    </div>
                                                </div>
                                                
                                                <div id="citacao<?php echo  $r['_id'];?>" hidden="hidden">
                                                    <li class="uk-h6 uk-margin-top">
                                                        <div class="uk-alert-danger" uk-alert>A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
                                                        <ul>
                                                            <li class="uk-margin-top">
                                                                <p><strong>ABNT</strong></p>
                                                                <?php
                                                                    $data = citation::citation_query($r["_source"]);
                                                                    print_r($citeproc_abnt->render($data, $mode));
                                                                ?>
                                                            </li>
                                                            <li class="uk-margin-top">
                                                                <p><strong>APA</strong></p>
                                                                <?php
                                                                    $data = citation::citation_query($r["_source"]);
                                                                    print_r($citeproc_apa->render($data, $mode));
                                                                ?>
                                                            </li>
                                                            <li class="uk-margin-top">
                                                                <p><strong>NLM</strong></p>
                                                                <?php
                                                                    $data = citation::citation_query($r["_source"]);
                                                                    print_r($citeproc_nlm->render($data, $mode));
                                                                ?>
                                                            </li>
                                                            <li class="uk-margin-top">
                                                                <p><strong>Vancouver</strong></p>
                                                                <?php
                                                                    $data = citation::citation_query($r["_source"]);
                                                                    print_r($citeproc_vancouver->render($data, $mode));
                                                                ?>
                                                            </li>                                                 
                                                        </ul>                                              
                                                    </li>
                                                </div>                                        
                                            </article>
                                        </div>
                                    </div>    
                                </li>
                                        

                            <?php endforeach;?>
                        </ul> 
                        
                    <hr class="uk-grid-divider">

                    <!-- Navegador de resultados - Início -->
                    <div class="uk-child-width-expand@s uk-grid-divider" uk-grid>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($page == 1) :?>
                                    <li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> <?php echo $t->gettext('Anterior'); ?></a></li>
                                <?php else :?>
                                    <?php $get_data["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($get_data); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> <?php echo $t->gettext('Anterior'); ?></a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> <?php echo $t->gettext('registros'); ?></p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $get_data["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($get_data); ?>"><?php echo $t->gettext('Próxima'); ?> <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#"><?php echo $t->gettext('Próxima'); ?> <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php endif; ?>
                            </ul>                            
                        </div>
                    </div>
                    <!-- Navegador de resultados - Fim -->    
                   
                </div>
            </div>
            <hr class="uk-grid-divider">
            </div>
            <?php include('inc/footer.php'); ?>          
        </div>
                


        <script>
        $('[data-uk-pagination]').on('select.uk.pagination', function(e, pageIndex){
            var url = window.location.href.split('&page')[0];
            window.location=url +'&page='+ (pageIndex+1);
        });
        </script>    

<?php include('inc/offcanvas.php'); ?>         
        
    </body>
</html>