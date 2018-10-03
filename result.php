<!DOCTYPE html>
<?php
    require 'inc/config.php'; 
    require 'inc/functions.php';
    require 'inc/functions_result.php';

    $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

    $result_get = get::analisa_get($_GET);
    $query = $result_get['query'];  
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $skip = $result_get['skip'];
    

    /*pagination - start*/
    $get_data = $_GET;    
    /*pagination - end*/
    

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = $limit;
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
        
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.2.2/d3.v3.min.js"></script>

        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
        
        <!-- Altmetric Script -->
        <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
        
        <!-- PlumX Script -->
        <script type="text/javascript" src="//d39af2mgp1pqhg.cloudfront.net/widget-popup.js"></script>

        
    </head>
    <body>
        <!-- < ?php include_once("inc/analyticstracking.php") ?> -->
        <div class="uk-margin-bottom">
            <?php include('inc/navbar.php'); ?>        
        </div>
        <br/><br/><br/>
        <div class="uk-container">
            <div class="uk-grid-divider" uk-grid>
	    
	    <div class="uk-width-1-1@s uk-width-1-1@m">
	    
	    
		<nav class="uk-navbar-container uk-margin" uk-navbar>

		    <div class="nav-overlay uk-navbar-left">

			<a class="uk-navbar-item uk-logo" uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#">Clique para uma nova pesquisa</a>
 
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
				<input class="uk-search-input" type="search" name="search[]" placeholder="Nova pesquisa..." autofocus>
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
	    
                <div class="uk-width-1-4@s uk-width-2-6@m">
                    <div class="uk-panel">
                        
    
                        <h3 class="uk-panel-title">Refinar meus resultados</h3>
                        <hr>
                        <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <?php
                                $facets = new facets();
                                $facets->query = $query;
                            
                                if (!isset($_GET["search"])) {
                                    $_GET["search"] = null;                                    
                                }

                                $facets->facet("author.person.name",120,"Compositores",null,"_term",$_GET["search"]);
                                $facets->facet("author.person.USP.autor_funcao",120,"Autor/Função",null,"_term",$_GET["search"]);
                                $facets->facet("USP.meio_de_expressao",200,"Meio de expressão",null,"_term",$_GET["search"]);                            
                                $facets->facet("datePublished",120,"Ano de publicação","desc","_term",$_GET["search"]);
                                $facets->facet("USP.about.genero_e_forma",100,"Gênero e forma",null,"_term",$_GET["search"]);
                                $facets->facet("about",100,"Assuntos",null,"_term",$_GET["search"]);
                                $facets->facet("publisher.organization.name",100,"Casa publicadora",null,"_term",$_GET["search"]);
                            ?>
                        </ul>
                        <hr>
                        <form class="uk-form">
                            <fieldset>
                                <legend>Limitar por data de publicação</legend>
                                <script>
                                    $( function() {
                                    $( "#limitar-data" ).slider({
                                      range: true,
                                      min: 1700,
                                      max: 2030,
                                      values: [ 1700, 2030 ],
                                      slide: function( event, ui ) {
                                        $( "#date" ).val( "datePublished:[" + ui.values[ 0 ] + " TO " + ui.values[ 1 ] + "]" );
                                      }
                                    });
                                    $( "#date" ).val( "datePublished:[" + $( "#limitar-data" ).slider( "values", 0 ) +
                                      " TO " + $( "#limitar-data" ).slider( "values", 1 ) + "]");
                                    } );
                                </script>
                                <p>
                                  <label for="date">Selecionar período de tempo:</label>
                                  <input type="text" id="date" readonly style="border:0; color:#f6931f; font-weight:bold;" name="search[]">
                                </p>        
                                <div id="limitar-data" class="uk-margin-bottom"></div>        
                                <?php if(!empty($_GET["search"])): ?>
                                    <?php foreach($_GET["search"] as $search_expression): ?>
                                        <input type="hidden" name="search[]" value="<?php echo str_replace('"','&quot;',$search_expression); ?>">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="uk-form-row"><button class="uk-button-primary">Limitar datas</button></div>
                            </fieldset>        
                        </form>
                        <hr>
                        <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                                <fieldset>
                                    <legend>Gerar relatório</legend>                  
                                    <div class="uk-form-row"><a href="<?php echo 'http://'.$_SERVER["SERVER_NAME"].'/~bdpi/report.php?'.$_SERVER["QUERY_STRING"].''; ?>" class="uk-button-primary">Gerar relatório</a>
                                    </div>
                                </fieldset>        
                        <?php endif; ?>                
                    </div>
                </div>
                
                <div class="uk-width-3-4@s uk-width-4-6@m">
                
                <!-- Vocabulário controlado - Início -->
                <?php if(isset($_GET["search"])) : ?>    
                <?php foreach ($_GET["search"] as $expressao_busca) : ?>    
                <?php if (preg_match("/\bsubject.keyword\b/i",$expressao_busca,$matches)) : ?>
                    <div class="uk-alert-primary" uk-alert>
                       <a class="uk-alert-close" uk-close></a>
                       <?php $assunto = str_replace("subject.keyword:","",$expressao_busca); USP::consultar_vcusp(str_replace("\"","",$assunto)); ?>
                    </div>   
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>    
                <!-- Vocabulário controlado - Fim -->
                
                <!-- Resultados -->
                    <div class="uk-child-width-expand@s uk-grid-divider" uk-grid>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($page == 1) :?>
                                    <li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php else :?>
                                    <?php $get_data["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($get_data); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $get_data["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($get_data); ?>">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php endif; ?>
                            </ul>                            
                        </div>
                    </div>
                    
                    <hr class="uk-grid-divider">
                    <div class="uk-width-1-1 uk-margin-top uk-description-list-line">
                        
                    <ul class="uk-list uk-list-divider">   
                    <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                        <li>                        
                            <div class="uk-grid-divider uk-padding-small" uk-grid>
                                <div class="uk-width-1-5@m">
                                    <p>
                                        <a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a>
                                    </p>                                    
                                </div>
                                <div class="uk-width-4-5@m">    
                                    <article class="uk-article">
                                        <p class="uk-text-lead uk-margin-remove"><a class="uk-link-reset" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo  $r['_id'];?>" target="_blank"><?php echo $r["_source"]['name'];?><?php if (!empty($r["_source"]['year'])) { echo ' ('.$r["_source"]['year'].')'; } ?></a></p>
                                        <?php if (!empty($r["_source"]['alternateName'])) : ?>
                                        <p class="uk-margin-remove">Título original: <?php echo $r["_source"]['alternateName'];?></a></p>
                                        <?php endif; ?>

                                        <?php if (!empty($r["_source"]['author'])) : ?>
                                            <p class="uk-article-meta uk-margin-remove"> 
                                            <?php foreach ($r["_source"]['author'] as $authors) {
                                                if (!empty($authors["person"]["date"])) {
                                                    $author_date = ' - ' . $authors["person"]["date"];
                                                } else {
                                                    $author_date = "";
                                                }

                                                if (!empty($authors["person"]["potentialAction"])) {
                                                    $authors_array[]='<a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].$author_date.' ('.$authors["person"]["potentialAction"].')</a>';
                                                } else {
                                                    $authors_array[]='<a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].$author_date.'</a>';
                                                }
                                            } 
                                            $array_aut = implode("; ",$authors_array);
                                            unset($authors_array);
                                            print_r($array_aut);
                                            ?>
                                            </p>        
                                        <?php endif; ?>                                                     
                                        <?php if (!empty($r["_source"]['isPartOf'])) : ?>
                                            <p class="uk-text-small uk-margin-remove">In: <a href="result.php?search[]=isPartOf.keyword:&quot;<?php echo $r["_source"]['isPartOf'];?>&quot;"><?php echo $r["_source"]['isPartOf'];?></a>
                                            </p>
                                        <?php endif; ?> 
                                        <p class="uk-text-small uk-margin-remove">
                                            <?php if (!empty($r["_source"]['about'])) : ?>
                                                Assuntos:
                                                <?php foreach ($r["_source"]['about'] as $assunto) : ?>
                                                    <a href="result.php?search[]=about.keyword:&quot;<?php echo $assunto;?>&quot;"><?php echo $assunto;?></a>
                                                <?php endforeach;?>
                                            <?php endif; ?>
                                            <?php if (!empty($r["_source"]['genero_e_forma'])) : ?>
                                                Gênero e forma:
                                                <?php foreach ($r["_source"]['genero_e_forma'] as $genero_e_forma) : ?>
                                                    <a href="result.php?search[]=genero_e_forma.keyword:&quot;<?php echo $genero_e_forma;?>&quot;"><?php echo $genero_e_forma;?></a>
                                                <?php endforeach;?>
                                            <?php endif; ?>
                                        </p>
                                         <p class="uk-text-small uk-margin-remove">   
                                            <?php if (!empty($r["_source"]["USP"]['notes'])) : ?>
                                                Notas:
                                                <?php foreach ($r["_source"]["USP"]['notes'] as $notas) : ?>
                                                    <a href="result.php?search[]=USP.notes.keyword:&quot;<?php echo $notas;?>&quot;"><?php echo $notas;?></a>
                                                <?php endforeach;?>
                                            <?php endif; ?>                                                                                         
                                        </p>
                                        <p class="uk-text-small uk-margin-remove">
                                            <?php if (!empty($r["_source"]["USP"]['meio_de_expressao'])) : ?>
                                                Meio de expressão:
                                                <?php foreach ($r["_source"]["USP"]['meio_de_expressao'] as $meio_de_expressao) : ?>
                                                    <a href="result.php?search[]=USP.meio_de_expressao.keyword:&quot;<?php echo $meio_de_expressao;?>&quot;"><?php echo $meio_de_expressao;?></a>
                                                <?php endforeach;?>
                                            <?php endif; ?>                                            
                                        </p>                                        
                                        <?php if (!empty($r["_source"]['fatorimpacto'])) : ?>
                                        <p class="uk-text-small uk-margin-remove">Fator de impacto da publicação: <?php echo $r["_source"]['fatorimpacto'][0]; ?></p>
                                        <?php endif; ?>
                                        <!-- Acesso ao texto completo - Começo -->
                                        <div class="uk-alert-primary uk-margin-remove" uk-alert>
                                            <p class="uk-text-small uk-margin-remove">Acesso ao documento:</p>
                                                <?php if (!empty($r["_source"]['url'])||!empty($r["_source"]['doi'])) : ?>
                                                <p>     
                                                    <?php if (!empty($r["_source"]['url'])) : ?>
                                                    <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                    <?php if ($url != '') : ?>
                                                    <a class="uk-button uk-button-primary uk-button-small uk-margin-remove" href="<?php echo $url;?>" target="_blank">Visualize a primeira página</a>
                                                    <?php endif; ?>
                                                    <?php endforeach;?>
                                                    <?php endif; ?>

                                                </p>
                                                <?php endif; ?>
                                                <?php processaResultados::load_itens_new($r['_id']); ?>
                                            
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
                                            
                                        </div>                                       
                                      
                                    </article>
                                </div>
                            </div>    
                        </li>
                                   

                    <?php endforeach;?>
                        </ul> 
                        
                    <hr class="uk-grid-divider">
                    <div class="uk-child-width-expand@s uk-grid-divider" uk-grid>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($page == 1) :?>
                                    <li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php else :?>
                                    <?php $get_data["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($get_data); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $get_data["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($get_data); ?>">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php endif; ?>
                            </ul>                            
                        </div>
                    </div>                      
                    
                </div>
            </div>
            <hr class="uk-grid-divider"></div>
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