<?php

include('inc/functions.php');

/* Citation Style - Session - Default: ABNT */

if (empty($_SESSION["citation_style"])) {
    $_SESSION["citation_style"]="abnt";
}
if (isset($_POST["citation_style"])) {
    $_SESSION["citation_style"] = $_POST['citation_style'];
} 

/* Pegar a URL atual */
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
  $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
} else {
  $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?";
}

/* Citeproc-PHP*/
include 'inc/citeproc-php/CiteProc.php';
$csl = file_get_contents('inc/citeproc-php/style/'.$_SESSION["citation_style"].'.csl');
$lang = "br";
$citeproc = new citeproc($csl,$lang);
$mode = "reference";

/* Montar a consulta */

$cursor = query_one_elastic($_GET['_id']);


/* Contador */
counter($_GET['_id']);

?>

<?php


$record = [];

switch ($cursor["_source"]["type"]) {
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

$record[] = "TI  - ".$cursor["_source"]['title']."";

if (!empty($cursor["_source"]['year'])) {
$record[] = "PY  - ".$cursor["_source"]['year']."";
}

foreach ($cursor["_source"]['authors'] as $autores) {
  $record[] = "AU  - ".$autores."";
}

if (!empty($cursor["_source"]['ispartof'])) {
$record[] = "T2  - ".$cursor["_source"]['ispartof']."";
}

if (!empty($cursor["_source"]['issn_part'][0])) {
$record[] = "SN  - ".$cursor["_source"]['issn_part'][0]."";
}

if (!empty($cursor["_source"]["doi"])) {
$record[] = "DO  - ".$cursor["_source"]["doi"][0]."";
}

if (!empty($cursor["_source"]["url"])) {
  $record[] = "UR  - ".$cursor["_source"]["url"][0]."";
}

if (!empty($cursor["_source"]["publisher-place"])) {
  $record[] = "PP  - ".$cursor["_source"]["publisher-place"]."";
}

if (!empty($cursor["_source"]["publisher"])) {
  $record[] = "PB  - ".$cursor["_source"]["publisher"]."";
}

if (!empty($cursor["_source"]["ispartof_data"])) {
  foreach ($cursor["_source"]["ispartof_data"] as $ispartof_data) {
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

$record_blob = implode("\\n", $record);

?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Detalhe do registro: <?php echo $cursor["_source"]['title'];?></title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <!-- <link rel="stylesheet" href="inc/uikit/css/uikit.min.css"> -->
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>        
        <script src="inc/uikit/js/uikit.min.js"></script>
        <script src="inc/uikit/js/components/grid.js"></script>
        <script src="inc/uikit/js/components/slideset.js"></script>
        
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

        <!-- Generate metadata to Google Scholar - START -->
        <meta name="citation_title" content="<?php echo $cursor["_source"]['title'];?>">
        <?php if (!empty($cursor["_source"]['authors'])): ?>
        <?php foreach ($cursor["_source"]['authors'] as $autores): ?>
        <meta name="citation_author" content="<?php echo $autores;?>">
        <?php endforeach;?>
        <?php endif; ?>
        <meta name="citation_publication_date" content="<?php echo $cursor["_source"]['year']; ?>">
        <meta name="citation_journal_title" content="<?php echo $cursor["_source"]['ispartof'];?>">
        <?php if (!empty($cursor["_source"]['ispartof_data'][0])): ?>
        <meta name="citation_volume" content="<?php echo $cursor["_source"]['ispartof_data'][0];?>">
        <?php endif; ?>

        <?php if (!empty($cursor["_source"]['ispartof_data'][1])): ?>
        <meta name="citation_issue" content="<?php echo $cursor["_source"]['ispartof_data'][1];?>">
        <?php endif; ?>

        <!--
        <meta name="citation_firstpage" content="11761">
        <meta name="citation_lastpage" content="11766">
        <meta name="citation_pdf_url" content="http://www.example.com/content/271/20/11761.full.pdf">
        -->
        <!-- Generate metadata to Google Scholar - END -->         
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

            
    
    <div class="uk-container uk-container-center uk-margin-large-bottom">

        <div class="uk-grid uk-margin-top" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                <div class="uk-panel uk-panel-box">
                    <h3 class="uk-panel-title">Ver registro no DEDALUS</h3>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
                        <hr>
                        <li>                    
                            <a href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $cursor["_id"];?>">Ver no Dedalus</a>
                        </li>
                    </ul>
                    <h3 class="uk-panel-title">Exportar</h3>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top" data-uk-nav="{multiple:true}">
                        <hr>                   
                        <li>
                            <button onclick="SaveAsFile('<?php echo $record_blob; ?>','record.ris','text/plain;charset=utf-8')">RIS (EndNote)</button>
                        </li>
                        <li>
                    <?php if (!empty($cursor["_source"]["files"][0]["visitors"])) : ?>
                    <h4>Visitas ao registro: <?php echo ''.$cursor["_source"]["files"][0]["visitors"].''; ?></h4>
                    <?php endif; ?>                
                        </li>
                    </ul>
                    <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <h3 class="uk-panel-title">Métricas alternativas</h3>
                        <hr>
                        <object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $cursor["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
                    <?php endif; ?>
                </div>
            </div>
            <div class="uk-width-medium-2-3">
                <ul class="uk-tab" data-uk-tab="{connect:'#single'}">
                    <li class="uk-active"><a href="">Visualização</a></li>
                    <li><a href="">Registro completo</a></li>
                </ul>
                <ul id="single" class="uk-switcher uk-margin">
                    <li>
                    
                        <h2><?php echo $cursor["_source"]['title'];?> (<?php echo $cursor["_source"]['year']; ?>)</h2>
                        <ul class="uk-list">
                            
                            <!--List authors -->
                        <?php if (!empty($cursor["_source"]['authors'])): ?>
                            <li>
                                <h4>Autor(es):</h4>
                                <ul class="uk-list uk-list-line">
                                    <?php foreach ($cursor["_source"]['authors'] as $autores): ?>
                                    <li>
                                        <a href="result.php?authors[]=<?php echo $autores;?>"><?php echo $autores;?></a>
                                    </li>
                                    <?php endforeach;?>                                
                                </ul>
                            </li>
                        <?php endif; ?>                            


                        <!--Authors USP -->
                        <?php if (!empty($cursor["_source"]['authorUSP'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Autor(es) USP:</h4>
                                <ul class="uk-list uk-list-line">
                                <?php foreach ($cursor["_source"]['authorUSP'] as $autoresUSP): ?>
                                <li>
                                    <a href="result.php?authorUSP[]=<?php echo $autoresUSP;?>"><?php echo $autoresUSP;?></a>
                                </li>
                                <?php endforeach;?>
                                </ul>
                            </li>
                        <?php endif; ?>                           


                        <!--Unidades USP -->
                        <?php if (!empty($cursor["_source"]['unidadeUSP'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Unidades USP:</h4>
                                <ul class="uk-list uk-list-line">
                                    <?php foreach ($cursor["_source"]['unidadeUSP'] as $unidadeUSP): ?>
                                    <li><a href="result.php?unidadeUSP[]=<?php echo $unidadeUSP;?>"><?php echo $unidadeUSP;?></a></li>
                                    <?php endforeach;?>
                                </ul>
                            </li>
                         <?php endif; ?>   

                        <!--Assuntos -->
                        <?php if (!empty($cursor["_source"]['subject'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Assuntos:</h4>
                                <ul class="uk-list uk-list-line">
                                    <?php foreach ($cursor["_source"]['subject'] as $subject): ?>
                                    <li><a href="result.php?subject[]=<?php echo $subject;?>"><?php echo $subject;?></a></li>
                                    <?php endforeach;?>
                                </ul>
                            </li>
                        <?php endif; ?>
                            
                        <!-- Idioma -->
                        <?php if (!empty($cursor["_source"]['language'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Idioma:</h4>
                                <ul class="uk-list uk-list-line">
                                   <?php foreach ($cursor["_source"]['language'] as $language): ?>
                                        <li><a href="result.php?language[]=<?php echo $language;?>"><?php echo $language;?></a></li>
                                   <?php endforeach;?>
                                </ul>                            
                            </li>
                        <?php endif; ?>
                            
                        <!-- Imprenta -->
                        <?php if (!empty($cursor["_source"]['publisher-place'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Imprenta:</h4>
                                <ul class="uk-list uk-list-line">
                                    <li>Local: <a href="result.php?publisher-place=<?php echo $cursor["_source"]['publisher-place'];?>"><?php echo $cursor["_source"]['publisher-   place'];?></a></li>
                                    <li>Data de publicação: <a href="result.php?year=<?php echo $cursor["_source"]['year'];?>"><?php echo $cursor["_source"]['year'];?></a></li>
                                </ul>
                            </li>
                            
                        <?php endif; ?>    
                            
                        <!-- Fonte -->
                        <?php if (!empty($cursor["_source"]['ispartof'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Fonte:</h4>
                                <ul class="uk-list uk-list-line">
                                    <li>Título: <a href="result.php?ispartof=<?php echo $cursor["_source"]['ispartof'];?>"><?php echo $cursor["_source"]['ispartof'];?></a></li>
                                    <?php if (!empty($cursor["_source"]['issn_part'])): ?>
                                    <li>ISSN: <a href="result.php?issn_part=<?php echo $cursor["_source"]['issn_part'][0];?>"><?php echo $cursor["_source"]['issn_part'][0];?></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($cursor["_source"]['ispartof_data'][0])): ?>
                                    <li>Volume: <?php echo $cursor["_source"]['ispartof_data'][0];?><br/></li>
                                    <?php endif; ?>
                                    <?php if (!empty($cursor["_source"]['ispartof_data'][1])): ?>
                                    <li>Número: <?php echo $cursor["_source"]['ispartof_data'][1];?><br/></li>
                                    <?php endif; ?>
                                    <?php if (!empty($cursor["_source"]['ispartof_data'][2])): ?>
                                    <li>Paginação: <?php echo $cursor["_source"]['ispartof_data'][2];?><br/></li>
                                    <?php endif; ?>
                                    <?php if (!empty($cursor["_source"]['doi'])): ?>
                                    <li>DOI: <a href="http://dx.doi.org/<?php echo $cursor["_source"]['doi'][0];?>"><?php echo $cursor["_source"]['doi'][0];?></a></li>
                                    <?php endif; ?>
                                </ul>                            
                            </li>
                        <?php endif; ?>
                        </ul>
                            
                    
                            

                            <hr>
                            <?php if (!empty($cursor["_source"]['doi'])): ?>
                            <a class="uk-button uk-button-primary" href="http://dx.doi.org/<?php echo $cursor["_source"]['doi'][0];?>" target="_blank">Acesso online</a>
                            <?php endif; ?>
                            <hr>
                            <?php load_itens_new($cursor["_id"]); ?>
                            <hr>

                            <h3>Escolha o estilo da Citação:</h3>
                            <div class="ui compact menu">
                                <form method="post" action="http://<?php echo $url; ?>">
                                    <button  type="submit" name="citation_style" class="ui icon button" value="apa">APA</button>
                                </form>
                                <form method="post" action="http://<?php echo $url; ?>">
                                    <button type="submit" name="citation_style" class="ui icon button" value="abnt">ABNT</button>
                                </form>
                                <form method="post" action="http://<?php echo $url; ?>">
                                    <button type="submit" name="citation_style" class="ui icon button" value="nlm">NLM</button>
                                </form>
                                <form method="post" action="http://<?php echo $url; ?>">
                                    <button type="submit" name="citation_style" class="ui icon button" value="vancouver">Vancouver</button>
                                </form>
                            </div>
                            <br/><br/>
                            <div class="extra" style="color:black;">
                                <h4>Como citar (<?php echo strtoupper($_SESSION["citation_style"]); ?>)</h4>
                                <?php
                                $type = get_type($cursor["_source"]["type"]);
                                
                                $author_array = array();
                                
                                if ($type == "thesis") {
                                    $array_authors = explode(',', $cursor["_source"]["authors"][0]);
                                    $authors = '{"family":"'.$array_authors[0].'","given":"'.$array_authors[1].'"}';                                    
                                } else {
                                    foreach ($cursor["_source"]["authors"] as $autor_citation){
                                        $array_authors = explode(',', $autor_citation);
                                        $author_array[] = '{"family":"'.$array_authors[0].'","given":"'.$array_authors[1].'"}';
                                    };
                                    $authors = implode(",",$author_array);                                    
                                }
                                
                                

                                if (!empty($cursor["_source"]["ispartof"])) {
                                    $container = '"container-title": "'.$cursor["_source"]["ispartof"].'",';
                                } else {
                                    $container = "";
                                };
                                if (!empty($cursor["_source"]["doi"])) {
                                    $doi = '"DOI": "'.$cursor["_source"]["doi"][0].'",';
                                } else {
                                    $doi = "";
                                };

                                if (!empty($cursor["_source"]["url"])) {
                                    $url = '"URL": "'.$cursor["_source"]["url"][0].'",';
                                } else {
                                    $url = "";
                                };

                                if (!empty($cursor["_source"]["publisher"])) {
                                    $publisher = '"publisher": "'.$cursor["_source"]["publisher"].'",';
                                } elseif ($type == "thesis") {
                                    $publisher = '"publisher":"Universidade de São Paulo",';
                                } else {
                                    $publisher = "";
                                };
                                if (!empty($cursor["_source"]["tipotese"])) {
                                    $tese = '"tipotese":"'.$cursor["_source"]["tipotese"].'",';                                    
                                }
                                

                                if (!empty($cursor["_source"]["publisher-place"])) {
                                    $publisher_place = '"publisher-place": "'.$cursor["_source"]["publisher-place"].'",';
                                } else {
                                    $publisher_place = "";
                                };

                                $volume = "";
                                $issue = "";
                                $page_ispartof = "";

                                if (!empty($cursor["_source"]["ispartof_data"])) {
                                    foreach ($cursor["_source"]["ispartof_data"] as $ispartof_data) {
                                        if (strpos($ispartof_data, 'v.') !== false) {
                                            $volume = '"volume": "'.str_replace("v.","",$ispartof_data).'",';
                                        } elseif (strpos($ispartof_data, 'n.') !== false) {
                                            $issue = '"issue": "'.str_replace("n.","",$ispartof_data).'",';
                                        } elseif (strpos($ispartof_data, 'p.') !== false) {
                                            $page_ispartof = '"page": "'.str_replace("p.","",$ispartof_data).'",';
                                        }
                                    }
                                } 
                                
                                $accessed = '"accessed": {
                                                "date-parts": [
                                                ["'.date("Y").'","'.date("m").'","'.date("d").'"]
                                                ]
                                                },';

                                $data = json_decode('{
                                "title": "'.$cursor["_source"]["title"].'",
                                "type": "'.$type.'",
                                '.$container.'
                                '.$doi.'
                                '.$url.'
                                '.$accessed.'
                                '.$tese.'
                                '.$publisher.'
                                '.$publisher_place.'
                                '.$volume.'
                                '.$issue.'
                                '.$page_ispartof.'
                                "issued": {
                                    "date-parts": [
                                        [
                                            "'.$cursor["_source"]["year"].'"
                                        ]
                                    ]
                                },
                                "author": [
                                    '.$authors.'
                                ]
                                }');
                                
                                $output = $citeproc->render($data, $mode);
                                print_r($output);
                                ?>
                            </div>                           
                         
                        
                    </li>
                    <li>
                    <table class="uk-table">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Ind. 1</th>
                                        <th>Ind. 2</th>
                                        <th>Subcampo</th>
                                        <th>Conteúdo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cursor["_source"]["record"] as $fields){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[3]).'';
                                            echo '<td>'.htmlentities($fields[4]).'';
                                        echo '</tr>';
                                    if (!empty($fields[5])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[5]).'';
                                            echo '<td>'.htmlentities($fields[6]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[7])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[7]).'';
                                            echo '<td>'.htmlentities($fields[8]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[9])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[9]).'';
                                            echo '<td>'.htmlentities($fields[10]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[11])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[11]).'';
                                            echo '<td>'.htmlentities($fields[12]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[13])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[13]).'';
                                            echo '<td>'.htmlentities($fields[14]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[15])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[15]).'';
                                            echo '<td>'.htmlentities($fields[16]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[17])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[17]).'';
                                            echo '<td>'.htmlentities($fields[18]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[19])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[19]).'';
                                            echo '<td>'.htmlentities($fields[20]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[21])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[21]).'';
                                            echo '<td>'.htmlentities($fields[22]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[23])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[23]).'';
                                            echo '<td>'.htmlentities($fields[24]).'';
                                        echo '</tr>';
                                    }
                                    if (!empty($fields[25])){
                                        echo '<tr>';
                                            echo '<td>'.htmlentities($fields[0]).'';
                                            echo '<td>'.htmlentities($fields[1]).'';
                                            echo '<td>'.htmlentities($fields[2]).'';
                                            echo '<td>'.htmlentities($fields[25]).'';
                                            echo '<td>'.htmlentities($fields[26]).'';
                                        echo '</tr>';
                                    }
                                    };
                                    ?>
                                </tbody>
                            </table>
                    </li>
                </ul>
            </div>
        </div>
        
        
        <hr class="uk-grid-divider">    
        <div id="footer" class="uk-grid" data-uk-grid-margin>
            <p>Sistema Integrado de Bibliotecas da Universidade de São Paulo</p>
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
        
    </div>
    
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