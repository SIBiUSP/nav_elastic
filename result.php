<!DOCTYPE html>
<?php
    include('inc/config.php'); 
    include('inc/functions.php');

    $result_get = analisa_get($_GET);
    $query_complete = $result_get['query_complete'];
    $query_aggregate = $result_get['query_aggregate'];
    $escaped_url = $result_get['escaped_url'];
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $new_get = $result_get['new_get'];
    $skip = $result_get['skip'];

    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> $limit,
        'from' => $skip,   
        'body' => $query_complete
    ];  
    
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
        <title>BDPI USP - Resultado da busca</title>
        <script src="inc/uikit/js/components/accordion.min.js"></script>
        <script src="inc/uikit/js/components/pagination.min.js"></script>
        <script src="inc/uikit/js/components/datepicker.min.js"></script>
        
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
        <?php include_once("inc/analyticstracking.php") ?>  
        <?php include('inc/navbar.php'); ?>        
        
        <div class="uk-container uk-container-center">
            <div class="uk-grid" data-uk-grid>                        
                <div class="uk-width-small-1-2 uk-width-medium-2-6">                    
                    

<div class="uk-panel uk-panel-box">
    <form class="uk-form" method="get" action="result.php">
    <fieldset>
        <legend>Filtros ativos</legend>
        <?php foreach ($new_get as $key => $value) : ?>
            <div class="uk-form-row">
                <label><?php echo $key; ?>: <?php echo implode(",",$value); ?></label>
                <input type="checkbox" checked="checked"  name="<?php echo $key; ?>[]" value="<?php echo implode(",",$value); ?>">
            </div>
        <?php endforeach;?>
        <?php if (!empty($result_get['termo_consulta'])): ?>
            <div class="uk-form-row">
                <label>Consulta: <?php echo $result_get['termo_consulta']; ?></label>
                <input type="checkbox" checked="checked"  name="search_index" value="<?php echo $result_get['termo_consulta']; ?>">
            </div>
        <?php endif; ?>
        <?php if (!empty($result_get['data_inicio'])): ?>
            <div class="uk-form-row">
                <label>Data inicial: <?php echo $result_get['data_inicio']; ?></label>
                <input type="checkbox" checked="checked"  name="date_init" value="<?php echo $result_get['data_inicio']; ?>">
            </div>
        <?php endif; ?>
        <?php if (!empty($result_get['data_fim'])): ?>
            <div class="uk-form-row">
                <label>Data final: <?php echo $result_get['data_fim']; ?></label>
                <input type="checkbox" checked="checked"  name="date_end" value="<?php echo $result_get['data_fim']; ?>">
            </div>
        <?php endif; ?>         
        <div class="uk-form-row"><button type="submit" class="uk-button-primary">Retirar filtros</button></div>
    </fieldset>        
    </form>    
    <hr>
    <h3 class="uk-panel-title">Refinar meus resultados</h3>    
    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
        <hr>
    <?php 
        gerar_faceta($query_aggregate,$escaped_url,$client,"base",10,"Base",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"type",10,"Tipo de material",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"unidadeUSPtrabalhos",100,"Unidade USP",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"departamentotrabalhos",100,"Departamento",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"authors",120,"Autores",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"authorUSP",100,"Autores USP",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"year",120,"Ano de publicação","desc");
        gerar_faceta($query_aggregate,$escaped_url,$client,"subject",100,"Assuntos",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"language",40,"Idioma",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"ispartof",100,"É parte de ...",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"publisher",100,"Editora",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"evento",100,"Nome do evento",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"country",200,"País de publicação",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"tipotese",30,"Tipo de tese",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"areaconcentracao",100,"Área de concentração",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"indexado",100,"Indexado em",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"fatorimpacto",1000,"Fator de impacto","desc");         
        gerar_faceta($query_aggregate,$escaped_url,$client,"grupopesquisa",100,"Grupo de pesquisa",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"internacionalizacao",30,"Internacionalização",null);  
        gerar_faceta($query_aggregate,$escaped_url,$client,"colab",120,"País dos autores externos à USP",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"colab_instituicao_corrigido",100,"Colaboração institucional",null);
        gerar_faceta($query_aggregate,$escaped_url,$client,"fomento",100,"Agência de fomento",null);
        gerar_faceta_range($query_aggregate,$escaped_url,$client,"metrics.three_years_citations_scopus",100,"Citações nos últimos 3 anos na Scopus");
        gerar_faceta_range($query_aggregate,$escaped_url,$client,"metrics.full_citations_scopus",100,"Total de citações na Scopus");
    ?>
    </ul>
        <?php if(!empty($_SESSION['oauthuserdata'])): ?>
            <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
            <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
            <hr>
            <?php         
                gerar_faceta($query_aggregate,$escaped_url,$client,"codpesbusca",100,"Número USP",null);
                gerar_faceta($query_aggregate,$escaped_url,$client,"codpes",100,"Número USP / Unidade",null);
                gerar_faceta($query_aggregate,$escaped_url,$client,"issn_part",100,"ISSN",null);
                gerar_faceta($query_aggregate,$escaped_url,$client,"colab_int_trab",100,"Colaboração - Internacionalização",null); gerar_faceta($query_aggregate,$escaped_url,$client,"colab_instituicao_trab",100,"Colaboração - Instituição",null); gerar_faceta($query_aggregate,$escaped_url,$client,"colab_instituicao_corrigido",100,"Colaboração - Instituição - Corrigido",null); corrigir_faceta($query_aggregate,$escaped_url,$client,"colab_instituicao_naocorrigido",10,"Colaboração - Instituição - Não corrigido");
                gerar_faceta($query_aggregate,$escaped_url,$client,"dataregistroinicial",100,"Data de registro","desc");
                gerar_faceta($query_aggregate,$escaped_url,$client,"dataregistro",100,"Data de registro e alterações","desc");
            ?>
            </ul>
        <?php endif; ?>
    <hr>
    <form class="uk-form">
    <fieldset>
        <legend>Limitar datas</legend>
        <div class="uk-form-row">
            <label>Ano inicial</label>
            <input type="text" placeholder="Ano inicial" name="date_init">
        </div>
        <div class="uk-form-row">
            <label>Ano final</label>
            <input type="text" placeholder="Ano final" name="date_end">
        </div>
        <?php foreach ($new_get as $key => $value) : ?>
            <div class="uk-form-row">
                <input type="hidden" checked="checked"  name="<?php echo $key; ?>[]" value="<?php echo implode(",",$value); ?>">
            </div>
        <?php endforeach;?>
        <?php if (!empty($result_get['termo_consulta'])): ?>
            <div class="uk-form-row">
                <input type="hidden" checked="checked"  name="search_index" value="<?php echo $result_get['termo_consulta']; ?>">
            </div>
        <?php endif; ?>
        <div class="uk-form-row"><button class="uk-button-primary">Limitar datas</button></div>
    </fieldset>        
    </form>
    <hr>
    <?php if(!empty($_SESSION['oauthuserdata'])): ?>
        <form class="uk-form" method="get" action="report.php">
            <fieldset>
                <legend>Gerar relatório</legend>
                <?php foreach ($new_get as $key => $value) : ?>
                    <div class="uk-form-row">
                        <input type="hidden" checked="checked"  name="<?php echo $key; ?>[]" value="<?php echo implode(",",$value); ?>">
                    </div>
                <?php endforeach;?>
                <?php if (!empty($result_get['termo_consulta'])): ?>
                    <div class="uk-form-row">
                        <input type="hidden" checked="checked"  name="search_index" value="<?php echo $result_get['termo_consulta']; ?>">
                    </div>
                <?php endif; ?>
                <?php if (!empty($result_get['data_inicio'])): ?>
                    <div class="uk-form-row">
                        <input type="hidden" checked="checked"  name="date_init" value="<?php echo $result_get['data_inicio']; ?>">
                    </div>
                <?php endif; ?>
                <?php if (!empty($result_get['data_fim'])): ?>
                    <div class="uk-form-row">
                        <input type="hidden" checked="checked"  name="date_end" value="<?php echo $result_get['data_fim']; ?>">
                    </div>
                <?php endif; ?>         
                <div class="uk-form-row"><button type="submit" class="uk-button-primary">Gerar relatório</button>
                </div>
            </fieldset>        
        </form>
    <?php endif; ?>                
            
</div>
    
                    

                    
                </div>
                <div class="uk-width-small-1-2 uk-width-medium-4-6">
                    
                <div class="uk-alert" data-uk-alert>
                    <a href="" class="uk-alert-close uk-close"></a>
                
                    
                <?php $ano_bar = generateDataGraphBar($client, $query_aggregate, 'year', "_term", 'desc', 'Ano', 10); ?>

                <div id="ano_chart" class="uk-visible-large"></div>
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
                            width: 600,
                            height: 140
                        }
                    })
                </script>                        
                    </div>
                    
                <?php if (isset($_REQUEST["assunto"])) : ?>    
                   <div class="uk-alert" data-uk-alert>
                       <a href="" class="uk-alert-close uk-close"></a>
                       <?php consultar_vcusp($_REQUEST["assunto"]); ?>
                   </div>
                <?php endif; ?>
                    
                    <div class="uk-grid uk-margin-top">
                        <div class="uk-width-1-3">                        
                        <ul class="uk-subnav uk-nav-parent-icon uk-subnav-pill">
                            <li>Ordenar por:</li>

                            <!-- This is the container enabling the JavaScript -->
                            <li data-uk-dropdown="{mode:'click'}">

                                <!-- This is the nav item toggling the dropdown -->
                                <a href="">Data (Novos)</a>

                                <!-- This is the dropdown -->
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li><a href="">Data (Antigos)</a></li>
                                        <li><a href="">Título</a></li>
                                    </ul>
                                </div>

                            </li>
                        </ul>                        
                            
                        </div>
                        <div class="uk-width-1-3"><p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p></div>
                        <div class="uk-width-1-3">
                            <ul class="uk-pagination" data-uk-pagination="{items:<?php print_r($total);?>,itemsOnPage:<?php print_r($limit);?>,displayedPages:3,edges:1,currentPage:<?php print_r($page-1);?>}"></ul>                         
                        </div>
                    </div>
                    
                    <hr class="uk-grid-divider">
                    <div class="uk-width-1-1 uk-margin-top uk-description-list-line">
                    <ul class="uk-list uk-list-line">   
                    <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                    
                    <!--    
                        
                    < ?php if (!empty($r["_source"]['issn_part'][0])) : ?>
                            < ?php $issn_info = get_title_elsevier(str_replace("-","",$r["_source"]['issn_part'][0]),$api_elsevier); ?>
                    < ?php endif; ?>

                    -->
                        
                        <li>                        
                            <div class="uk-grid uk-flex-middle" data-uk-grid-   margin="">
                                <div class="uk-width-medium-2-10 uk-row-first">
                                    <div class="uk-panel uk-h6 uk-text-break">
                                        <a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a>
                                    </div>
                                    <div class="uk-panel uk-h6 uk-text-break">

                                        <?php

                                        if (isset($issn_info["serial-metadata-response"])) {                                     

                                            $image_url = "{$issn_info["serial-metadata-response"]["entry"][0]["link"][2]["@href"]}&apiKey={$api_elsevier}";   

                                            if (exif_imagetype($image_url) == IMAGETYPE_GIF) {
                                                echo '<img src="'.$image_url.'">';
                                            }

                                        }                                    

                                        ?>
                                      
                                    </div>
                                    
                                    <div class="uk-panel uk-h6 uk-text-break">

                                        <?php
                                            if (isset($issn_info["serial-metadata-response"])) {
                                                if ($issn_info["serial-metadata-response"]["entry"][0]["openaccess"] == '1') {                                     
                                                        echo '<img src="inc/images/openaccess-t.png">';
                                                }
                                            }
                                        ?>
                                      
                                    </div>                                     
                                    
                                </div>
                                <div class="uk-width-medium-8-10 uk-flex-middle">
                                    
                                    <ul class="uk-list">
                                        <li class="uk-margin-top uk-h4">
                                            <strong><a href="single.php?_id=<?php echo  $r['_id'];?>"><?php echo $r["_source"]['title'];?> (<?php echo $r["_source"]['year']; ?>)</a></strong>
                                        </li>
                                        <li class="uk-h6">
                                            Autores:
                                            <?php if (!empty($r["_source"]['authors'])) : ?>
                                            <?php foreach ($r["_source"]['authors'] as $autores) {
                                                $authors_array[]='<a href="result.php?authors[]='.$autores.'">'.$autores.'</a>';
                                            } 
                                           $array_aut = implode(", ",$authors_array);
                                            unset($authors_array);
                                            print_r($array_aut);
                                            ?>
                                            
                                           
                                            <?php endif; ?>                           
                                        </li>
                                        
                                        <?php if (!empty($r["_source"]['ispartof'])) : ?><li class="uk-h6">In: <a href="result.php?ispartof[]=<?php echo $r["_source"]['ispartof'];?>"><?php echo $r["_source"]['ispartof'];?></a></li><?php endif; ?>
                                        
                                        <li class="uk-h6">
                                            Unidades USP:
                                            <?php if (!empty($r["_source"]['unidadeUSP'])) : ?>
                                            <?php $unique =  array_unique($r["_source"]['unidadeUSP']); ?>
                                            <?php foreach ($unique as $unidadeUSP) : ?>
                                                <a href="result.php?unidadeUSP[]=<?php echo $unidadeUSP;?>"><?php echo $unidadeUSP;?></a>
                                            <?php endforeach;?>
                                            <?php endif; ?>
                                        </li>
                                        
                                        <li class="uk-h6">
                                            Assuntos:
                                            <?php if (!empty($r["_source"]['subject'])) : ?>
                                            <?php foreach ($r["_source"]['subject'] as $assunto) : ?>
                                                <a href="result.php?subject[]=<?php echo $assunto;?>"><?php echo $assunto;?></a>
                                            <?php endforeach;?>
                                            <?php endif; ?>
                                        </li>
                                        <?php if (!empty($r["_source"]['fatorimpacto'])) : ?>
                                        <li class="uk-h6">
                                            <p>Fator de impacto da publicação: <?php echo $r["_source"]['fatorimpacto'][0]; ?></p>
                                        </li>
                                        <?php endif; ?>
                                        <div class="uk-alert">
                                            <h5>Acesso ao documento:</h5>
                                            <li>
                                                <?php if (!empty($r["_source"]['url'])||!empty($r["_source"]['doi'])) : ?>
                                                <div class="uk-button-group" style="padding:15px 15px 15px 0;">     
                                                    <?php if (!empty($r["_source"]['url'])) : ?>
                                                    <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                    <?php if ($url != '') : ?>
                                                    <a class="uk-button-small uk-button-primary" href="<?php echo $url;?>" target="_blank">Acesso online à fonte</a>
                                                    <?php endif; ?>
                                                    <?php endforeach;?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($r["_source"]['doi'])) : ?>
                                                    <a class="uk-button-small uk-button-primary" href="http://dx.doi.org/<?php echo $r["_source"]['doi'][0];?>" target="_blank">Resolver DOI</a>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                            </li>
                                            <?php if (isset($issn_info["serial-metadata-response"])): ?>
                                            <div class="uk-alert">
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
                                        
                                            <li>
                                                <?php
                                                    $sfx_array[] = 'rft.atitle='.$r["_source"]['title'].'';
                                                    $sfx_array[] = 'rft.year='.$r["_source"]['year'].'';
                                                    if (!empty($r["_source"]['ispartof'])) {
                                                        $sfx_array[] = 'rft.jtitle='.$r["_source"]['ispartof'].'';
                                                    }
                                                    if (!empty($r["_source"]['doi'])) {
                                                        $sfx_array[] = 'rft_id=info:doi/'.$r["_source"]['doi'][0].'';
                                                    }
                                                    if (!empty($r["_source"]['issn_part'][0])) {
                                                        $sfx_array[] = 'rft.issn='.$r["_source"]['issn_part'][0].'';
                                                    }
                                                    if (!empty($r["_source"]['ispartof_data'][0])) {
                                                        $sfx_array[] = 'rft.volume='.trim(str_replace("v.","",$r["_source"]['ispartof_data'][0])).'';
                                                    }                                             
                                                ?>
                                                <a href="http://143.107.154.66:3410/sfxlcl41?<?php echo implode("&",$sfx_array); unset($sfx_array); ?>" target="_blank">Buscar este registro por <img src="http://143.107.154.66:3410/sfxlcl41/sfx.gif"></a>
                                            </li>

                                            <li class="uk-h6 uk-margin-top">
                                               <?php load_itens_new($r['_id']); ?>
                                            </li>
                                        </div> 
                                        <?php if (!empty($r["_source"]['doi'])) : ?>
                                        <li class="uk-h6 uk-margin-top">
                                            <p>Métricas:</p>
                                            <ul>
                                                <li>
                                                    <div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $r["_source"]['doi'][0];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                                </li>
                                                <li>
                                                    <a href="https://plu.mx/plum/a/?doi=<?php echo $r["_source"]['doi'][0];?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true"></a>
                                                </li>
                                                <li>
                                                     <object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
                                                    <!--
                                                    < ?php 
                                                        $citations_scopus = get_citations_elsevier($r["_source"]['doi'][0],$api_elsevier);
                                                        if (!empty($citations_scopus['abstract-citations-response'])) {
                                                            echo '<a href="https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp='.$citations_scopus['abstract-citations-response']['identifier-legend']['identifier'][0]['scopus_id'].'&origin=inward">Citações na SCOPUS: '.$citations_scopus['abstract-citations-response']['citeInfoMatrix']['citeInfoMatrixXML']['citationMatrix']['citeInfo'][0]['rowTotal'].'</a>';
                                                            echo '<br/><br/>';
                                                        } 
                                                    ? >
                                                    -->
                                                </li>
                                            </ul>  
                                        </li>
                                        <?php endif; ?>
                                        <a href="#" class="uk-margin-top" data-uk-toggle="{target:'#citacao<?php echo  $r['_id'];?>'}">Ver citação</a>
                                        <div id="citacao<?php echo  $r['_id'];?>" class="uk-hidden">
                                        <li class="uk-h6 uk-margin-top">
                                            <div class="uk-alert uk-alert-danger">A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
                                            <ul>
                                                <li class="uk-margin-top">
                                                    <p><strong>ABNT</strong></p>
                                                    <?php
                                                        $data = gera_consulta_citacao($r["_source"]);
                                                        print_r($citeproc_abnt->render($data, $mode));
                                                    ?>
                                                </li>
                                                <li class="uk-margin-top">
                                                    <p><strong>APA</strong></p>
                                                    <?php
                                                        $data = gera_consulta_citacao($r["_source"]);
                                                        print_r($citeproc_apa->render($data, $mode));
                                                    ?>
                                                </li>
                                                <li class="uk-margin-top">
                                                    <p><strong>NLM</strong></p>
                                                    <?php
                                                        $data = gera_consulta_citacao($r["_source"]);
                                                        print_r($citeproc_nlm->render($data, $mode));
                                                    ?>
                                                </li>
                                                <li class="uk-margin-top">
                                                    <p><strong>Vancouver</strong></p>
                                                    <?php
                                                        $data = gera_consulta_citacao($r["_source"]);
                                                        print_r($citeproc_vancouver->render($data, $mode));
                                                    ?>
                                                </li>                                                 
                                            </ul>                                              
                                        </li>
                                        </div>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    <?php endforeach;?>
                    </ul>
                    </div>
                    <hr class="uk-grid-divider">
                    <div class="uk-grid uk-margin-top">
                        <div class="uk-width-1-2"><p class="uk-text-center"><?php print_r($total);?> registros</p></div>
                        <div class="uk-width-1-2">
                            <ul class="uk-pagination" data-uk-pagination="{items:<?php print_r($total);?>,itemsOnPage:<?php print_r($limit);?>,displayedPages:3,edges:1,currentPage:<?php print_r($page-1);?>}"></ul>                         
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