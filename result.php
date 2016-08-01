<!DOCTYPE html>
<?php 
    include('inc/functions.php');

    $result_get = analisa_get($_GET);
    $query_complete = $result_get['query_complete'];
    $query_aggregate = $result_get['query_aggregate'];
    $escaped_url = $result_get['escaped_url'];
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $new_get = $result_get['new_get'];


    $cursor = query_elastic($query_complete);
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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Resultado da busca</title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
        <link rel="stylesheet" href="inc/uikit/css/docs.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>        
        <script src="inc/uikit/js/uikit.min.js"></script>
        <script src="inc/uikit/js/components/accordion.min.js"></script>
        <script src="inc/uikit/js/components/pagination.min.js"></script>
        <script src="inc/uikit/js/components/datepicker.min.js"></script>
        
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.2.2/d3.v3.min.js"></script>

        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
        
        <!-- Altmetric Script -->
        <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
        
    </head>
    <body>        

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
        gerar_faceta_new($query_aggregate,$escaped_url,base,10,"Base");
        gerar_faceta_new($query_aggregate,$escaped_url,type,10,"Tipo de material");
        gerar_faceta_new($query_aggregate,$escaped_url,unidadeUSPtrabalhos,100,"Unidade USP");              gerar_faceta_new($query_aggregate,$escaped_url,departamentotrabalhos,100,"Departamento");             gerar_faceta_new($query_aggregate,$escaped_url,authors,120,"Autores");
        gerar_faceta_new($query_aggregate,$escaped_url,year,120,"Ano de publicação","desc");
        gerar_faceta_new($query_aggregate,$escaped_url,subject,100,"Assuntos");
        gerar_faceta_new($query_aggregate,$escaped_url,language,40,"Idioma");
        gerar_faceta_new($query_aggregate,$escaped_url,ispartof,100,"É parte de ...");
        gerar_faceta_new($query_aggregate,$escaped_url,evento,100,"Nome do evento");
        gerar_faceta_new($query_aggregate,$escaped_url,country,200,"País de publicação");    
    ?>
    </ul>
    <h3 class="uk-panel-title uk-margin-top">Informações administrativas</h3>
    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
        <hr>
    <?php 
        gerar_faceta_new($query_aggregate,$escaped_url,authorUSP,100,"Autores USP");
        gerar_faceta_new($query_aggregate,$escaped_url,codpesbusca,100,"Número USP");
        gerar_faceta_new($query_aggregate,$escaped_url,codpes,100,"Número USP / Unidade"); gerar_faceta_new($query_aggregate,$escaped_url,internacionalizacao,30,"Internacionalização");                           gerar_faceta_new($query_aggregate,$escaped_url,tipotese,30,"Tipo de tese");
        gerar_faceta_new($query_aggregate,$escaped_url,fomento,100,"Agência de fomento");
        gerar_faceta_new($query_aggregate,$escaped_url,indexado,100,"Indexado em");
        gerar_faceta_new($query_aggregate,$escaped_url,issn_part,100,"ISSN");
        gerar_faceta_new($query_aggregate,$escaped_url,areaconcentracao,100,"Área de concentração");
        gerar_faceta_new($query_aggregate,$escaped_url,fatorimpacto,1000,"Fator de impacto","desc");
        gerar_faceta_new($query_aggregate,$escaped_url,grupopesquisa,100,"Grupo de pesquisa");
        gerar_faceta_new($query_aggregate,$escaped_url,colab,120,"País dos autores externos à USP");
        gerar_faceta_new($query_aggregate,$escaped_url,colab_int_trab,100,"Colaboração - Internacionalização"); gerar_faceta_new($query_aggregate,$escaped_url,colab_instituicao_trab,100,"Colaboração - Instituição"); gerar_faceta_new($query_aggregate,$escaped_url,colab_instituicao_corrigido,100,"Colaboração - Instituição - Corrigido"); corrigir_faceta_new($query_aggregate,$escaped_url,colab_instituicao_naocorrigido,100,"Colaboração - Instituição - Não corrigido");
        gerar_faceta_new($query_aggregate,$escaped_url,dataregistroinicial,100,"Data de registro","desc");
        gerar_faceta_new($query_aggregate,$escaped_url,dataregistro,100,"Data de registro e alterações","desc");
    ?>
    </ul>
    
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
</div>
                    

                    
                </div>
                <div class="uk-width-small-1-2 uk-width-medium-4-6">
                    
                <div class="uk-alert" data-uk-alert>
                    <a href="" class="uk-alert-close uk-close"></a>
                
                    
                <?php $ano_bar = generateDataGraphBar($url, $query_aggregate, 'year', "_term", 'desc', 'Ano', 10); ?>

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
                        <div class="uk-width-1-3"><p class="uk-text-center"><?php print_r($total);?> registros</p></div>
                        <div class="uk-width-1-3">
                            <ul class="uk-pagination" data-uk-pagination="{items:<?php print_r($total);?>,itemsOnPage:<?php print_r($limit);?>,displayedPages:3,edges:1,currentPage:<?php print_r($page-1);?>}"></ul>                         
                        </div>
                    </div>
                    
                    <hr class="uk-grid-divider">
                    <div class="uk-width-1-1 uk-margin-top uk-description-list-line">
                    <ul class="uk-list uk-list-line">   
                    <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                        <li>                        
                            <div class="uk-grid uk-flex-middle" data-uk-grid-   margin="">
                                <div class="uk-width-medium-2-10 uk-row-first">
                                    <div class="uk-panel uk-h6 uk-text-break">
                                        <a href="result.php?type[]=<?php echo $r["_source"]['type'];?>"><?php echo ucfirst(strtolower($r["_source"]['type']));?></a>
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
                                        <li class="uk-h6 uk-margin-top">
                                            <p>Métricas alternativas:</p>
                                            <ul>
                                                <li><div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $r["_source"]['doi'][0];?>" data-hide-no-mentions="true" class="altmetric-embed"></div></li>
                                                <li><object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object></li>
                                            </ul>  
                                        </li>
                                        <a href="#" data-uk-toggle="{target:'#exemplares<?php echo  $r['_id'];?>'}">Citar</a>
                                        <div id="exemplares<?php echo  $r['_id'];?>" class="uk-hidden">
                                        <li class="uk-h6 uk-margin-top">
                                            <div class="uk-alert uk-alert-danger">Citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
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