<!DOCTYPE html>
<?php 
    include('inc/functions.php');

/* Missing query */
    foreach( $_GET as $k => $v ){
        if($v == 'N/D'){
            $filter[] = '{"missing" : { "field" : "'.$k.'" }}';
            unset($_GET[$k]); 
        }    
    }

/* limpar base all */
if (isset($_GET['base']) && $_GET['base'] == 'all'){
    unset($_GET['base']);
}

/* Subject */
if (isset($_GET['assunto'])){   
    $_GET['subject'][] = $_GET['assunto'];
    unset($_GET['assunto']);
}

/* Pagination variables */
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        unset($_GET['page']); 
    } else {
        $page = 1;
    }

    $limit = 20;
    $skip = ($page - 1) * $limit;

    $next = ($page + 1);
    $prev = ($page - 1);
    $sort = array('year' => -1);

/* Pegar a URL atual */
if (isset($_GET)){
    foreach ($_GET as $key => $value){
        $new_get[] = ''.$key.'[]='.$value[0].'';
        $query_get = implode("&",$new_get);
}
    $url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['PHP_SELF'].'?'.$query_get.'';
} else {
    $url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['PHP_SELF'].'';
}
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

/*
    if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'';          
    } else {
        $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'?';        
    }
        $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
*/


/* Montar a consulta */
    if (!empty($_GET["date_init"])||(!empty($_GET["date_end"]))) {
        $date_range = '
        {
            "range" : {
                "year" : {
                    "gte" : '.$_GET["date_init"].',
                    "lte" : '.$_GET["date_end"].'
                }
            }
        }
        ';
        unset($_GET["date_init"]);
        unset($_GET["date_end"]); 
    }



    if (empty($_GET)) {
        $search_term = '"match_all": {}';
        $filter_query = '';

    } elseif (!empty($_GET['search_index'])) {
        $search_term ='"query": {
        "match" : {
            "_all" : {
            "query": "'.$_GET['search_index'].'",
            "operator" : "and"
            }
        }}'; 
        $termo = $_GET['search_index']; 
        unset($_GET['search_index']);

       foreach ($_GET as $key => $value) {
            $filter[] = '{"term":{"'.$key.'":"'.$value.'"}}';
        }

        if (!empty($date_range)) {
            $filter[] = $date_range;
        }

        if (count($filter) > 0) {
            $filter_query = ''.implode(",", $filter).''; 
        } else {
            $filter_query = '';
        }
        $_GET['search_index'] = $termo;


        $query_complete = '{
        "sort" : [
                { "year" : "desc" }
            ],    
        "query": {    
        "bool": {
          "must": {
            '.$search_term.'
          },
          "filter":[
            '.$filter_query.'        
            ]
          }
        },
        "from": '.$skip.',
        "size": '.$limit.'
        }';

        $query_aggregate = '
            "query": {
                "bool": {
                  "must": {
                    '.$search_term.'
                  },
                  "filter":[
                    '.$filter_query.'
                    ]
                  }
                },
            ';


    } else {

        $query_complete = monta_consulta($_GET,$skip,$limit,$date_range);   
        $query_aggregate = monta_aggregate($_GET,$date_range);


    }

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
        
    </head>
    <body>        
        <div class="barrausp">
            <div class="uk-container uk-container-center">

            <nav class="uk-margin-top">
                <a class="uk-navbar-brand uk-hidden-small" href="http://sibi.usp.br" style="color:white">SIBiUSP</a>
                <ul class="uk-navbar-nav uk-hidden-small">
                    <li>
                        <a href="index.php" style="color:white">Início</a>
                    </li>
                    <li>
                        <a href="#" data-uk-toggle="{target:'#busca_avancada'}" style="color:white">Busca avançada</a>
                    </li>
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
                                <a href="login.php" style="color:white">Login</a>
                            </li>
                            <li>
                                <a href="about.php" style="color:white">Sobre</a>
                            </li>
                        </ul>
                    </div>    
                </ul>
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <div class="uk-navbar-brand uk-navbar-center uk-visible-small" style="color:white">BDPI USP</div>
            </nav>
            
            </div>
            <div id="busca_avancada" class="uk-container uk-container-center uk-grid uk-hidden" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <div class="uk-alert uk-alert-large"><p>Teste</p></div>
                </div>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="uk-grid" data-uk-grid>                        
                <div class="uk-width-small-1-2 uk-width-medium-2-6">                    
                    

<div class="uk-panel uk-panel-box">
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
            <?php foreach ($_GET as $key => $value) {
                echo '<input type="hidden" name="'.$key.'[]" value="'.$value[0].'">';
            };?>
            <?php if (!empty($q)) {
                echo '<input type="hidden" name="category" value="buscaindice">';
                echo '<input type="hidden" name="q" value="'.$q.'">';
            }; ?>
        </div>
        <div class="uk-form-row"><button class="uk-button">Limitar datas</button></div>
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
                                            <div class="uk-button-group">
                                                <?php if (!empty($r["_source"]['url'])) : ?>
                                                <?php foreach ($r["_source"]['url'] as $url) : ?>
                                                <?php if ($url != '') : ?>
                                                <a class="uk-button-small uk-button-primary" href="<?php echo $url;?>" target="_blank">Acesso online</a>
                                                <?php endif; ?>
                                                <?php endforeach;?>
                                                <?php endif; ?>
                                                <?php if (!empty($r['doi'])) : ?>
                                                <a class="uk-button-small uk-button-primary" href="http://dx.doi.org/<?php echo $r["_source"]['doi'][0];?>" target="_blank">Acesso online</a>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                        <li class="uk-h6">
                                           <?php load_itens_new($r['_id']); ?>
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
            <div id="footer" class="uk-grid" data-uk-grid-margin>
                <p>Sistema Integrado de Bibliotecas da Universidade de São Paulo</p>
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
        
    </body>
</html>