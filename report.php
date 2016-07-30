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
        <title>BDPI USP - Relatório Gerencial</title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>        
        <script src="inc/uikit/js/uikit.min.js"></script>
        <script src="inc/uikit/js/components/accordion.min.js"></script>
        <script src="inc/uikit/js/components/pagination.min.js"></script>
        
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

        <?php include('inc/navbar.php'); ?>
 
     <div class="uk-container uk-container-center">   
        
         <h3 class="uk-margin-top">Relatório com os seguintes parâmetros:
                    <?php foreach ($_GET as $filters) : ?>
                    <?php echo implode(",",$filters);?>
                    <?php endforeach;?>
        </h3>

        <div>
            <h3 class="ui header">Total: <?php echo $total; ?> registros</h3>
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
                <button class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_type; ?>','tipo_de_material.csv','text/plain;charset=utf-8')">
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
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_unidadeUSPtrabalhos; ?>','unidadeUSP_trabalhos.csv','text/plain;charset=utf-8')">
                    Exportar todas os trabalhos por unidades em csv
                </button>      

                <h3>Unidade USP - Participações (10 primeiros)</h3>
                <?php generateDataTable($url, $query_aggregate, 'unidadeUSP', "_count", 'desc', 'Unidade USP - Participações', 9); ?>
                <?php $csv_unidadeUSP = generateCSV($url, $query_aggregate, 'unidadeUSP', "_count", 'desc', 'Unidade USP - Participações', 10000); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_unidadeUSP; ?>','unidadeUSP_participacoes.csv','text/plain;charset=utf-8')">
                    Exportar todas participações por Unidade em csv
                </button>




                <h3>Departamento - Participações</h3>
                <?php generateDataTable($url, $query_aggregate, 'departamento', "_count", 'desc', 'Departamento - Participações', 9); ?>
                <?php $csv_departamento = generateCSV($url, $query_aggregate, 'departamento', "_count", 'desc', 'Departamento - Participações', 10000); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_departamento); ?>','departamento_part.csv','text/plain;charset=utf-8')">
                    Exportar todos as participações dos departamentos em csv
                </button>



                <h3>Autores USP (10 primeiros)</h3>
                <?php generateDataTable($url, $query_aggregate, 'authorUSP', "_count", 'desc', 'Autores USP', 9); ?>
                <?php $csv_authorUSP = generateCSV($url, $query_aggregate, 'authorUSP', "_count", 'desc', 'Autores USP', 10000); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_authorUSP); ?>','autoresUSP.csv','text/plain;charset=utf-8')">
                    Exportar todos os autores em csv
                </button>


                <h3>Obra da qual a produção faz parte (10 primeiros)</h3>      
                <?php generateDataTable($url, $query_aggregate, 'ispartof', "_count", 'desc', 'Obra da qual a produção faz parte', 9); ?>
                <?php $csv_ispartof = generateCSV($url, $query_aggregate, 'ispartof', "_count", 'desc', 'Obra da qual a produção faz parte', 20000); ?>
                <?php $csv_ispartof = str_replace('"', '', $csv_ispartof); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_ispartof); ?>','obras.csv','text/plain;charset=utf-8')">
                    Exportar todos as obras em csv
                </button>


                <h3>Nome do evento (10 primeiros)</h3>        
                <?php generateDataTable($url, $query_aggregate, 'evento', "_count", 'desc', 'Nome do evento', 9); ?>
                <?php $csv_evento = generateCSV($url, $query_aggregate, 'evento', "_count", 'desc', 'Nome do evento', 10000); ?>
                <?php $csv_evento = str_replace('"', '', $csv_evento); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_evento); ?>','evento.csv','text/plain;charset=utf-8')">
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
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_year; ?>','ano.csv','text/plain;charset=utf-8')">
                    Exportar todos os anos em csv
                </button>

                <h3>Idioma</h3>       
                <?php generateDataTable($url, $query_aggregate, 'language', "_count", 'desc', 'Idioma', 10); ?>
                <?php $csv_language = generateCSV($url, $query_aggregate, 'language', "_count", 'desc', 'Idioma', 10000); ?>
                <button  class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_language; ?>','idioma.csv','text/plain;charset=utf-8')">
                    Exportar todos os idiomas em csv
                </button>

                <h3>Internacionalização</h3>  



<?php $internacionalizacao_bar = generateDataGraphBar($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10); ?>
<div id="internacionalizacao_chart"></div>         
<script type="application/javascript">
var graphdef = {
categories : ['internacionalização'],
dataset : {
'internacionalização' : [<?= $internacionalizacao_bar; ?>]
}
}
var chart = uv.chart ('Pie', graphdef, {
meta : {
position: '#internacionalizacao_chart',
caption : 'Internacionalização',
subcaption : 'Trabalhos publicados em publicações internacionais',
hlabel : 'Registros',
vlabel : 'Local',
isDownloadable: true,
downloadLabel: 'Baixar'
},
dimension : {
width: 800,
height: 600
}
})
</script>      

<?php generateDataTable($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10); ?>
<?php $csv_internacionalizacao = generateCSV($url, $query_aggregate, 'internacionalizacao', "_count", 'desc', 'Internacionalização', 10000); ?>
<button  class="uk-button-primary" onclick="SaveAsFile('<?php echo $csv_internacionalizacao; ?>','internacionalizacao.csv','text/plain;charset=utf-8')">Exportar em csv</button>

<h3>País de publicação</h3>
<?php generateDataTable($url, $query_aggregate, 'country', "_count", 'desc', 'País de publicação', 10); ?>
<?php $csv_country = generateCSV($url, $query_aggregate, 'country', "_count", 'desc', 'País de publicação', 10000); ?>
<button  class="uk-button-primary" onclick="SaveAsFile('<?php echo str_replace("'", "", $csv_country); ?>','pais.csv','text/plain;charset=utf-8')">Exportar todos em csv</button>
         
         
         
         

            <hr class="uk-grid-divider">
<?php include('inc/footer.php'); ?>         
    </div>
                
<?php include('inc/offcanvas.php'); ?>        
        
    </body>
</html>