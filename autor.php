<!DOCTYPE html>
<?php
    include('inc/config.php'); 
    include('inc/functions.php');

    if (!empty($_GET)) {
        $result_get = get::analisa_get($_GET);
        $query = $result_get['query'];  
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }
    
        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 10000;
        $params["from"] = $skip;
        $params["body"] = $query; 
    
        $cursor = $client->search($params);
        $total = $cursor["hits"]["total"];   
                

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


} else {
    echo '<div class="uk-alert-danger" uk-alert>
    <a class="uk-alert-close" uk-close></a>
    <p>Não foi informado nenhum parametro.</p>
</div>';
}    

?>
<html>
    <head>
        <?php
            include('inc/meta-header.php'); 
        ?>        
        <title><?php echo $branch_abrev; ?> - Resultado da busca</title>

        <script src="http://cdn.jsdelivr.net/g/filesaver.js"></script>
        <script>
              function SaveAsFile(t,f,m) {
                    try {
                        var b = new Blob([t],{type:m});
                        saveAs(b, f);
                    } catch (e) {
                        window.open("data:"+m+"," + encodeURIComponent(t), '_blank','');
                    }
                }

        </script>               
        

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
                                    $facets->facet("isPartOf.name",50,$t->gettext('Título da fonte'),null,"_term",$_GET["search"]);
                                    $facets->facet("publisher.organization.name",50,$t->gettext('Editora'),null,"_term",$_GET["search"]);
                                    $facets->facet("releasedEvent",50,$t->gettext('Nome do evento'),null,"_term",$_GET["search"]);
                                    $facets->facet("country",200,$t->gettext('País de publicação'),null,"_term",$_GET["search"]);
                                    $facets->facet("USP.grupopesquisa",100,"Grupo de pesquisa",null,"_term",$_GET["search"]);
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
                                    $facets->facet_range("USP.citescore.citescore.2016.citescore",100,"Citescore - 2016",$_GET["search"]);
                                    $facets->facet_range("USP.citescore.citescore.2016.SJR",100,"SJR - 2016",$_GET["search"]);
                                    $facets->facet_range("USP.citescore.citescore.2016.SNIP",100,"SNIP - 2016",$_GET["search"]);
                                    $facets->facet("USP.citescore.citescore.2016.open_access",50,$t->gettext('Acesso aberto'),null,"_term",$_GET["search"]);
                                    
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
                                    $facets->facet("isPartOf.issn",100,"ISSN",null,"_term",$_GET["search"]);
                                    $facets->facet("doi",100,"DOI",null,"_term",$_GET["search"]);
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

           

<!-- Resultados por formato -->

<hr class="uk-grid-divider">
<div class="uk-width-1-1 uk-margin-top uk-description-list-line">       

<?php if(isset($_GET["format"])) : ?>

    <?php if($_GET["format"] == "table") : ?>

    <div class="uk-overflow-auto">
        <table class="uk-table">
            <caption>Trabalhos</caption>
            <thead>
                <tr>
                    <th>Sysno</th>
                    <th>DOI</th>
                    <th>Título</th>
                    <th>Autores</th>
                    <th>Fonte da publicação</th>                    
                    <th>Paginação</th>
                    <th>Ano de publicação</th>
                    <th>ISSN</th>  
                    <th>Local de publicação</th>
                    <th>Editora</th>
                    <th>Nome do evento</th>
                    <th>Tipo de Material</th>
                    <th>Autores USP</th>
                    <th>Número USP</th>
                    <th>Unidades USP</th>
                    <th>Departamentos</th>
                    <th>Qualis 2013/2016</th>
                    <th>JCR - Journal Impact Factor - 2016</th>
                    <th>Citescore - 2016</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                <tr>
                    <td><a href="single.php?_id=<?php echo  $r['_id'];?>"><?php echo  $r['_id'];?></a></td>
                    <td>
                        <?php if (!empty($r["_source"]['doi'])) : ?>
                            <a href="http://dx.doi.org/<?php echo $r["_source"]['doi'];?>" target="_blank"><?php echo $r["_source"]['doi'];?></a>
                        <?php endif; ?>    
                    </td>
                    <td><?php echo $r["_source"]['name'];?></td>
                    <td>
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
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['isPartOf'])) : ?>
                            <a href="result.php?search[]=isPartOf.name.keyword:&quot;<?php if (!empty($r["_source"]['isPartOf']["name"])) { echo $r["_source"]['isPartOf']["name"]; } ?>&quot;"><?php if (!empty($r["_source"]['isPartOf']["name"])) { echo $r["_source"]['isPartOf']["name"];} ?></a>
                        <?php endif; ?>     
                    </td>    
                    <td>
                        <?php if (!empty($r["_source"]['isPartOf']['USP']['dados_do_periodico'])) {
                                 echo $r["_source"]['isPartOf']['USP']['dados_do_periodico']; 
                        } 
                        ?>
                    </td>
                    <td><?php if (!empty($r["_source"]['datePublished'])) { echo $r["_source"]['datePublished']; } ?></td>
                    <td>
                        <?php if (!empty($r["_source"]['isPartOf']['issn'])) : ?>
                        <?php foreach ($r["_source"]['isPartOf']['issn'] as $issn) {
                            $issn_array[]='<a href="result.php?search[]=isPartOf.issn.keyword:&quot;'.$issn.'&quot;">'.$issn.'</a>';
                        } 
                        $array_issn = implode("; ",$issn_array);
                        unset($issn_array);
                        print_r($array_issn);
                        ?>
                        <?php endif; ?>
                    </td> 
                    <td>
                        <?php if (!empty($r["_source"]['publisher']['organization']['location'])) {
                                 echo $r["_source"]['publisher']['organization']['location']; 
                        } 
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['publisher']['organization']['name'])) {
                                 echo $r["_source"]['publisher']['organization']['name']; 
                        } 
                        ?>
                    </td>                                        
                    <td>
                        <?php if (!empty($r["_source"]['releasedEvent'])) {
                                 echo $r["_source"]['releasedEvent']; 
                              } 
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['type'])) {
                                 echo $r["_source"]['type']; 
                              } 
                        ?>
                    </td>                      
                    <td>
                        <?php foreach ($r["_source"]['authorUSP'] as $authorsUSP) {
                            $authorsUSP_array[]='<a href="result.php?search[]=authorUSP.name.keyword:&quot;'.$authorsUSP["name"].'&quot;">'.$authorsUSP["name"].'</a>';
                        } 
                        $array_autUSP = implode("; ",$authorsUSP_array);
                        unset($authorsUSP_array);
                        print_r($array_autUSP);
                        ?>
                    </td>
                    <td>
                        <?php foreach ($r["_source"]['authorUSP'] as $numUSP) {
                            $numUSP_array[]='<a href="result.php?search[]=authorUSP.codpes.keyword:&quot;'.$numUSP["codpes"].'&quot;">'.$numUSP["codpes"].'</a>';
                        } 
                        $array_numUSP = implode("; ",$numUSP_array);
                        unset($numUSP_array);
                        print_r($array_numUSP);
                        ?>
                    </td>
                    <td>
                        <?php foreach ($r["_source"]['authorUSP'] as $unidadesUSP_aut) {
                            $unidadesUSP_array[]='<a href="result.php?search[]=authorUSP.unidadeUSP.keyword:&quot;'.$unidadesUSP_aut["unidadeUSP"].'&quot;">'.$unidadesUSP_aut["unidadeUSP"].'</a>';
                        } 
                        $array_unidadesUSP = implode("; ",$unidadesUSP_array);
                        unset($unidadesUSP_array);
                        print_r($array_unidadesUSP);
                        ?>
                    </td>
                    <td>
                        <?php foreach ($r["_source"]['authorUSP'] as $departament_aut) {
                            if (!empty($departament_aut["departament"])) {
                                $departament_array[]='<a href="result.php?search[]=authorUSP.departament.keyword:&quot;'.$departament_aut["departament"].'&quot;">'.$departament_aut["departament"].'</a>';
                                $array_departament = implode("; ",$departament_array);
                                unset($departament_array);
                                print_r($array_departament);                                
                            }
                        } 
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['USP']['serial_metrics']['qualis']['2016'])) : ?>
                        <?php foreach ($r["_source"]['USP']['serial_metrics']['qualis']['2016'] as $qualis) {
                            $qualis_array[]='<a href="result.php?search[]=USP.serial_metrics.qualis.2016.area_nota.keyword:&quot;'.$qualis["area_nota"].'&quot;">'.$qualis["area_nota"].'</a>';
                        } 
                        $array_qualis = implode("; ",$qualis_array);
                        unset($qualis_array);
                        print_r($array_qualis);
                        ?>
                        <?php endif; ?>   
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['USP']['JCR']['JCR']['2016'][0]['Journal_Impact_Factor'])) {
                                 echo $r["_source"]['USP']['JCR']['JCR']['2016'][0]['Journal_Impact_Factor']; 
                              } 
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($r["_source"]['USP']['citescore']['citescore']['2016'][0]['citescore'])) {
                                 echo $r["_source"]['USP']['citescore']['citescore']['2016'][0]['citescore']; 
                              } 
                        ?>
                    </td>                                                                                                                                                                               
                                  
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>

    </div>   

    <?php elseif($_GET["format"] == "abnt") : ?>
  
        <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
            <li class="uk-h6 uk-margin-top">
                <ul>
                    <li class="uk-margin-top">
                        <?php
                            $data = citation::citation_query($r["_source"]);
                            print_r($citeproc_abnt->render($data, $mode));
                        ?>
                    </li>                                               
                </ul>                                              
            </li>
        <?php endforeach;?>    

    <?php elseif($_GET["format"] == "RIS") : ?>

       <?php

            foreach ($cursor["hits"]["hits"] as $r) { 
                /* Exportador RIS */
                $record_blob[] = exporters::RIS($r);
                $record_blob = str_replace("'","",$record_blob);
            }
       ?> 
    <h2>Exportar RIS</h2> 
    <p><button class="uk-button uk-button-primary" onclick="SaveAsFile('<?php echo implode("",str_replace('"','',$record_blob)); ?>','record.ris','text/plain;charset=utf-8')">RIS (EndNote)</button></p>
           
    <?php else: ?>
    Não definido

    <?php endif; ?>

<?php else: ?>
<p>Formato não definido</p>

<?php endif; ?>                   
                 

                        
                    <hr class="uk-grid-divider">

                   
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