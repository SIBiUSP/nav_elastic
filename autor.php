<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php');
        
                if (array_key_exists("codpes", $_GET)) { 
                    $result_get = get::analisa_get($_GET);
                    //$query_complete = $result_get['query_complete'];
                    $query_aggregate = $result_get['query_aggregate'];
                    $query_complete = '
                    {
                    '.$result_get['query_aggregate'].'
                    "size" : 10000
                    }            
                    ';                    
                    $limit = $result_get['limit'];
                    $page = $result_get['page'];                   
                    
                    $params = [
                        'index' => 'sibi',
                        'type' => 'producao',
                        'size'=> $limit, 
                        'body' => $query_complete
                    ];  

                    $cursor = $client->search($params);                        
                    //print_r($cursor);    
                    //$cursor = query_elastic($query_complete,$server);
                    $total = $cursor["hits"]["total"];
        
                } 
                    
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
        
            $ref_abnt[] = "";
            $record = [];
        
        ?>
        <title>BDPI USP - Autor USP</title>
        <script src="inc/uikit/js/components/slideset.js"></script>
        <script src="inc/uikit/js/components/accordion.min.js"></script>
        <script src="inc/uikit/js/components/pagination.min.js"></script>
        <script src="inc/uikit/js/components/datepicker.min.js"></script>
        
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.2.2/d3.v3.min.js"></script>

        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/rgbcolor.js"></script> 
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/StackBlur.js"></script>
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/canvg.js"></script> 
        <script type="text/javascript" src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.js"></script>        
        
        <!-- Altmetric Script -->
        <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>

        <!-- Save as javascript -->
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
        
    </head>

    <body>
        <!-- < ?php include_once("inc/analyticstracking.php") ?> -->
        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-top">

            <div class="uk-grid" data-uk-grid>                        
                <div class="uk-width-small-1-2 uk-width-2-6@m"> 
                    <div class="uk-panel uk-panel-box">
                        <form class="uk-form" method="get" action="result.php">
                        <fieldset>

                            <?php if (!empty($_GET["codpes"])) : ?>
                            <legend>Filtros ativos</legend>
                                <div class="uk-form-row">
                                    <p><?php print_r($_GET["codpes"]); ?></p><br/>
                                </div>
                            <div class="uk-form-row"><button type="submit" class="uk-button-primary">Retirar filtros</button></div>
                            <?php endif;?> 
                        </fieldset>        
                        </form>   
                        <hr>
                        <h3 class="uk-panel-title">Resumo</h3>    
                        <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
                            <hr>
                        <?php
                            $facets_author = new facets();
                            $facets_author->query_aggregate = $query_aggregate;
                            
                            $facets_author->facet("base",10,"Base",null);
                            $facets_author->facet("type",10,"Tipo de material",null);
                            $facets_author->facet("unidadeUSPtrabalhos",100,"Unidade USP",null);             
                            $facets_author->facet("departamentotrabalhos",100,"Departamento",null);             
                            $facets_author->facet("authors",120,"Autores",null);
                            $facets_author->facet("year",120,"Ano de publicação","desc");
                            $facets_author->facet("subject",100,"Assuntos",null);
                            $facets_author->facet("language",40,"Idioma",null);
                            $facets_author->facet("ispartof",100,"É parte de ...",null);
                            $facets_author->facet("evento",100,"Nome do evento",null);
                            $facets_author->facet("country",200,"País de publicação",null);    
                        ?>
                        </ul>
                        <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
                        <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
                            <hr>
                        <?php 
                            $facets_author->facet("authorUSP",100,"Autores USP",null);
                            $facets_author->facet("codpes",100,"Número USP",null);
                            $facets_author->facet("codpes_unidade",100,"Número USP / Unidade",null); 
                            $facets_author->facet("internacionalizacao",30,"Internacionalização",null);                           
                            $facets_author->facet("tipotese",30,"Tipo de tese",null);
                            $facets_author->facet("fomento",100,"Agência de fomento",null);
                            $facets_author->facet("indexado",100,"Indexado em",null);
                            $facets_author->facet("issn_part",100,"ISSN",null);
                            $facets_author->facet("areaconcentracao",100,"Área de concentração",null);
                            $facets_author->facet("fatorimpacto",1000,"Fator de impacto","desc");
                            $facets_author->facet("grupopesquisa",100,"Grupo de pesquisa",null);
                            $facets_author->facet("colab",120,"País dos autores externos à USP",null);
                            $facets_author->facet("colab_int_trab",100,"Colaboração - Internacionalização",null); 
                            $facets_author->facet("colab_instituicao_trab",100,"Colaboração - Instituição",null); 
                            $facets_author->facet("colab_instituicao_corrigido",100,"Colaboração - Instituição - Corrigido",null); 
                            $facets_author->facet("dataregistroinicial",100,"Data de registro","desc");
                            $facets_author->facet("dataregistro",100,"Data de registro e alterações","desc");
                        ?>
                        </ul>

                        <hr>
                        <form class="uk-form">
                        <fieldset>
                            <legend>Limitar datas</legend>

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
            
                <div class="uk-width-small-1-2 uk-width-4-6@m">
                    
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
                    
                    
                    <div class="uk-grid uk-margin-top">
                        <div class="uk-width-1-3"></div>
                        <div class="uk-width-1-3"><p class="uk-text-center"><?php print_r(number_format($total,0,',','.'));?> registros</p></div>
                        <div class="uk-width-1-3"></div>
                    </div>
                    
                                    <ul class="uk-tab" data-uk-tab="{connect:'#tab-content'}">
                                        <li class="uk-active" aria-expanded="true"><a href="#">Obras</a></li>
                                        <li aria-expanded="false" class=""><a href="#">Ver em formato referência</a></li>
                                    </ul>

                                    <ul id="tab-content" class="uk-switcher uk-margin">
                                        <li class="uk-active" aria-hidden="false">

                    <div class="uk-width-1-1 uk-margin-top uk-description-list-line">
                    <ul class="uk-list uk-list-line">   
                    <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                        <li>                        
                            <div class="uk-grid uk-flex-middle" data-uk-grid-   margin="">
                                <div class="uk-width-2-10@m uk-row-first">
                                    <div class="uk-panel uk-h6 uk-text-break">
                                        <a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a>
                                    </div>
                                </div>
                                <div class="uk-width-8-10@m uk-flex-middle">
                                    
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
                                        <li class="uk-h6 uk-margin-top">
                                           <?php load_itens_new($r['_id']); ?>
                                        </li>
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
                                        <a href="#" data-uk-toggle="{target:'#citacao<?php echo  $r['_id'];?>'}">Citar</a>
                                        <div id="citacao<?php echo  $r['_id'];?>" class="uk-hidden">
                                        <li class="uk-h6 uk-margin-top">
                                            <div class="uk-alert uk-alert-danger">A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
                                            <ul>
                                                <li class="uk-margin-top">
                                                    <p><strong>ABNT</strong></p>
                                                    <?php
                                                        $data = gera_consulta_citacao($r["_source"]);
                                                        print_r($citeproc_abnt->render($data, $mode));
                                                        $ref_abnt[] = $citeproc_abnt->render($data, $mode);
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
<?php

switch ($r["_source"]["type"]) {
case "ARTIGO DE PERIODICO":
    $record[] = "TY  - JOUR";
    break;
case "PARTE DE MONOGRAFIA/LIVRO":
    $record[] = "TY  - CHAP";
    break;
case "TRABALHO DE EVENTO-RESUMO":
    $record[] = "TY  - CPAPER";
    break;
case "TEXTO NA WEB":
    $record[] = "TY  - ICOMM";
    break;
}

$record[] = "TI  - ".$r["_source"]['title']."";

if (!empty($r["_source"]['year'])) {
$record[] = "PY  - ".$r["_source"]['year']."";
}

foreach ($r["_source"]['authors'] as $autores) {
  $record[] = "AU  - ".$autores."";
}

if (!empty($r["_source"]['ispartof'])) {
$record[] = "T2  - ".$r["_source"]['ispartof']."";
}

if (!empty($r["_source"]['issn_part'][0])) {
$record[] = "SN  - ".$r["_source"]['issn_part'][0]."";
}

if (!empty($r["_source"]["doi"])) {
$record[] = "DO  - ".$r["_source"]["doi"][0]."";
}

if (!empty($r["_source"]["url"])) {
  $record[] = "UR  - ".$r["_source"]["url"][0]."";
}

if (!empty($r["_source"]["publisher-place"])) {
  $record[] = "PP  - ".$r["_source"]["publisher-place"]."";
}

if (!empty($r["_source"]["publisher"])) {
  $record[] = "PB  - ".$r["_source"]["publisher"]."";
}

if (!empty($r["_source"]["ispartof_data"])) {
  foreach ($r["_source"]["ispartof_data"] as $ispartof_data) {
    if (strpos($ispartof_data, 'v.') !== false) {
      $record[] = "VL  - ".str_replace("v.","",$ispartof_data)."";
    } elseif (strpos($ispartof_data, 'n.') !== false) {
      $record[] = "IS  - ".str_replace("n.","",$ispartof_data)."";
    } elseif (strpos($ispartof_data, 'p.') !== false) {
      $record[] = "SP  - ".str_replace("p.","",$ispartof_data)."";
    }
  }
}
$record[] = "ER  - ";

?>
                        <?php
                            ob_flush();
                            flush(); 
                        ?>
                        <?php 
                        endforeach;
                        ob_end_flush();
                    ?>
                        
                    </ul>
                    </div>                                            
                                        
                                        </li>
                                        <li aria-hidden="true" class=""><?php echo implode("<br/>",$ref_abnt); ?></li>
                                    </ul>
                    
                    <hr class="uk-grid-divider">

                    <hr class="uk-grid-divider">
                    <div class="uk-grid uk-margin-top">
                        <div class="uk-width-1-2"><p class="uk-text-center"><?php print_r($total);?> registros</p></div>
                        <div class="uk-width-1-2">
                            <?php $record = str_replace("'","",$record); ?>
                            <?php $record = str_replace('"','',$record); ?>
                            <?php $record_blob = implode("\\n", $record); ?>                        
                            <button class="uk-button-small uk-button-primary" onclick="SaveAsFile('<?php echo $record_blob; ?>','record.ris','text/plain;charset=utf-8')">Exportar registros em formato RIS (EndNote)</button>
                            
                        </div>
                    </div>                   
                                        
                </div>
            </div>           
    

        
            <hr class="uk-grid-divider">
            
                    
            <?php include('inc/footer.php'); ?>

        </div>
        
        
        <?php include('inc/offcanvas.php'); ?>
        
    </body>
</html>