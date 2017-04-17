<!DOCTYPE html>
<?php
    include('inc/config.php'); 
    include('inc/functions.php');
    include('inc/functions_result.php');

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
                <div class="uk-width-1-4@s uk-width-2-6@m">
                    <div class="uk-panel uk-panel-box">
                        <!--
                        <form method="get" action="result.php">
                        <fieldset>

                            <?php if (!empty($_GET["search"])) : ?>
                            <legend uk-form>Filtros ativos</legend>
                                <div class="uk-form-row">
                                    <?php foreach($_GET["search"] as $filters): ?>
                                        <input type="checkbox" name="search[]" value="<?php print_r(str_replace('"','&quot;',$filters)); ?>" checked><?php print_r($filters); ?><br/>
                                    <?php endforeach; ?>
                                </div>
                            <div class="uk-form-row"><button type="submit" class="uk-button-primary">Retirar filtros</button></div>
                            <?php endif;?> 
                        </fieldset>        
                        </form>
                        -->

                        <h3 class="uk-panel-title">Refinar meus resultados</h3>
                        <hr>
                        <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                            <?php
                                $facets = new facets();
                                $facets->query = $query;

                                $facets->facet("type",10,"Tipo de material",null);
                                $facets->facet("authors",120,"Autores",null);
                                $facets->facet("authors_function",120,"Função dos autores",null);
                                $facets->facet("meio_de_expressao",200,"Meio de expressão",null);                            
                                $facets->facet("year",120,"Ano de publicação","desc");
                                $facets->facet("genero_e_forma",100,"Gênero e forma",null);
                                $facets->facet("subject",100,"Assuntos",null);
                                $facets->facet("language",40,"Idioma",null);
                                $facets->facet("ispartof",100,"É parte de ...",null);
                                $facets->facet("publisher",100,"Editora",null);
                                $facets->facet("country",200,"País de publicação",null);
                            ?>
                        </ul>
                        <hr>
                        <form class="uk-form">
                            <fieldset>
                                <legend>Limitar por intervalo de datas</legend>
                                <script>
                                    $( function() {
                                    $( "#limitar-data" ).slider({
                                      range: true,
                                      min: 1700,
                                      max: 2030,
                                      values: [ 1700, 2030 ],
                                      slide: function( event, ui ) {
                                        $( "#date" ).val( "year:[" + ui.values[ 0 ] + " TO " + ui.values[ 1 ] + "]" );
                                      }
                                    });
                                    $( "#date" ).val( "year:[" + $( "#limitar-data" ).slider( "values", 0 ) +
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
                            <div class="uk-grid-divider" uk-grid>
                                <div class="uk-width-1-5@m">
                                    <p>
                                        <a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a>
                                    </p>                                    
                                </div>
                                <div class="uk-width-4-5@m">    
                                    <article class="uk-article">
                                        <p class="uk-text-lead uk-margin-remove"><a class="uk-link-reset" href="single.php?_id=<?php echo  $r['_id'];?>"><?php echo $r["_source"]['title'];?><?php if (!empty($r["_source"]['year'])) { echo ' ('.$r["_source"]['year'].')'; } ?></a></p>
                                        <?php if (!empty($r["_source"]['authors'])) : ?>
                                            <p class="uk-article-meta">
                                            <?php foreach ($r["_source"]['authors'] as $autores) {
                                                $authors_array[]='<a href="result.php?search[]=authors.keyword:&quot;'.$autores.'&quot;">'.$autores.'</a>';
                                            } 
                                            $array_aut = implode("; ",$authors_array);
                                            unset($authors_array);
                                            print_r($array_aut);
                                            ?>
                                            </p>        
                                        <?php endif; ?>                                                       
                                        <?php if (!empty($r["_source"]['ispartof'])) : ?>
                                            <p class="uk-text-small uk-margin-remove">In: <a href="result.php?search[]=ispartof.keyword:&quot;<?php echo $r["_source"]['ispartof'];?>&quot;"><?php echo $r["_source"]['ispartof'];?></a>
                                            </p>
                                        <?php endif; ?> 
                                        <p class="uk-text-small uk-margin-remove">
                                            <?php if (!empty($r["_source"]['subject'])) : ?>
                                                Assuntos:
                                                <?php foreach ($r["_source"]['subject'] as $assunto) : ?>
                                                    <a href="result.php?search[]=subject.keyword:&quot;<?php echo $assunto;?>&quot;"><?php echo $assunto;?></a>
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
                                            <?php if (!empty($r["_source"]['meio_de_expressao'])) : ?>
                                                Meio de expressão:
                                                <?php foreach ($r["_source"]['meio_de_expressao'] as $meio_de_expressao) : ?>
                                                    <a href="result.php?search[]=meio_de_expressao.keyword:&quot;<?php echo $meio_de_expressao;?>&quot;"><?php echo $meio_de_expressao;?></a>
                                                <?php endforeach;?>
                                            <?php endif; ?>                                            
                                        </p>                                        
                                        <?php if (!empty($r["_source"]['fatorimpacto'])) : ?>
                                        <p class="uk-text-small uk-margin-remove">Fator de impacto da publicação: <?php echo $r["_source"]['fatorimpacto'][0]; ?></p>
                                        <?php endif; ?>
                                        <!-- Acesso ao texto completo - Começo -->
                                        <div class="uk-alert-primary" uk-alert>
                                            <p class="uk-text-small">Acesso ao documento:</p>
                                                <?php if (!empty($r["_source"]['url'])||!empty($r["_source"]['doi'])) : ?>
                                                <p>     
                                                    <?php if (!empty($r["_source"]['url'])) : ?>
                                                    <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                    <?php if ($url != '') : ?>
                                                    <a class="uk-button uk-button-primary uk-button-small" href="<?php echo $url;?>" target="_blank">Acesso online</a>
                                                    <?php endif; ?>
                                                    <?php endforeach;?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($r["_source"]['doi'])) : ?>
                                                    <a class="uk-button uk-button-primary uk-button-small" href="http://dx.doi.org/<?php echo $r["_source"]['doi'][0];?>" target="_blank">Resolver DOI</a>
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
                                        <!-- Acesso ao texto completo - Fim -->

                                        <div class="uk-grid-small uk-child-width-auto" uk-grid>
                                            <div>
                                                <a class="uk-button uk-button-text" href="single.php?_id=<?php echo  $r['_id'];?>">Ver registro completo</a>
                                            </div>
                                            <div>
                                                <a class="uk-button uk-button-text" href="#" uk-toggle="target: #citacao<?php echo  $r['_id'];?>; animation: uk-animation-fade">Como citar</a>
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
            <hr class="uk-grid-divider">
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