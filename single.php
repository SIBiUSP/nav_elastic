<?php
if (session_status() === PHP_SESSION_NONE){
    session_start();
}
    
include('inc/config.php'); 
include('inc/functions.php');
include('inc/functions_result.php');

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
$cursor = elasticsearch::elastic_get($_GET['_id'],$type,null);

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
                'parent' => $_GET['_id'],
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
        <title><?php echo $branch_abrev; ?> - Detalhe do registro: <?php echo $cursor["_source"]['title'];?></title>
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
                <script>UIkit.notify("<span uk-icon="icon: check"></span> Arquivo incluído com sucesso", {status:'success'})</script>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(!empty($response_delete)) : ?>        
            <?php if ($response_delete['result'] == 'deleted'): ?>
                <script>UIkit.notify("<span uk-icon="icon: check"></span> Arquivo excluído com sucesso", {status:'danger'})</script>
            <?php endif; ?> 
        <?php endif; ?>
        <!-- < ?php include_once("inc/analyticstracking.php") ?> -->

        <?php include('inc/navbar.php'); ?>
        <br/><br/><br/>

    <div class="uk-container uk-margin-large-bottom">

        <div class="uk-grid uk-margin-top" uk-grid>            
            
            <div class="uk-width-1-3@m">
                <div class="uk-panel uk-panel-box">
                    
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
                    </ul>
                </div>
            </div>
            <div class="uk-width-2-3@m">
                
                <article class="uk-article">
                    <!--Type -->
                    <?php if (!empty($cursor["_source"]['type'])): ?>
                        <p class="uk-article-meta">    
                            <a href="result.php?search[]=type.keyword:&quot;<?php echo $cursor["_source"]['type'];?>&quot;"><?php echo $cursor["_source"]['type'];?></a>
                        </p>    
                    <?php endif; ?>                            
                    <h1 class="uk-article-title uk-margin-remove-top"><a class="uk-link-reset" href=""><?php echo $cursor["_source"]["title"];?><?php if (!empty($cursor["_source"]['year'])) { echo ' ('.$cursor["_source"]['year'].')'; } ?></a></h1>
                    <h4 class="uk-article-title uk-margin-remove-top"><a class="uk-link-reset" href=""><?php echo $cursor["_source"]["title_original"];?></a></h4>
                            
                     <!--List authors -->
                    <?php if (!empty($cursor["_source"]['authors'])): ?>
                        <p class="uk-article-meta">
                        <?php foreach ($cursor["_source"]['authors'] as $autores) {
                            $authors_array[]='<a href="result.php?search[]=authors.keyword:&quot;'.$autores.'&quot;">'.$autores.'</a>';
                        } 
                        $array_aut = implode("; ",$authors_array);
                        unset($authors_array);
                        print_r($array_aut);
                        ?>
                        </p>
                    <?php endif; ?>
                            
                    <!--Assuntos -->
                    <?php if (!empty($cursor["_source"]['subject'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Assuntos:                            
                            <?php foreach ($cursor["_source"]['subject'] as $assunto) : ?>
                                <a href="result.php?search[]=subject.keyword:&quot;<?php echo $assunto;?>&quot;"><?php echo $assunto;?></a>
                            <?php endforeach;?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($cursor["_source"]['genero_e_forma'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Genero e forma:                            
                            <?php foreach ($cursor["_source"]['genero_e_forma'] as $genero) : ?>
                                <a href="result.php?search[]=genero_e_forma.keyword:&quot;<?php echo $genero;?>&quot;"><?php echo $genero;?></a>
                            <?php endforeach;?>
                        </p>
                    <?php endif; ?>                     

                    <!-- Idioma -->
                    <?php if (!empty($cursor["_source"]['language'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Idioma:
                               <?php foreach ($cursor["_source"]['language'] as $language): ?>
                                    <a href="result.php?search[]=language.keyword:&quot;<?php echo $language;?>&quot;"><?php echo $language;?></a>
                               <?php endforeach;?>  
                        </p>
                    <?php endif; ?>
                            
                    <!-- Resumo -->
                    <?php if (!empty($cursor["_source"]['resumo'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Resumo:
                               <?php foreach ($cursor["_source"]['resumo'] as $resumo): ?>
                                    <?php echo $resumo;?>
                               <?php endforeach;?>     
                        </p>
                    <?php endif; ?>                            
                            
                    <!-- Imprenta -->
                    <?php if (!empty($cursor["_source"]['publisher-place'])): ?>
                        <p class="uk-text-small uk-margin-remove">Imprenta:</p>
                        <p>Local: <a href="result.php?search[]=publisher-place.keyword:&quot;<?php echo $cursor["_source"]['publisher-place'];?>&quot;"><?php echo $cursor["_source"]['publisher-place']; ?></a></p>
                        <p>Data de publicação: <a href="result.php?search[]=year.keyword:&quot;<?php echo $cursor["_source"]['year'];?>&quot;"><?php echo $cursor["_source"]['year']; ?></a></p>                         
                    <?php endif; ?>    
                            
                    <!-- Source -->
                    <?php if (!empty($cursor["_source"]['ispartof'])): ?>
                        <p class="uk-text-small uk-margin-remove">Fonte:</p>
                        <p class="uk-text-small uk-margin-remove">Título: <a href="result.php?search[]=ispartof.keyword:&quot;<?php echo $cursor["_source"]['ispartof'];?>&quot;"><?php echo $cursor["_source"]['ispartof'];?></a></p>
                        <?php if (!empty($cursor["_source"]['issn'])): ?>
                        <p class="uk-text-small uk-margin-remove">ISSN: <a href="result.php?search[]=issn.keyword:&quot;<?php echo $cursor["_source"]['issn'][0];?>&quot;"><?php echo $cursor["_source"]['issn'][0];?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($cursor["_source"]['ispartof_data'][0])): ?>
                        <p class="uk-text-small uk-margin-remove">Volume: <?php echo $cursor["_source"]['ispartof_data'][0];?><br/></p>
                        <?php endif; ?>
                        <?php if (!empty($cursor["_source"]['ispartof_data'][1])): ?>
                        <p class="uk-text-small uk-margin-remove">Número: <?php echo $cursor["_source"]['ispartof_data'][1];?><br/></p>
                        <?php endif; ?>
                        <?php if (!empty($cursor["_source"]['ispartof_data'][2])): ?>
                        <p class="uk-text-small uk-margin-remove">Paginação: <?php echo $cursor["_source"]['ispartof_data'][2];?><br/></p>
                        <?php endif; ?>
                        <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <p class="uk-text-small uk-margin-remove">DOI: <a href="http://dx.doi.org/<?php echo $cursor["_source"]['doi'][0];?>"><?php echo $cursor["_source"]['doi'][0];?></a></p>
                        <?php endif; ?>
                    <?php endif; ?>

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
                            </div>
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
                                    <span>Informe o tipo de acesso <span uk-icon="icon: caret-down"></span></span>
                                    <select name="rights" data-validation="required">
                                        <option value="">Informe o tipo de acesso <span uk-icon="icon: caret-down"></span></option>
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
                            $full_links = processaResultados::get_fulltext_file($_GET['_id'],$_SESSION['oauthuserdata']);
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
                                <p class="uk-text-small uk-margin-remove">
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
                                </p>
                            </div>                           
                         
        </div>
                     <li>
                        <div class="uk-overflow-container">
                            
 
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