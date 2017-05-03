<!DOCTYPE html>
<?php
    include('inc/config.php'); 
    include('inc/functions.php');

    $result_get = get::analisa_get($_GET);
    $query = $result_get['query'];  
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $skip = $result_get['skip'];    

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
 
        <!-- < ?php include_once("inc/analyticstracking.php") ?> -->
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

                        <!-- Facetas - Início -->
                        <h3 class="uk-panel-title">Refinar meus resultados</h3>
                            <hr>
                            <ul class="uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">
                                <?php
                                    $facets = new facets();
                                    $facets->query = $query;
                                
                                    if (!isset($_GET["search"])) {
                                        $_GET["search"] = null;                                    
                                    }                            

                                    $facets->facet("base",10,"Base",null,$_GET["search"]);
                                    $facets->facet("type",10,"Tipo de material",null,$_GET["search"]);
                                    $facets->facet("unidadeUSP",100,"Unidade USP",null,$_GET["search"]);
                                    $facets->facet("departamento",100,"Departamento",null,$_GET["search"]);
                                    $facets->facet("authors",120,"Autores",null,$_GET["search"]);
                                    $facets->facet("authorUSP",100,"Autores USP",null,$_GET["search"]);
                                    $facets->facet("year",120,"Ano de publicação","desc",$_GET["search"]);
                                    $facets->facet("subject",100,"Assuntos",null,$_GET["search"]);
                                    $facets->facet("language",40,"Idioma",null,$_GET["search"]);
                                    $facets->facet("ispartof",100,"É parte de ...",null,$_GET["search"]);
                                    $facets->facet("publisher",100,"Editora",null,$_GET["search"]);
                                    $facets->facet("evento",100,"Nome do evento",null,$_GET["search"]);
                                    $facets->facet("country",200,"País de publicação",null,$_GET["search"]);
                                    $facets->facet("tipotese",30,"Tipo de tese",null,$_GET["search"]);
                                    $facets->facet("areaconcentracao",100,"Área de concentração",null,$_GET["search"]);
                                    $facets->facet("programa_pos_sigla",100,"Sigla do Departamento/Programa de Pós Graduação",null,$_GET["search"]);
                                    $facets->facet("programa_pos_nome",100,"Departamento/Programa de Pós Graduação",null,$_GET["search"]);
                                    $facets->facet("indexado",100,"Indexado em",null,$_GET["search"]);
                                    $facets->facet("fatorimpacto",1000,"Fator de impacto","desc",$_GET["search"]);         
                                    $facets->facet("grupopesquisa",100,"Grupo de pesquisa",null,$_GET["search"]);
                                    $facets->facet("internacionalizacao",30,"Internacionalização",null,$_GET["search"]);  
                                    $facets->facet("colab",120,"País dos autores externos à USP",null,$_GET["search"]);
                                    $facets->facet("colab_instituicao_corrigido",100,"Colaboração institucional",null,$_GET["search"]);
                                    $facets->facet("fomento",100,"Agência de fomento",null,$_GET["search"]);
                                    $facets->facet_range("three_years_citations_scopus",100,"Citações nos últimos 3 anos na Scopus",$_GET["search"]);
                                    $facets->facet_range("full_citations_scopus",100,"Total de citações na Scopus",$_GET["search"]);
                                ?>
                            </ul>
                            <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                                <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
                                <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
                                <hr>
                                <?php         
                                    $facets->facet("codpes",100,"Número USP",null,$_GET["search"]);
                                    $facets->facet("codpes_unidade",100,"Número USP / Unidade",null,$_GET["search"]);
                                    $facets->facet("issn",100,"ISSN",null,$_GET["search"]);
                                    $facets->facet("colab_int_trab",100,"Colaboração - Internacionalização",null,$_GET["search"]); 
                                    $facets->facet("colab_instituicao_trab",100,"Colaboração - Instituição",null,$_GET["search"]); 
                                    $facets->facet("colab_instituicao_corrigido",100,"Colaboração - Instituição - Corrigido",null,$_GET["search"]); 
                                    $facets->rebuild_facet("colab_instituicao_naocorrigido",10,"Colaboração - Instituição - Não corrigido",$_GET["search"]);
                                    $facets->facet("dataregistroinicial",100,"Data de registro","desc",$_GET["search"]);
                                    $facets->facet("dataregistro",100,"Data de registro e alterações","desc",$_GET["search"]);
                                ?>
                                </ul>
                            <?php endif; ?>
                            <!-- Facetas - Fim -->

                            <hr>

                            <!-- Limitar por data - Início -->
                            <form class="uk-form">
                                <fieldset>
                                    <legend>Limitar por data</legend>
                                    <script>
                                        $( function() {
                                        $( "#limitar-data" ).slider({
                                        range: true,
                                        min: 1900,
                                        max: 2030,
                                        values: [ 1900, 2030 ],
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
                            <!-- Limitar por data - Fim -->

                            <hr>

                            <!-- Gerar relatório - Início -->
                            <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                                    <fieldset>
                                        <legend>Gerar relatório</legend>                  
                                        <div class="uk-form-row"><a href="<?php echo 'http://'.$_SERVER["SERVER_NAME"].'/~bdpi/report.php?'.$_SERVER["QUERY_STRING"].''; ?>" class="uk-button-primary">Gerar relatório</a>
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
                        <?php $ano_bar = processaResultados::generateDataGraphBar($query, 'year', "_term", 'desc', 'Ano', 10); ?>
                        <div id="ano_chart" class="uk-visible@l"></div>
                        <script type="application/javascript">
                            var graphdef = {
                                categories : ['Ano'],
                                dataset : {
                                    'Ano' : [<?= $ano_bar; ?>]
                                }
                            }
                            var chart = uv.chart ('Bar', graphdef, {
                                meta : {
                                    position: '#ano_chart',
                                    caption : 'Ano de publicação',
                                    hlabel : 'Ano',
                                    vlabel : 'Registros'
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
                        <?php if (preg_match("/\bsubject.keyword\b/i",$expressao_busca,$matches)) : ?>
                            <div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <?php $assunto = str_replace("subject.keyword:","",$expressao_busca); USP::consultar_vcusp(str_replace("\"","",$assunto)); ?>
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
                                    <li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php else :?>
                                    <?php $result_get["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($result_get['query']); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $result_get["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($result_get['query']); ?>">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
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
                                    <div class="uk-grid-divider" uk-grid>
                                        <div class="uk-width-1-5@m">
                                            <p><a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a></p>
                                            <p>                              
                                                    Unidades USP:
                                                    <?php if (!empty($r["_source"]['unidadeUSP'])) : ?>
                                                    <?php $unique =  array_unique($r["_source"]['unidadeUSP']); ?>
                                                    <?php foreach ($unique as $unidadeUSP) : ?>
                                                        <a href="result.php?search[]=unidadeUSP.keyword:&quot;<?php echo $unidadeUSP;?>&quot;"><?php echo $unidadeUSP;?></a>
                                                    <?php endforeach;?>
                                                    <?php endif; ?>                                   
                                            </p>
                                            
                                            <!-- Se tem capa disponível na API da Elsevier  -->
                                            
                                            <?php
                                                if ($use_api_elsevier == true) {
                                                    if (isset($issn_info["serial-metadata-response"])) {
                                                    $image_url = "{$issn_info["serial-metadata-response"]["entry"][0]["link"][2]["@href"]}&apiKey={$api_elsevier}";
                                                        if (exif_imagetype($image_url) == IMAGETYPE_GIF) {
                                                            echo '<img src="'.$image_url.'">';
                                                        }
                                                    } 
                                                } 

                                            ?>
                                            
                                            <!-- Se é de acesso aberto  -->
                                            <?php
                                                if (isset($issn_info["serial-metadata-response"])) {
                                                    if ($issn_info["serial-metadata-response"]["entry"][0]["openaccess"] == '1') {                                     
                                                            echo '<img src="inc/images/openaccess-t.png">';
                                                    }
                                                }
                                            ?>                                    
                                            
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
                                                    Assuntos:
                                                    <?php if (!empty($r["_source"]['subject'])) : ?>
                                                    <?php foreach ($r["_source"]['subject'] as $assunto) : ?>
                                                        <a href="result.php?search[]=subject.keyword:&quot;<?php echo $assunto;?>&quot;"><?php echo $assunto;?></a>
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
                                                            <a class="uk-button uk-button-primary uk-button-small" href="<?php echo $url;?>" target="_blank">Acesso online à fonte</a>
                                                            <?php endif; ?>
                                                            <?php endforeach;?>
                                                            <?php endif; ?>
                                                            <?php if (!empty($r["_source"]['doi'])) : ?>
                                                            <a class="uk-button uk-button-primary uk-button-small" href="http://dx.doi.org/<?php echo $r["_source"]['doi'][0];?>" target="_blank">Resolver DOI</a>
                                                            <?php endif; ?>

                                                            <?php
                                                                $sfx_array[] = 'rft.atitle='.$r["_source"]['title'].'';
                                                                $sfx_array[] = 'rft.year='.$r["_source"]['year'].'';
                                                                if (!empty($r["_source"]['ispartof'])) {
                                                                    $sfx_array[] = 'rft.jtitle='.$r["_source"]['ispartof'].'';
                                                                }
                                                                if (!empty($r["_source"]['doi'])) {
                                                                    $sfx_array[] = 'rft_id=info:doi/'.$r["_source"]['doi'][0].'';
                                                                }
                                                                if (!empty($r["_source"]['issn'][0])) {
                                                                    $sfx_array[] = 'rft.issn='.$r["_source"]['issn'][0].'';
                                                                }
                                                                if (!empty($r["_source"]['ispartof_data'][0])) {
                                                                    $sfx_array[] = 'rft.volume='.trim(str_replace("v.","",$r["_source"]['ispartof_data'][0])).'';
                                                                }                                             
                                                            ?>
                                                            <a class="uk-text-small" href="http://143.107.154.66:3410/sfxlcl41?<?php echo implode("&",$sfx_array); unset($sfx_array); ?>" target="_blank"> Ou pesquise este registro no <img src="http://143.107.154.66:3410/sfxlcl41/sfx.gif"></a>
                                                        </p>
                                                        <?php endif; ?>
                                                        <?php
                                                            if ($dedalus == true) {
                                                                processaResultados::load_itens_new($r['_id']);
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
                                                    
                                                </div>
                                                <!-- Acesso ao texto completo - Fim -->
                                                <!-- Informações de APIs - Início -->
                                                <?php if (isset($issn_info["serial-metadata-response"])): ?>
                                                <div class="uk-alert-danger" uk-alert>
                                                    <li class="uk-h6">
                                                        Informações sobre o periódico <a href="<?php print_r($issn_info["serial-metadata-response"]["entry"][0]["link"][1]["@href"]); ?>"><?php print_r($issn_info["serial-metadata-response"]["entry"][0]["dc:title"]); ?></a> (Fonte: Scopus API):
                                                        <ul>
                                                            <li>
                                                                Editor: <?php print_r($issn_info["serial-metadata-response"]["entry"][0]["dc:publisher"]); ?>
                                                            </li>
                                                        <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["subject-area"] as $subj_area) : ?>
                                                            <li> 
                                                                Área: <?php print_r($subj_area["$"]); ?>
                                                            </li>
                                                        <?php endforeach; ?>                                                    
                                                        <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["SJRList"]["SJR"] as $sjr) : ?>
                                                            <li>                                                    
                                                                SJR <?php print_r($sjr["@year"]); ?>: <?php print_r($sjr["$"]); ?>
                                                            </li>
                                                        <?php endforeach; ?>

                                                        <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["SNIPList"]["SNIP"] as $snip) : ?>
                                                            <li>                                                    
                                                                SNIP <?php print_r($snip["@year"]); ?>: <?php print_r($snip["$"]); ?>
                                                            </li>
                                                        <?php endforeach; ?>

                                                        <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["IPPList"]["IPP"] as $ipp) : ?>
                                                            <li>                                                    
                                                                IPP <?php print_r($ipp["@year"]); ?>: <?php print_r($ipp["$"]); ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </li>
                                                </div> 
                                                <?php flush(); unset($issn_info); endif; ?>                                         
                                                <!-- Informações de APIs - Fim -->
                                                
                                                <!-- Métricas - Início -->
                                                <?php if (!empty($r["_source"]['doi'])) : ?>
                                                <div class="uk-alert-warning" uk-alert>
                                                    <p>Métricas:</p>
                                                    <div uk-grid>
                                                        <div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $r["_source"]['doi'][0];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                                        <div><a href="https://plu.mx/plum/a/?doi=<?php echo $r["_source"]['doi'][0];?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true"></a></div>
                                                        <div><object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object></div>
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
                                                <!-- Métricas - Fim -->                                         

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
                                    <?php $result_get["page"] = $page-1 ; ?>
                                    <li><a href="result.php?<?php echo http_build_query($result_get); ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> Anterior</a></li>
                                <?php endif; ?>
                            </ul>    
                        </div>
                        <div>
                            <p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p>
                        </div>
                        <div>
                            <ul class="uk-pagination">
                                <?php if ($total/$limit > $page): ?>
                                    <?php $result_get["page"] = $page+1 ; ?>
                                    <li class="uk-margin-auto-left"><a href="result.php?<?php echo http_build_query($result_get); ?>">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php else :?>
                                    <li class="uk-margin-auto-left"><a href="#">Próxima <span class="uk-margin-small-left" uk-pagination-next></span></a></li>
                                <?php endif; ?>
                            </ul>                            
                        </div>
                    </div> 
                    
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