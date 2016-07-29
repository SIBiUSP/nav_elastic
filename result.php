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
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Resultado da busca</title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
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

        <div class="barrausp">
            <div class="uk-container uk-container-center">

            <nav class="uk-margin-top">
                <a class="uk-navbar-brand uk-hidden-small" href="index.php" style="color:white">BDPI USP</a>
                <ul class="uk-navbar-nav uk-hidden-small">
                    <li>
                        <a href="index.php" style="color:white">Início</a>
                    </li>
                    <li>
                        <a href="#" data-uk-toggle="{target:'#busca_avancada'}" style="color:white">Busca avançada</a>
                    </li>
                </ul>
                    <div class="uk-navbar-flip">
                        <ul class="uk-navbar-nav">
                            <li data-uk-dropdown="{mode:'click'}">
                                <a href="" style="color:white">
                                    Idioma
                                    <i class="uk-icon-caret-down"></i>
                                </a>
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li style="color:black"><a href="">Português</a></li>
                                        <li><a href="">Inglês</a></li>
                                    </ul>
                                </div> 
                            </li>
                            <li>
                                <a href="contato.php" style="color:white">Contato</a>
                            </li>
                            <li>
                                <a href="about.php" style="color:white">Sobre</a>
                            </li>
                            <li data-uk-dropdown="" aria-haspopup="true" aria-expanded="false">
                                <a href="" style="color:white"><i class="uk-icon-home"></i> Admin</a>

                                <div class="uk-dropdown uk-dropdown-navbar uk-dropdown-bottom" style="top: 40px; left: 0px;">
                                    <ul class="uk-nav uk-nav-navbar">
                                        <li class="uk-nav-header">Ferramentas</li>
                                        <li><a href="comparar_lattes.php">Comparador Lattes</a></li>
                                        <li><a href="comparar_wos.php">Comparador WoS</a></li>
                                        <li><a href="comparar_registros.php">Comparador weRUSP</a></li>
                                        <li class="uk-nav-divider"></li>
                                        <li class="uk-nav-header">Acesso</li>
                                        <li><a href="login.php">Login</a></li>
                                    </ul>
                                </div>

                            </li>
                            <a class="uk-navbar-brand uk-hidden-small" href="http://sibi.usp.br" style="color:white">SIBiUSP</a>
                        </ul>
                    </div>                
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <div class="uk-navbar-brand uk-navbar-center uk-visible-small" style="color:white">BDPI USP</div>
            </nav>
                
            </div>
            
            <div id="busca_avancada" class="uk-container uk-container-center uk-grid uk-hidden" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <div class="uk-alert uk-alert-large">
                        
                        
<form class="uk-form" role="form" action="result.php" method="get">

    <fieldset data-uk-margin>
        <legend>Número USP</legend>
        <input type="text" placeholder="Insira um número USP" name="codpesbusca[]">
        <button class="uk-button" type="submit">Buscar</button>
    </fieldset>

</form>
                        
<form class="uk-form" role="form" action="result.php" method="get" name="assunto">

    <fieldset data-uk-margin>
        <legend>Assunto do Vocabulário Controlado</legend>
        <label><a href="#" onclick="creaPopup('inc/popterms/index.php?t=assunto&f=assunto&v=http://143.107.154.55/pt-br/services.php&loadConfig=1'); return false;">Consultar o Vocabulário Controlado USP</a></label><br/>
        <input type="text" name="assunto">
        <button class="uk-button" type="submit">Buscar</button>
    </fieldset>

</form>                          
                        
                       
                    </div>
                </div>
            </div>
        </div>        
        
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
                                            <?php if (!empty($r["_source"]['url'])) : ?>
                                            <div class="uk-button-group" style="padding:15px 15px 15px 0;">     
                                                <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                <?php if ($url != '') : ?>
                                                <a class="uk-button-small uk-button-primary" href="<?php echo $url;?>" target="_blank">Acesso online</a>
                                                <?php endif; ?>
                                                <?php endforeach;?>
                                                <?php endif; ?>
                                                <?php if (!empty($r['doi'])) : ?>
                                                <a class="uk-button-small uk-button-primary" href="http://dx.doi.org/<?php echo $r["_source"]['doi'][0];?>" target="_blank">Acesso online</a>
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
            <div id="footer" data-uk-grid-margin>
                <p>Sistema Integrado de Bibliotecas</p>
                <p><img src="inc/images/logo-footer.png"></p>
            </div>          
        </div>
                
        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                <ul class="uk-nav uk-nav-offcanvas">
                    <li class="uk-active">
                        <a href="index.php">Início</a>
                    </li>
                    <li>
                        <a href="#">Busca avançada</a>
                    </li>
                    <li>
                        <a href="contact.php">Contato</a>
                    </li>
                    <li>
                        <a href="login.php">Login</a>
                    </li>
                    <li>
                        <a href="about.php">Sobre</a>
                    </li>
                </ul>
            </div>
        </div>

        <script>
        $('[data-uk-pagination]').on('select.uk.pagination', function(e, pageIndex){
            var url = window.location.href.split('&page')[0];
            window.location=url +'&page='+ (pageIndex+1);
        });
        </script>    
        <!-- ###### Script para criar o pop-up do popterms ###### -->
<script>
    function creaPopup(url)
    {
      tesauro=window.open(url,
      "Tesauro",
      "directories=no, menubar =no,status=no,toolbar=no,location=no,scrollbars=yes,fullscreen=no,height=600,width=450,left=500,top=0"
      )
    }
 </script>
    </body>
</html>