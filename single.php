<?php
if (session_status() === PHP_SESSION_NONE){
    session_start();
}
    
include('inc/config.php'); 
include('inc/functions.php');

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

/* Montar a consulta */
$cursor = query_one_elastic($_GET['_id'],$client);

/* Contador */
counter($_GET['_id'],$client);

/* Upload de PDF */

if (!empty($_FILES)) {
    if (!is_dir('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'')){
        mkdir('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'', 0700, true);
    }
    
    $uploaddir = 'upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'/';
    $count_files = count(glob('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'/*',GLOB_BRACE));
    $rights = '{"rights":"'.$_POST["rights"].'"},';
    
    if (!empty($_POST["embargo_date"])){
        $embargo_date = '{"embargo_date":"'.$_POST["embargo_date"].'"},';
    } else {
        $embargo_date = '{"embargo_date":""},';
    }
    
    if ($_FILES['upload_file']['type'] == 'application/pdf'){
        $uploadfile = $uploaddir . basename($_GET['_id'] . "_" . ($count_files+1) . ".pdf");
    } else {
        $uploadfile = $uploaddir . basename($_GET['_id'] . "_" . ($count_files+1) . ".pptx");
    }    
    
    if ($_FILES['upload_file']['type'] == 'application/pdf'||$_FILES['upload_file']['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
        //echo '<pre>';
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
            $query = 
            '
            {
                "doc":{
                    "sysno":"'.$_GET['_id'].'",
                    "file_info" :[ 
                        {"num_usp":"'.$_SESSION['oauthuserdata']->{'loginUsuario'}.'"},
                        {"name_file":"'.$_FILES['upload_file']['name'].'"},
                        '.$rights.'
                        '.$embargo_date.'
                        {"file_type":"'.$_FILES['upload_file']['type'].'"}
                    ],
                    "date_file":"'.date("Y-m-d").'"
                },                    
                "doc_as_upsert" : true
            }
            ';
                        
            $params = [
                'index' => 'sibi',
                'type' => 'files',
                'id' => $uploadfile,
                'body' => $query
            ];
            $response_upload = $client->update($params); 
            
            
            $myfile = fopen("$uploadfile.json", "w") or die("Unable to open file!");
            $txt = $query;
            fwrite($myfile, $txt);
            fclose($myfile);
            
            
            
        } else {
            echo "Possível ataque de upload de arquivo!\n";
        }
    }
    
    //echo 'Aqui está mais informações de debug:';
    //print_r($_FILES);
   //print "</pre>";    
    
}

if (!empty($_POST['delete_file'])) {
    unlink($_POST['delete_file']);
    $delete_json = ''.$_POST['delete_file'].'.json';
    unlink($delete_json);
    $params = [
        'index' => 'sibi',
        'type' => 'files',
        'id' => $_POST['delete_file']
    ];
    $response_delete = $client->delete($params);
    
    //print_r($response_delete);
    
}




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

if (!empty($cursor["_source"]['issn'][0])) {
$record[] = "SN  - ".$cursor["_source"]['issn'][0]."";
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
        <?php include('inc/meta-header.php'); ?>
        <title>BDPI USP - Detalhe do registro: <?php echo $cursor["_source"]['title'];?></title>
        <script src="inc/uikit/js/components/slideset.js"></script>
        <script src="inc/uikit/js/components/notify.min.js"></script>
        <script src="inc/uikit/js/components/upload.min.js"></script>
        <script src="inc/uikit/js/components/form-select.min.js"></script>
        <script src="inc/uikit/js/components/datepicker.min.js"></script>
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
        <?php if (!empty($cursor["_source"]['ispartof'])): ?>
        <meta name="citation_journal_title" content="<?php echo $cursor["_source"]['ispartof'];?>">
        <?php endif; ?>
        <?php if (!empty($cursor["_source"]['ispartof_data'][0])): ?>
        <meta name="citation_volume" content="<?php echo $cursor["_source"]['ispartof_data'][0];?>">
        <?php endif; ?>

        <?php if (!empty($cursor["_source"]['ispartof_data'][1])): ?>
        <meta name="citation_issue" content="<?php echo $cursor["_source"]['ispartof_data'][1];?>">
        <?php endif; ?>
        
        <?php 
        
        $files_upload = glob('upload/'.$_GET['_id'].'/*.{pdf,pptx}', GLOB_BRACE);    
        $links_upload = "";
        if (!empty($files_upload)){       
            foreach($files_upload as $file) {        
                echo '<meta name="citation_pdf_url" content="http://'.$_SERVER['SERVER_NAME'].'/'.$file.'">
            ';
            }
        }
        ?>
        <!--
        <meta name="citation_firstpage" content="11761">
        <meta name="citation_lastpage" content="11766">
        <meta name="citation_pdf_url" content="http://www.example.com/content/271/20/11761.full.pdf">
        -->
        <!-- Generate metadata to Google Scholar - END -->
        
        <!-- Generate JSON-LD - START -->
        <?php 
        
        foreach ($cursor["_source"]['authors'] as $autores) {
            $autor_json[] = '"'.$autores.'"';
        }
        
        
        echo '<script type="application/ld+json">';
        echo '
            {
            "@context":"http://schema.org",
            "@graph": [
              {
                "@id": "http://bdpi.usp.br",
                "@type": "Library",
                "name": "Base de Produção Intelectual da USP"
              },
              ';
            
        
            switch ($cursor["_source"]["type"]) {
                case "ARTIGO DE PERIODICO":
                    
                    echo '

    {
        "@id": "#periodical", 
        "@type": [
            "Periodical"
        ], 
        "name": "'.$cursor["_source"]['ispartof'].'", 
        "issn": [
            "'.$cursor["_source"]['issn'][0].'"
        ],  
        "publisher": "'.$cursor["_source"]['publisher'].'"
    },
    {
        "@id": "#volume", 
        "@type": "PublicationVolume", 
        "volumeNumber": "'.str_replace("v. ","",$cursor["_source"]['ispartof_data'][0]).'", 
        "isPartOf": "#periodical"
    },     
    {
        "@id": "#issue", 
        "@type": "PublicationIssue", 
        "issueNumber": "'.str_replace(" n. ","",$cursor["_source"]['ispartof_data'][1]).'", 
        "datePublished": "'.$cursor["_source"]['year'].'", 
        "isPartOf": "#volume"
    }, 
    {
        "@type": "ScholarlyArticle", 
        "isPartOf": "#issue", 
        "description": "'.$cursor["_source"]['resumo'][0].'",
        ';
        if (!empty($cursor["_source"]['doi'])) {            
            echo '"sameAs": "http://dx.doi.org/'.$cursor["_source"]['doi'][0].'",';
        }
        echo '
        "about": [
            "Works", 
            "Catalog"
        ], 
        "pageEnd": "'.str_replace(" p. ","",$cursor["_source"]['ispartof_data'][2]).'", 
        "pageStart": "'.str_replace(" p. ","",$cursor["_source"]['ispartof_data'][2]).'", 
        "name": "'.$cursor["_source"]['title'].'", 
        "author": ['.implode(",",$autor_json).']
    }
                    
                    ';                   
                    
                    break;
                case "PARTE DE MONOGRAFIA/LIVRO":
                    
                    break;
                case "TRABALHO DE EVENTO-RESUMO":
                    
                    break;
                case "TEXTO NA WEB":
                    
                    break;
                }
        
            echo '

            ]
            }
    </script>';
        
        ?>
        <!-- Generate JSON-LD - END -->
        
    </head>
    <body>
        <?php if(!empty($response_upload)) : ?>
            <?php if ($response_upload['result'] == 'created'): ?>
                <script>UIkit.notify("<i class='uk-icon-check'></i> Arquivo incluído com sucesso", {status:'success'})</script>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(!empty($response_delete)) : ?>        
            <?php if ($response_delete['result'] == 'deleted'): ?>
                <script>UIkit.notify("<i class='uk-icon-check'></i> Arquivo excluído com sucesso", {status:'danger'})</script>
            <?php endif; ?> 
        <?php endif; ?>
        <?php include_once("inc/analyticstracking.php") ?>
        <?php include('inc/navbar.php'); ?>

    <div class="uk-container uk-container-center uk-margin-large-bottom">

        <div class="uk-grid uk-margin-top" data-uk-grid-margin>
            
            <?php if (!empty($cursor["_source"]['issn'][0])) : ?>
                <?php $issn_info = get_title_elsevier(str_replace("-","",$cursor["_source"]['issn'][0]),$api_elsevier); ?>
                <?php
                    if (!empty($issn_info)) {
                        //print_r($issn_info);
                        store_issn_info($client,$cursor["_source"]['issn'][0],json_encode($issn_info));
                    }
                    
                ?>
            <?php endif; ?>             
            
            
            <div class="uk-width-medium-1-3">
                <div class="uk-panel uk-panel-box">
                    
                   <?php
                        if (isset($issn_info["serial-metadata-response"])) {
                            $image_url = "{$issn_info["serial-metadata-response"]["entry"][0]["link"][2]["@href"]}&apiKey={$api_elsevier}";
                            
                    $headers = get_headers($image_url, 1);
                    if ($headers[0] == 'HTTP/1.1 200 OK') {
                            if (exif_imagetype($image_url) == IMAGETYPE_GIF) {
                                echo '<div class="uk-margin-top uk-margin-bottom">';    
                                echo '<img src="'.$image_url.'">';
                                echo '</div>';
                            }
                    }                            

                        } 
                    ?>
                    
                    <h3 class="uk-panel-title">Ver registro no DEDALUS</h3>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top uk-margin-bottom" data-uk-nav="{multiple:true}">
                        <hr>
                        <li>                    
                            <button class="uk-button-small uk-button-primary" onclick="window.location.href='http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $cursor["_id"];?>'">Ver no Dedalus</button>
                        </li>
                    </ul>
                    <h3 class="uk-panel-title">Exportar registro bibliográfico</h3>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top uk-margin-bottom" data-uk-nav="{multiple:true}">
                        <hr>                   
                        <li>
                            <button class="uk-button-small uk-button-primary" onclick="SaveAsFile('<?php echo $record_blob; ?>','record.ris','text/plain;charset=utf-8')">RIS (EndNote)</button>
                        </li>
                        <li>
                    <?php if (!empty($cursor["_source"]["files"][0]["visitors"])) : ?>
                    <h4>Visitas ao registro: <?php echo ''.$cursor["_source"]["files"][0]["visitors"].''; ?></h4>
                    <?php endif; ?>                
                        </li>
                    </ul>
                    <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <h3 class="uk-panel-title">Métricas</h3>
                        <hr>
                        <!-- 
                            <object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=< ?php echo $cursor["_source"]['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
                        -->
                    
                    <?php
                        $full_citations = get_citations_elsevier(trim($cursor["_source"]['doi'][0]),$api_elsevier);
                        if (!empty($full_citations["abstract-citations-response"])) {
                            echo '<h4>API SCOPUS</h4>';
                            echo '<a href="https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp='.$full_citations['abstract-citations-response']['identifier-legend']['identifier'][0]['scopus_id'].'&origin=inward">Ver registro na SCOPUS</a>';
                            echo '<h5>Ver perfil dos autores na SCOPUS:</h5>';
                            foreach ($full_citations["abstract-citations-response"]["citeInfoMatrix"]["citeInfoMatrixXML"]["citationMatrix"]["citeInfo"][0]["author"] as $authors_scopus) {
                            //print_r($authors_scopus);
                            echo '<a href="https://www.scopus.com/authid/detail.uri?partnerID=HzOxMe3b&authorId='.$authors_scopus['authid'].'&origin=inward">'.$authors_scopus['index-name'].'</a><br/>';
                            }
                             
                            echo '
                            <table class="uk-table">
                                <caption>Citações na Scopus nos últimos 3 anos</caption>
                                <thead>
                                    <tr>';
                            foreach ($full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["columnHeading"] as $header){
                                echo '<th>'.$header["$"].'</th>';
                            }
                            echo '
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>';
                            foreach ($full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["columnTotal"] as $total){
                                echo '<td>'.$total["$"].'</td>';
                            }
                            echo ' 
                                    </tr>
                                </tbody>
                            </table>                        

                            ';
                            //print_r($full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]);
                            echo 'Total de citações nos últimos 3 anos: '.$full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["rangeColumnTotal"].'<br/>';
                            echo 'Total de citações: '.$full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["grandTotal"].'<br/>';
                        
                    
                            
                            $metrics[] = '"three_years_citations_scopus": '.$full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["rangeColumnTotal"].'';
                            $metrics[] = '"full_citations_scopus": '.$full_citations["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["grandTotal"].'';
                        }
                    ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="uk-width-medium-2-3">
                <ul class="uk-tab" data-uk-tab="{connect:'#single'}">
                    <li class="uk-active"><a href="">Visualização</a></li>
                    <li><a href="">Texto completo</a></li>
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
                                        <a href="result.php?search[]=authors.keyword:&quot;<?php echo $autores;?>&quot;"><?php echo $autores;?></a>
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
                                    <a href="result.php?search[]=authorUSP.keyword:&quot;<?php echo $autoresUSP;?>&quot;"><?php echo $autoresUSP;?></a>
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
                                    <li><a href="result.php?search[]=unidadeUSP.keyword:&quot;<?php echo $unidadeUSP;?>&quot;"><?php echo $unidadeUSP;?></a></li>
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
                                    <li><a href="result.php?assunto=<?php echo $subject;?>"><?php echo $subject;?></a></li>
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
                                        <li><a href="result.php?search[]=language.keyword:&quot;<?php echo $language;?>&quot;"><?php echo $language;?></a></li>
                                   <?php endforeach;?>
                                </ul>                            
                            </li>
                        <?php endif; ?>
                            
                        <!-- Resumo -->
                        <?php if (!empty($cursor["_source"]['resumo'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Resumo:</h4>
                                <ul class="uk-list uk-list-line">
                                   <?php foreach ($cursor["_source"]['resumo'] as $resumo): ?>
                                        <li><?php echo $resumo;?></li>
                                   <?php endforeach;?>
                                </ul>                            
                            </li>
                        <?php endif; ?>                            
                            
                        <!-- Imprenta -->
                        <?php if (!empty($cursor["_source"]['publisher-place'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Imprenta:</h4>
                                <ul class="uk-list uk-list-line">
                                    <li>Local: <a href="result.php?search[]=publisher-place.keyword:&quot;<?php echo $cursor["_source"]['publisher-place'];?>&quot;"><?php echo $cursor["_source"]['publisher-   place'];?></a></li>
                                    <li>Data de publicação: <a href="result.php?search[]=year.keyword:&quot;<?php echo $cursor["_source"]['year'];?>&quot;"><?php echo $cursor["_source"]['year'];?></a></li>
                                </ul>
                            </li>
                            
                        <?php endif; ?>    
                            
                        <!-- Source -->
                        <?php if (!empty($cursor["_source"]['ispartof'])): ?>
                            <li>
                                <h4 class="uk-margin-top">Fonte:</h4>
                                <ul class="uk-list uk-list-line">
                                    <li>Título: <a href="result.php?search[]=ispartof.keyword:&quot;<?php echo $cursor["_source"]['ispartof'];?>&quot;"><?php echo $cursor["_source"]['ispartof'];?></a></li>
                                    <?php if (!empty($cursor["_source"]['issn'])): ?>
                                    <li>ISSN: <a href="result.php?search[]=issn.keyword:&quot;<?php echo $cursor["_source"]['issn'][0];?>&quot;"><?php echo $cursor["_source"]['issn'][0];?></a></li>
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
                        

                        <?php if (isset($issn_info["serial-metadata-response"])): ?>
                            <div class="uk-alert">
                                <li class="uk-h6">
                                    Informações sobre o periódico <a href="<?php print_r($issn_info["serial-metadata-response"]["entry"][0]["link"][1]["@href"]); ?>"><?php print_r($issn_info["serial-metadata-response"]["entry"][0]["dc:title"]); ?></a> (Fonte: Scopus API)
                                    <ul>
                                        <li>
                                            Editor: <?php print_r($issn_info["serial-metadata-response"]["entry"][0]["dc:publisher"]); ?>
                                        </li>
                                    <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["subject-area"] as $subj_area) : ?>
                                        <li> 
                                            Área: <?php 
                                                      print_r($subj_area["$"]);
                                                      $subject_area_array[] = '"'.$subj_area["$"].'"';
                                                  ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php $metrics[] = '"subject_area_scopus":['.implode(",",$subject_area_array).']'; ?>    
                                    <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["SJRList"]["SJR"] as $sjr) : ?>
                                        <li>                                                    
                                            SJR <?php print_r($sjr["@year"]); ?>: <?php print_r($sjr["$"]); ?>
                                            <?php $metrics[] = '"scopus_sjr_'.$sjr["@year"].'": '.$sjr["$"].'';?>
                                        </li>
                                    <?php endforeach; ?>

                                    <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["SNIPList"]["SNIP"] as $snip) : ?>
                                        <li>                                                    
                                            SNIP <?php print_r($snip["@year"]); ?>: <?php print_r($snip["$"]); ?>
                                            <?php $metrics[] = '"scopus_snip_'.$snip["@year"].'": '.$snip["$"].'';?>
                                        </li>
                                    <?php endforeach; ?>
                                    
                                    <?php if (isset($issn_info["serial-metadata-response"]["entry"][0]["IPPList"]["IPP"])): ?>    
                                        <?php foreach ($issn_info["serial-metadata-response"]["entry"][0]["IPPList"]["IPP"] as $ipp) : ?>
                                            <li>                                                    
                                                IPP <?php print_r($ipp["@year"]); ?>: <?php print_r($ipp["$"]); ?>
                                                <?php $metrics[] = '"scopus_ipp_'.$ipp["@year"].'": '.$ipp["$"].'';?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                    <?php 
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['openaccess'])) {
                                            echo '<li>Periódico de acesso aberto</li>';
                                            $metrics[] = '"scopus_openaccess":"'.$issn_info["serial-metadata-response"]["entry"][0]['openaccess'].'"';
                                        }
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['openaccessArticle'])) {
                                            echo '<li>Artigo em Acesso aberto</li>';
                                            $metrics[] = '"scopus_openaccessArticle":"'.$issn_info["serial-metadata-response"]["entry"][0]['openaccessArticle'].'"';
                                        }
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['openArchiveArticle'])) {
                                            echo '<li>Artigo em arquivo de Acesso aberto</li>';
                                            $metrics[] = '"scopus_openArchiveArticle":"'.$issn_info["serial-metadata-response"]["entry"][0]['openArchiveArticle'].'"';
                                        } 
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['openaccessType'])) {
                                            echo '<li>Tipo de acesso aberto: '.$issn_info["serial-metadata-response"]["entry"][0]['openaccessType'].'</li>';
                                            $metrics[] = '"scopus_openaccessType":"'.$issn_info["serial-metadata-response"]["entry"][0]['openaccessType'].'"';
                                        }  
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['openaccessStartDate'])) {
                                            echo '<li>Data de início do acesso aberto: '.$issn_info["serial-metadata-response"]["entry"][0]['openaccessStartDate'].'</li>';
                                            $metrics[] = '"scopus_openaccessStartDate":"'.$issn_info["serial-metadata-response"]["entry"][0]['openaccessStartDate'].'"';
                                        }
                                        if (!empty($issn_info["serial-metadata-response"]["entry"][0]['oaAllowsAuthorPaid'])) {
                                            echo '<li>Acesso aberto pago pelo autor: '.$issn_info["serial-metadata-response"]["entry"][0]['oaAllowsAuthorPaid'].'</li>';
                                            $metrics[] = '"scopus_oaAllowsAuthorPaid":"'.$issn_info["serial-metadata-response"]["entry"][0]['oaAllowsAuthorPaid'].'"';
                                        }                                        
                                    ?>    
                                    </ul>
                                </li>
                          </div>    
                                        
                          <?php flush(); unset($issn_info); endif; ?>       
                        
                        </ul>
                            <?php if (!empty($cursor["_source"]['url'])||!empty($cursor["_source"]['doi'])) : ?>
                            <hr>
                            <div class="uk-button-group" style="padding:15px 15px 15px 0;">     
                                <?php if (!empty($cursor["_source"]['url'])) : ?>
                                    <?php foreach ($cursor["_source"]['url'] as $url) : ?>
                                        <?php if ($url != '') : ?>
                                            <a class="uk-button-small uk-button-primary" href="<?php echo $url;?>" target="_blank">Acesso online à fonte</a>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                <?php endif; ?>
                                <?php if (!empty($cursor["_source"]['doi'])) : ?>
                                    <a class="uk-button-small uk-button-primary" href="http://dx.doi.org/<?php echo $cursor["_source"]['doi'][0];?>" target="_blank">Resolver DOI</a>
                                <?php endif; ?>
                            </div>
                           <?php if (!empty($cursor["_source"]['doi'])) {
                                    $oadoi = get_oadoi($cursor["_source"]['doi'][0]);
                                    echo '<div class="uk-alert uk-h6">Informações sobre o DOI: '.$cursor["_source"]['doi'][0].' (Fonte: <a href="oadoi.org">oaDOI API</a>)';
                                    echo '<ul>';
                                    if ($oadoi['results'][0]['is_subscription_journal'] == 1) {
                                        echo '<li>Este periódico é de assinatura</li>';
                                    } else {
                                        echo '<li>Este periódico é de acesso aberto</li>';
                                    }
                                    if ($oadoi['results'][0]['is_free_to_read'] == 1) {
                                        echo '<li>Este artigo é de acesso aberto</li>';
                                    } else {
                                        echo '<li>Este artigo NÃO é de acesso aberto<br/>';
                                    }
                                    if (!empty($oadoi['results'][0]['is_free_to_read'])) {                                        
                                        $metrics[] = '"oadoi_is_free_to_read": '.$oadoi['results'][0]['is_free_to_read'].'';
                                    }    
                                    if (!empty($oadoi['results'][0]['free_fulltext_url'])) {                                        
                                        echo '<li><a href="'.$oadoi['results'][0]['free_fulltext_url'].'">URL de acesso aberto</a></li>';
                                    }
                                    if (!empty($oadoi['results'][0]['oa_color'])) {  
                                        echo '<li>Cor do Acesso Aberto: '.$oadoi['results'][0]['oa_color'].'</li>';
                                        $metrics[] = '"oadoi_oa_color": "'.$oadoi['results'][0]['oa_color'].'"';
                                    }
                                    if (!empty($oadoi['results'][0]['license'])) {                                        
                                        echo '<li>Licença: '.$oadoi['results'][0]['license'].'</li>';
                                    }
                                    echo '</ul></div>';
                                    
                                    if (!empty($oadoi['results'][0]['is_subscription_journal'])) {
                                        $metrics[] = '"oadoi_is_subscription_journal": '.$oadoi['results'][0]['is_subscription_journal'].'';
                                    }
                                    metrics_update($client,$_GET['_id'],$metrics);      
                                }
                            ?>                            
                            <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['oauthuserdata'])): ?>
                        <div class="uk-alert-warning">
                            <h4 class="uk-margin-top">Upload do texto completo:</h4>
                            <form enctype="multipart/form-data" method="POST" action="single.php?_id=<?php echo $_GET['_id']; ?>" name="upload"> 
                                <div id="upload-drop" class="uk-placeholder uk-text-center">
                                    <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Arrastar arquivos aqui ou <a class="uk-form-file">selecionar arquivo<input id="upload-select" name="upload_file" type="file"></a>.
                                </div>

                                <div id="progressbar" class="uk-progress uk-hidden">
                                    <div class="uk-progress-bar" style="width: 0%;">0%</div>
                                </div>
                                
                                <script>

                                    $(function(){

                                        var progressbar = $("#progressbar"),
                                            bar         = progressbar.find('.uk-progress-bar'),
                                            settings    = {

                                            method: 'POST', // HTTP method, default is 'POST'    

                                            action: 'single.php', // upload url

                                            allow : '*.(pdf|pptx)', // allow only images

                                            loadstart: function() {
                                                bar.css("width", "0%").text("0%");
                                                progressbar.removeClass("uk-hidden");
                                            },

                                            progress: function(percent) {
                                                percent = Math.ceil(percent);
                                                bar.css("width", percent+"%").text(percent+"%");
                                            },

                                            allcomplete: function(response) {

                                                bar.css("width", "100%").text("100%");

                                                setTimeout(function(){
                                                    progressbar.addClass("uk-hidden");
                                                }, 250);

                                                alert("Upload Completo")
                                            }
                                        };

                                        var select = UIkit.uploadSelect($("#upload-select"), settings),
                                            drop   = UIkit.uploadDrop($("#upload-drop"), settings);
                                    });

                                </script>                                
                                                                
                                <!--<div class="uk-form-file">
                                    <button class="uk-button">Selecionar arquivo</button>
                                    <input name="upload_file" data-validation="required" data-validation="mime size" data-validation-allowing="pdf, pptx" data-validation-max-size="100M" type="file">
                                </div> -->
                                <div class="uk-form-select uk-button" data-uk-form-select>
                                    <span>Informe o tipo de acesso <i class="uk-icon-caret-down"></i></span>
                                    <select name="rights" data-validation="required">
                                        <option value="">Informe o tipo de acesso <i class="uk-icon-caret-down"></i></option>
                                        <option value="Acesso aberto">Acesso aberto</option>
                                        <option value="Embargado">Embargado</option>
                                    </select>
                                </div><br/><br/>
                                <span>Caso tenha embargo, informe a data de liberação</span><br/>
                                <input type="date" name="embargo_date" data-uk-datepicker="{months:['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'], weekdays:['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'], format:'YYYYMMDD'}" placeholder="Informe a data de embargo">                                
                                <br/><br/><button class="uk-button">Enviar</button>
                            </form> 
                        </div>    
                        <?php endif; ?>
                        
                        
                        
                        
                        <?php 
                            if(empty($_SESSION['oauthuserdata'])){
                                $_SESSION['oauthuserdata']="";
                            } 
                            $full_links = get_fulltext_file($_GET['_id'],$_SESSION['oauthuserdata']);
                            if (!empty($full_links)){
                                echo '<h4 class="uk-margin-top uk-margin-bottom">Download do texto completo</h4><div class="uk-grid">';
                                        foreach ($full_links as $links) {
                                            print_r($links);
                                        }                                  
                                echo '</div>';
                            }

                        ?>    
                        
                        
                        <hr>                            
                        <?php load_itens_single($cursor["_id"]); ?>                            
  
                            <div class="extra" style="color:black;">
                                <h4>Como citar</h4>
                                <div class="uk-alert uk-alert-danger">A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
                                <ul>
                                    <li class="uk-margin-top">
                                        <p><strong>ABNT</strong></p>
                                        <?php
                                            $data = gera_consulta_citacao($cursor["_source"]);
                                            print_r($citeproc_abnt->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>APA</strong></p>
                                        <?php
                                            $data = gera_consulta_citacao($cursor["_source"]);
                                            print_r($citeproc_apa->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>NLM</strong></p>
                                        <?php
                                            $data = gera_consulta_citacao($cursor["_source"]);
                                            print_r($citeproc_nlm->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>Vancouver</strong></p>
                                        <?php
                                            $data = gera_consulta_citacao($cursor["_source"]);
                                            print_r($citeproc_vancouver->render($data, $mode));
                                        ?>                                    
                                    </li>                                      
                                </ul>
                            </div>                           
                         
                        
                    </li>
                     <li>
                        <div class="uk-overflow-container">
                            
                            <?php
                                if (!empty($cursor["_source"]['doi'])) {
                                    $full_html = get_articlefull_elsevier(trim($cursor["_source"]['doi'][0]),$api_elsevier);
                                    print_r($full_html);                                    
                                } 
                            ?>
                            
                            
                            
                    <!-- <table class="uk-table">
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
                                    < ?php foreach ($cursor["_source"]["record"] as $fields){
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
                            </table> -->
                            </div>
                    </li> 
                </ul>
            </div>
        </div>
        
        
        <hr class="uk-grid-divider">
        
<?php include('inc/footer.php'); ?>    
    </div>
<?php include('inc/offcanvas.php'); ?>

             
        
    </body>
</html>