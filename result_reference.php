<!DOCTYPE html>
<?php 

include('inc/functions.php');
        
/* Pegar a URL atual */
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
      $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'';
} else {
      $url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'?';
}
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

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

    
} else {
    $search_term = '"match_all": {}';
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
}

$query_complete = '

{
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
  }

';


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


$cursor = query_elastic($query_complete);
$total = $cursor["hits"]["total"];



?>

<html>
    <head>
        <title>BDPI USP - Biblioteca Digital da Produção Intelectual da Universidade de São Paulo</title>
        <?php include('inc/meta-header.php'); ?>
        
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
                                            <?php foreach ($_GET as $key => $value) : ?>
                                            <div class="field">
                                                <div class="ui checkbox">
                                                    <input type="checkbox" checked="checked"  name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                                                    <label><?php echo $value; ?></label>
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
                            gerar_faceta($query_aggregate,$escaped_url,type,30,"Tipo de material");
                            gerar_faceta($query_aggregate,$escaped_url,unidadeUSPtrabalhos,30,"Unidade USP");
                            gerar_faceta($query_aggregate,$escaped_url,departamentotrabalhos,30,"Departamento");
                            gerar_faceta($query_aggregate,$escaped_url,authors,30,"Autores");
                            gerar_faceta($query_aggregate,$escaped_url,year,30,"Ano de publicação","desc");
                            gerar_faceta($query_aggregate,$escaped_url,subject,30,"Assuntos");
                            gerar_faceta($query_aggregate,$escaped_url,language,30,"Idioma");
                            gerar_faceta($query_aggregate,$escaped_url,ispartof,30,"Obra da qual a produção faz parte");
                            gerar_faceta($query_aggregate,$escaped_url,evento,30,"Nome do evento");
                            gerar_faceta($query_aggregate,$escaped_url,country,30,"País de publicação");
                        ?>
                        </div>
                        <h3>Informações administrativas</h3>
                        <div class="ui fluid vertical accordion menu">   
                        <?php 
                            gerar_faceta($query_aggregate,$escaped_url,authorUSP,30,"Autores USP");
                            gerar_faceta($query_aggregate,$escaped_url,codpesbusca,30,"Número USP");
                            gerar_faceta($query_aggregate,$escaped_url,codpes,30,"Número USP / Unidade");
                            gerar_faceta($query_aggregate,$escaped_url,internacionalizacao,30,"Internacionalização");
                            gerar_faceta($query_aggregate,$escaped_url,fomento,30,"Agência de fomento");
                            gerar_faceta($query_aggregate,$escaped_url,indexado,30,"Indexado em");
                            gerar_faceta($query_aggregate,$escaped_url,issn_part,30,"ISSN");
                            gerar_faceta($query_aggregate,$escaped_url,areaconcentracao,30,"Área de concentração");
                            gerar_faceta($query_aggregate,$escaped_url,fatorimpacto,30,"Fator de impacto");
                            gerar_faceta($query_aggregate,$escaped_url,grupopesquisa,30,"Grupo de pesquisa");
                            gerar_faceta($query_aggregate,$escaped_url,colab,30,"País dos autores externos à USP");
                            gerar_faceta($query_aggregate,$escaped_url,colab_int_trab,30,"Colaboração - Internacionalização");
                            gerar_faceta($query_aggregate,$escaped_url,colab_instituicao_trab,30,"Colaboração - Instituição");
                            gerar_faceta($query_aggregate,$escaped_url,colab_instituicao_corrigido,30,"Colaboração - Instituição - Corrigido");
                            gerar_faceta($query_aggregate,$escaped_url,colab_instituicao_naocorrigido,30,"Colaboração - Instituição - Não corrigido");
                            gerar_faceta($query_aggregate,$escaped_url,dataregistroinicial,30,"Data de registro","desc");
                            gerar_faceta($query_aggregate,$escaped_url,dataregistro,30,"Data de registro e alterações","desc");
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
                        
                        <div class="ui divided items">
                            <?php foreach ($cursor["hits"]["hits"] as $r) : ?>
                            <div class="item">
                                <div class="image">
                                    <h4 class="ui center aligned icon header">
                                        <a class="ui blue label" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $r['_id'];?>">
                                            Ver no Dedalus
                                        </a>
                                    </h4>
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
                                                <a href="result.php?authors=<?php echo $autores;?>"><?php echo $autores;?></a>
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
        

        </div>
    </body>
</html>
