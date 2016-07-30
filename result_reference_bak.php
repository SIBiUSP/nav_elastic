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

/* Citation Style - Session - Default: ABNT */

if (empty($_SESSION["citation_style"])) {
    $_SESSION["citation_style"]="abnt";
}
if (isset($_POST["citation_style"])) {
    $_SESSION["citation_style"] = $_POST['citation_style'];
}   

/* Citeproc-PHP*/
include 'inc/citeproc-php/CiteProc.php';
$csl = file_get_contents('inc/citeproc-php/style/'.$_SESSION["citation_style"].'.csl');
$lang = "br";
$citeproc = new citeproc($csl,$lang);
$mode = "reference";

?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Resultado da busca - Referências</title>
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

        <div class="ui main container">

            <div id="main">
                
                <div class="ui main two column stackable grid">
                    <div class="four wide column">
                        <div class="item">
                            Filtros ativos
                            <div class="active content">
                                <div class="ui form">
                                    <div class="grouped fields">
                                        <form method="get" action="result.php">
                                            <?php foreach ($_REQUEST as $key => $value) : ?>
                                            <div class="field">
                                                <div class="ui checkbox">
                                                    <input type="checkbox" checked="checked"  name="<?php echo $key; ?>[]" value="<?php echo implode(",",$value); ?>">
                                                    <label><?php echo $key; ?>: <?php echo implode(",",$value); ?></label>
                                                </div>
                                            </div>
                                            <?php endforeach;?>
                                            <button type="submit" class="ui icon button">Retirar filtros</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3>Navegação</h3>
                        <div class="ui fluid vertical accordion menu">
                        <?php 
                            gerar_faceta_new($query_aggregate,$escaped_url,type,30,"Tipo de material");
                            gerar_faceta_new($query_aggregate,$escaped_url,unidadeUSPtrabalhos,30,"Unidade USP");
                            gerar_faceta_new($query_aggregate,$escaped_url,departamentotrabalhos,30,"Departamento");
                            gerar_faceta_new($query_aggregate,$escaped_url,authors,30,"Autores");
                            gerar_faceta_new($query_aggregate,$escaped_url,year,30,"Ano de publicação","desc");
                            gerar_faceta_new($query_aggregate,$escaped_url,subject,30,"Assuntos");
                            gerar_faceta_new($query_aggregate,$escaped_url,language,30,"Idioma");
                            gerar_faceta_new($query_aggregate,$escaped_url,ispartof,30,"Obra da qual a produção faz parte");
                            gerar_faceta_new($query_aggregate,$escaped_url,evento,30,"Nome do evento");
                            gerar_faceta_new($query_aggregate,$escaped_url,country,30,"País de publicação");
                            gerar_faceta_new($query_aggregate,$escaped_url,areaconcentracao,30,"Área de concentração");
                            gerar_faceta_new($query_aggregate,$escaped_url,authorUSP,30,"Autores USP");
                            gerar_faceta_new($query_aggregate,$escaped_url,codpesbusca,30,"Número USP");
                            gerar_faceta_new($query_aggregate,$escaped_url,fomento,30,"Agência de fomento");
                            gerar_faceta_new($query_aggregate,$escaped_url,indexado,30,"Indexado em");
                            gerar_faceta_new($query_aggregate,$escaped_url,fatorimpacto,30,"Fator de impacto");
                            gerar_faceta_new($query_aggregate,$escaped_url,grupopesquisa,30,"Grupo de pesquisa");
                            gerar_faceta_new($query_aggregate,$escaped_url,colab,30,"País dos autores externos à USP");
                            gerar_faceta_new($query_aggregate,$escaped_url,dataregistroinicial,30,"Data de registro","desc");
                        ?>
                        </div>  

                        <h3>Filtrar por data</h3>
                        <form method="get" action="<?php echo $escaped_url; ?>">
                            <div class="ui calendar" id="date_init">
                                <div class="ui input left icon">
                                    <i class="time icon"></i>
                                    <input type="text" placeholder="Ano inicial" name="date_init">
                                </div>
                            </div>
                            <div class="ui calendar" id="date_end">
                                <div class="ui input left icon">
                                    <i class="time icon"></i>
                                    <input type="text" placeholder="Ano final" name="date_end">
                                </div>
                            </div>
                            <?php foreach ($_GET as $key => $value) {
                                echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
                            };?>
                            <?php if (!empty($q)) {
                                echo '<input type="hidden" name="category" value="buscaindice">';
                                echo '<input type="hidden" name="q" value="'.$q.'">';
                            }; ?>
                            <button type="submit" class="ui icon button">Limitar datas</button>
                        </form>
                        <div>
                            <form method="post" action="report.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
                                <button type="submit" name="page" class="ui icon button" value="<?php echo $escaped_url;?>">
                                    Gerar relatório
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="twelve wide column">
                        
                        <div class="page-header"><h3>Resultado da busca: <?php print_r($total);?> registros</h3></div>
                            
                        <?php
                        echo '<br/><br/>';
                        /* Pagination - Start */
                        echo '<div class="ui buttons">';
                        if ($page > 1) {
                            echo '<form method="post" action="'.$escaped_url.'">';
                            echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
                            echo '<button type="submit" name="page" class="ui labeled icon button active" value="'.$prev.'">
                            <i class="left chevron icon"></i>Anterior</button>';
                            echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
                            if ($page * $limit < $total) {
                                echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                            } else {
                                echo '<button class="ui right labeled icon button disabled">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                            }
                            echo '</form>';
                        } else {
                            if ($page * $limit < $total) {
                                echo '<form method="post" action="'.$escaped_url.'">';
                                echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
                                echo '<button class="ui labeled icon button disabled"><i class="left chevron icon"></i>Anterior</button>';
                                echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
                                echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                                echo '</form>';
                            }
                        }
                        echo '</div>';
                        echo '<br/><br/>';
                        /* Pagination - End */
                        ?>
                        
                          <h3> Escolha o estilo da Citação:</h3>
                          <div class="ui compact menu">
                            <form method="post" action="result_reference.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
                              <button  type="submit" name="citation_style" class="ui icon button" value="apa">APA</button>
                            </form>
                            <form method="post" action="result_reference.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
                              <button type="submit" name="citation_style" class="ui icon button" value="abnt">ABNT</button>
                            </form>
                            <form method="post" action="result_reference.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
                              <button type="submit" name="citation_style" class="ui icon button" value="nlm">NLM</button>
                            </form>
                            <form method="post" action="result_reference.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
                              <button type="submit" name="citation_style" class="ui icon button" value="vancouver">Vancouver</button>
                            </form>
                          </div>
                        
                        <div class="ui divided items">
                            <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                            <div class="item">
                                <div class="image">
                                    <h4 class="ui center aligned icon header">
                                        <a class="ui blue label" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $r['_id'];?>">
                                            Ver no Dedalus
                                        </a>
                                    </h4>
                                        <?php if (!empty($r["_source"]['doi'])) : ?>
                                        <br/><br/>
                                        <object height="50" style="overflow:hidden" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
                                        <div data-badge-popover="right" data-badge-type="donut" data-doi="<?php echo $r["_source"]['doi'][0];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                        <?php endif; ?>                                     
                                </div>
                                <div class="content">
                                    <a class="ui medium header" href="single.php?_id=<?php echo $r['_id'];?>">
                                        <?php echo $r["_source"]['title'];?> (<?php echo $r["_source"]['year']; ?>)
                                    </a>
                                    <!--List authors -->
                                    <div class="extra">
                                        <a class="ui sub header">Autores:</a>
                                        <?php if (!empty($r["_source"]['authors'])) : ?>
                                        <?php foreach ($r["_source"]['authors'] as $autores) : ?>
                                            <div class="ui label" style="color:black;">
                                                <i class="user icon"></i>
                                                <a href="result_reference.php?authors=<?php echo $autores;?>"><?php echo $autores;?></a>
                                            </div>
                                        <?php endforeach;?>
                                        <?php endif; ?>
                                    </div>
                                     <?php if (!empty($r["_source"]['fatorimpacto'])) : ?>
                                    <div class="extra">
                                        <a class="ui sub header">Fator de impacto:</a>
                                            <div class="ui label" style="color:black;">
                                                <?php echo $r["_source"]['fatorimpacto'][0]; ?>
                                            </div>                                                
                                    </div>
                                    <?php endif; ?>

                                    
                                    <h3>Como citar</h3>
<?php
$type = get_type($r["_source"]["type"]);
$author_array = array();
foreach ($r["_source"]["authors"] as $autor_citation){

$array_authors = explode(',', $autor_citation);
$author_array[] = '{"family":"'.$array_authors[0].'","given":"'.$array_authors[1].'"}';
};
$authors = implode(",",$author_array);

if (!empty($r["_source"]["ispartof"])) {
$container = '"container-title": "'.$r["_source"]["ispartof"].'",';
} else {
$container = "";
};
if (!empty($r["_source"]["doi"])) {
$doi = '"DOI": "'.$r["_source"]["doi"][0].'",';
} else {
$doi = "";
};

if (!empty($r["_source"]["url"])) {
$url = '"URL": "'.$r["_source"]["url"][0].'",';
} else {
$url = "";
};

if (!empty($r["_source"]["publisher"])) {
$publisher = '"publisher": "'.$r["_source"]["publisher"].'",';
} else {
$publisher = "";
};

if (!empty($r["_source"]["publisher-place"])) {
$publisher_place = '"publisher-place": "'.$r["_source"]["publisher-place"].'",';
} else {
$publisher_place = "";
};

$volume = "";
$issue = "";
$page_ispartof = "";

if (!empty($r["_source"]["ispartof_data"])) {
foreach ($r["_source"]["ispartof_data"] as $ispartof_data) {
if (strpos($ispartof_data, 'v.') !== false) {
$volume = '"volume": "'.str_replace("v.","",$ispartof_data).'",';
} elseif (strpos($ispartof_data, 'n.') !== false) {
$issue = '"issue": "'.str_replace("n.","",$ispartof_data).'",';
} elseif (strpos($ispartof_data, 'p.') !== false) {
$page_ispartof = '"page": "'.str_replace("p.","",$ispartof_data).'",';
}
}
}

$data = json_decode('{
"title": "'.$r["_source"]["title"].'",
"type": "'.$type.'",
'.$container.'
'.$doi.'
'.$url.'
'.$publisher.'
'.$publisher_place.'
'.$volume.'
'.$issue.'
'.$page_ispartof.'
"issued": {
"date-parts": [
[
"'.$r["_source"]["year"].'"
]
]
},
"author": [
'.$authors.'
]
}');
$output = $citeproc->render($data, $mode);
print_r($output)
?>                                
                                </div>                            
                            </div>
                            

                            
                            <?php endforeach;?>
                            
                        <?php
                        echo '<br/><br/>';
                        /* Pagination - Start */
                        echo '<div class="ui buttons">';
                        if ($page > 1) {
                            echo '<form method="post" action="'.$escaped_url.'">';
                            echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
                            echo '<button type="submit" name="page" class="ui labeled icon button active" value="'.$prev.'">
                            <i class="left chevron icon"></i>Anterior</button>';
                            echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
                            if ($page * $limit < $total) {
                                echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                            } else {
                                echo '<button class="ui right labeled icon button disabled">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                            }
                            echo '</form>';
                        } else {
                            if ($page * $limit < $total) {
                                echo '<form method="post" action="'.$escaped_url.'">';
                                echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
                                echo '<button class="ui labeled icon button disabled"><i class="left chevron icon"></i>Anterior</button>';
                                echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
                                echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
                                Próximo
                                <i class="right chevron icon"></i></button>';
                                echo '</form>';
                            }
                        }
                        echo '</div>';
                        echo '<br/><br/>';
                        /* Pagination - End */
                        ?>                            
                            
                        </div>
                    
                    </div>

                    <?php include('inc/footer.php'); ?>
    
                    <script>
                        $('.ui.dropdown')
                            .dropdown()
                        ;
                    </script>
                    <script>
                        $('#date_init').calendar({
                            type: 'year'
                        });
                    </script>
                    <script>
                        $('#date_end').calendar({
                            type: 'year'
                        });
                    </script>
                    <script>
                        $('.ui.checkbox')   
                            .checkbox();
                    </script>
                     <script>
                        $('.ui.accordion')
                          .accordion()
                        ;
                    </script>
        

        </div>
    </body>
</html>
