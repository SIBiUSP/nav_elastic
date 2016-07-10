<!DOCTYPE html>
<?php 
include 'inc/functions.php';

/* Pegar a URL atual */
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
      $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'';
} else {
      $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'?';
}
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
   

/* Pagination variables */
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$limit = 15;
$skip = ($page - 1) * $limit;
$next = ($page + 1);
$prev = ($page - 1);
$sort = array('year' => -1);

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
    
    $query_complete = monta_consulta($_GET,$skip,$limit);   
    $query_aggregate = monta_aggregate($_GET);
    
}


$cursor = query_elastic($query_complete);
$total = $cursor["hits"]["total"];



?>
<html>
    <head>
        <title>BDPI USP - Relatório gerencial</title>
        <?php include('inc/meta-header.php'); ?>
        
        <!-- D3.js Libraries and CSS -->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.2.2/d3.v3.min.js"></script>

        <!-- UV Charts -->
        <script type="text/javascript" src=inc/uvcharts/uvcharts.full.min.js></script>
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/rgbcolor.js"></script> 
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/StackBlur.js"></script>
        <script type="text/javascript" src="http://gabelerner.github.io/canvg/canvg.js"></script> 
        <script type="text/javascript" src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.js"></script>

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
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <h3>Relatório com os seguintes parâmetros:
                    <?php foreach ($_GET as $filters) : ?>
                    <?php echo $filters;?>
                    <?php endforeach;?>
                </h3><br/><br/>


                <div class="ui vertical stripe segment">
                    <div class="ui text container">
                        <h3 class="ui header">Total</h3><br/><br/>
                        <div class="ui one statistics">
                            <div class="statistic">
                                <div class="value">
                                    <i class="file icon"></i> <?php echo $total; ?>
                                </div>
                                <div class="label">
                                    Quantidade de registros
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <h3>Tipo de publicação (Somente os primeiros)</h3>
                <?php $type_mat_bar = generateDataGraphBar($url, $query_aggregate, "type", "_count", "desc", 'Tipo de publicação', 4); ?>
       
                
                <div id="type_chart" style="font-size:10px"></div>
                <script type="application/javascript">
                    var graphdef = {
                        categories : ['Tipo'],
                        dataset : {
                            'Tipo' : [<?= $type_mat_bar; ?>]
                        }
                    }
                    var chart = uv.chart ('Bar', graphdef, {
                        meta : {
                            position: '#type_chart',
                            caption : 'Tipo de trabalho',
                            hlabel : 'Tipo',
                            vlabel : 'Registros',
                            isDownloadable: true,
                            downloadLabel: 'Baixar'
                        },
                        graph : {
                            orientation : "Vertical"
                        },
                        dimension : {
                            width: 900,
                            height: 300
                        }
                    })
                </script> 
                <?php generateDataTable($url, $query_aggregate, "type", "_count", "desc", 'Tipo de publicação', 9); ?>

                <?php $csv_type = generateCSV($url, $query_aggregate, 'type',  "_count", "desc", 'Tipo de publicação', 500); ?> 
                <button class="ui blue label" onclick="SaveAsFile('<?php echo $csv_type; ?>','tipo_de_material.csv','text/plain;charset=utf-8')">
                    Exportar todos os tipos de publicação em csv
                </button>


                <h3>Unidade USP - Trabalhos (10 primeiros)</h3>
                <?php $unidadeUSP_trab_bar = generateDataGraphBar($url, $query_aggregate, "unidadeUSPtrabalhos", "_count", "desc", 'Unidade USP - Trabalhos', 9); ?>


                <div id="unidadeUSP_chart"></div>
                <script type="application/javascript">
                    var graphdef = {
                        categories : ['Unidade USP'],
                        dataset : {
                            'Unidade USP' : [<?= $unidadeUSP_trab_bar; ?>]
                        }
                    }
                    var chart = uv.chart ('Bar', graphdef, {
                        meta : {
                            position: '#unidadeUSP_chart',
                            caption : 'Unidade USP',
                            hlabel : 'Unidade USP',
                            vlabel : 'Registros',
                            isDownloadable: true,
                            downloadLabel: 'Baixar'
                        },
                        graph : {
                            orientation : "Vertical"
                        },
                        dimension : {
                            width: 900,
                            height: 300
                        }
                    })
                </script> 

                <?php generateDataTable($url, $query_aggregate, 'unidadeUSPtrabalhos', "_count", "desc", 'Unidade USP - Trabalhos', 9); ?>
                <?php $csv_unidadeUSPtrabalhos = generateCSV($url, $query_aggregate, 'unidadeUSPtrabalhos', "_count", 'desc', 'Unidade USP - Trabalhos', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo $csv_unidadeUSPtrabalhos; ?>','unidadeUSP_trabalhos.csv','text/plain;charset=utf-8')">
                    Exportar todas os trabalhos por unidades em csv
                </button>      

                <h3>Unidade USP - Participações (10 primeiros)</h3>
                <?php generateDataTable($url, $query_aggregate, 'unidadeUSP', "_count", 'desc', 'Unidade USP - Participações', 9); ?>
                <?php $csv_unidadeUSP = generateCSV($url, $query_aggregate, 'unidadeUSP', "_count", 'desc', 'Unidade USP - Participações', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo $csv_unidadeUSP; ?>','unidadeUSP_participacoes.csv','text/plain;charset=utf-8')">
                    Exportar todas participações por Unidade em csv
                </button>




                <h3>Departamento - Participações</h3>
                <?php generateDataTable($url, $query_aggregate, 'departamento', "_count", 'desc', 'Departamento - Participações', 9); ?>
                <?php $csv_departamento = generateCSV($url, $query_aggregate, 'departamento', "_count", 'desc', 'Departamento - Participações', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_departamento); ?>','departamento_part.csv','text/plain;charset=utf-8')">
                    Exportar todos as participações dos departamentos em csv
                </button>



                <h3>Autores USP (10 primeiros)</h3>
                <?php generateDataTable($url, $query_aggregate, 'authorUSP', "_count", 'desc', 'Autores USP', 9); ?>
                <?php $csv_authorUSP = generateCSV($url, $query_aggregate, 'authorUSP', "_count", 'desc', 'Autores USP', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_authorUSP); ?>','autoresUSP.csv','text/plain;charset=utf-8')">
                    Exportar todos os autores em csv
                </button>


                <h3>Obra da qual a produção faz parte (10 primeiros)</h3>      
                <?php generateDataTable($url, $query_aggregate, 'ispartof', "_count", 'desc', 'Obra da qual a produção faz parte', 9); ?>
                <?php $csv_ispartof = generateCSV($url, $query_aggregate, 'ispartof', "_count", 'desc', 'Obra da qual a produção faz parte', 20000); ?>
                <?php $csv_ispartof = str_replace('"', '', $csv_ispartof); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_ispartof); ?>','obras.csv','text/plain;charset=utf-8')">
                    Exportar todos as obras em csv
                </button>


                <h3>Nome do evento (10 primeiros)</h3>        
                <?php generateDataTable($url, $query_aggregate, 'evento', "_count", 'desc', 'Nome do evento', 9); ?>
                <?php $csv_evento = generateCSV($url, $query_aggregate, 'evento', "_count", 'desc', 'Nome do evento', 10000); ?>
                <?php $csv_evento = str_replace('"', '', $csv_evento); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_evento); ?>','evento.csv','text/plain;charset=utf-8')">
                    Exportar todos os eventos em csv
                </button>


                <h3>Ano de publicação</h3>  
                <?php $ano_bar = generateDataGraphBar($url, $query_aggregate, 'year', "_term", 'desc', 'Ano', 19); ?>

                <div id="ano_chart"></div>
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
                            vlabel : 'Registros',
                            isDownloadable: true,
                            downloadLabel: 'Baixar'
                        },
                        graph : {
                            orientation : "Vertical"
                        },
                        dimension : {
                            width: 900,
                            height: 300
                        }
                    })
                </script>       

                <?php generateDataTable($url, $query_aggregate, 'year', "_term", 'desc', 'Ano de publicação', 200); ?>
                <?php $csv_year = generateCSV($url, $query_aggregate, 'year', "_term", 'asc', 'Ano de publicação', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo $csv_year; ?>','ano.csv','text/plain;charset=utf-8')">
                    Exportar todos os anos em csv
                </button>

                <h3>Idioma</h3>       
                <?php generateDataTable($url, $query_aggregate, 'language', "_count", 'desc', 'Idioma', 10); ?>
                <?php $csv_language = generateCSV($url, $query_aggregate, 'language', "_count", 'desc', 'Idioma', 10000); ?>
                <button  class="ui blue label" onclick="SaveAsFile('<?php echo $csv_language; ?>','idioma.csv','text/plain;charset=utf-8')">
                    Exportar todos os idiomas em csv
                </button>

                <h3>Internacionalização</h3>  



<?php $internacionalizacao_bar = generateDataGraphBar($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10); ?>
<div id="internacionalização_chart"></div>         
<script type="application/javascript">
var graphdef = {
categories : ['internacionalização'],
dataset : {
'internacionalização' : [<?= $internacionalizacao_bar; ?>]
}
}
var chart = uv.chart ('Pie', graphdef, {
meta : {
position: '#internacionalização_chart',
caption : 'Internacionalização',
subcaption : 'Trabalhos publicados em publicações internacionais',
hlabel : 'Registros',
vlabel : 'Local',
isDownloadable: true,
downloadLabel: 'Baixar'
},
dimension : {
width: document.getElementById("body").offsetWidth,
height: 600
}
})
</script>      

<?php generateDataTable($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10); ?>
<?php $csv_internacionalizacao = generateCSV($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10000); ?>
<button  class="ui blue label" onclick="SaveAsFile('<?php echo $csv_internacionalizacao; ?>','internacionalizacao.csv','text/plain;charset=utf-8')">Exportar em csv</button>

<h3>País de publicação</h3>
<?php generateDataTable($url, $query_aggregate, 'country', "_count", 'desc', 'País de publicação', 10); ?>
<?php $csv_country = generateCSV($url, $query_aggregate, 'country', "_count", 'desc', 'País de publicação', 10000); ?>
<button  class="ui blue label" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_country); ?>','pais.csv','text/plain;charset=utf-8')">Exportar todos em csv</button>

</div>

                
            </div>            
        <?php include('inc/footer.php'); ?>
<script>
$('.ui.accordion')
  .accordion()
;
</script>
<script>
$('.menu .item')
  .tab()
;
</script>
    </body>
</html>