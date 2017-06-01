<?php

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
$cursor = elasticsearch::elastic_get($_GET['_id'],$type,null);


/* Exportador RIS */
$record_blob = exporters::RIS($cursor);

/* Contador */
//counter($_GET['_id'],$client); (Necessário melhorar)

/* Upload de PDF */

if (!empty($_FILES)) {
    paginaSingle::uploader();    
}

if (!empty($_POST['delete_file'])) {
    unlink($_POST['delete_file']);
    $delete_json = ''.$_POST['delete_file'].'.json';
    unlink($delete_json);
//    $params = [
//        'index' => 'sibi',
//        'type' => 'files',
//        'id' => $_POST['delete_file']
//    ];
//    $response_delete = $client->delete($params);
}

?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php include('inc/meta-header.php'); ?>
        <title><?php echo $branch_abrev; ?> - Detalhe do registro: <?php echo $cursor["_source"]['name'];?></title>
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

        <?php paginaSingle::metadataGoogleScholar($cursor["_source"]); ?>

        <?php paginaSingle::jsonLD($cursor["_source"]); ?>

        
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

        <?php
            if (file_exists("inc/analyticstracking.php")){
                include_once("inc/analyticstracking.php");
            }
        ?>

        <?php include('inc/navbar.php'); ?>
        <br/><br/><br/>

    <div class="uk-container uk-margin-large-bottom">

        <div class="uk-grid uk-margin-top" uk-grid>            
            
            <!-- Obtem informações da API da Elsevier -->
            <?php
                if ($use_api_elsevier == true) {
                    if (!empty($cursor["_source"]["isPartOf"]["issn"])) {
                        $issn_info = API::get_title_elsevier(str_replace("-","",$cursor["_source"]["isPartOf"]["issn"]),$api_elsevier);
                        if (!empty($issn_info)) {
                            API::store_issn_info($client,$cursor["_source"]["isPartOf"]["issn"],json_encode($issn_info));
                        }
                    }
                }
            ?>            
            
            
            <div class="uk-width-1-4@m">
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
                    
                    <h5 class="uk-panel-title">Ver registro no DEDALUS</h5>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top uk-margin-bottom" data-uk-nav="{multiple:true}">
                        <hr>
                        <li>
                            <a class="uk-button uk-button-primary" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $cursor["_id"];?>" target="_blank">Ver no Dedalus</a>                    
                        </li>
                    </ul>
                    <h5 class="uk-panel-title">Exportar registro bibliográfico</h5>
                    <ul class="uk-nav uk-nav-side uk-nav-parent-icon uk-margin-top uk-margin-bottom" data-uk-nav="{multiple:true}">
                        <hr>                   
                        <li>
                            <button class="uk-button uk-button-primary" onclick="SaveAsFile('<?php echo $record_blob; ?>','record.ris','text/plain;charset=utf-8')">RIS (EndNote)</button>
                        </li>
                        <li>
                    <?php if (!empty($cursor["_source"]["files"][0]["visitors"])) : ?>
                    <h4>Visitas ao registro: <?php echo ''.$cursor["_source"]["files"][0]["visitors"].''; ?></h4>
                    <?php endif; ?>                
                        </li>
                    </ul>
                    <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <h3 class="uk-panel-title"><?php echo $t->gettext('Métricas'); ?></h3>
                        <hr>
                        <!-- 
                            <object height="50" data="http://api.elsevier.com/content/abstract/citation-count?doi=< ?php echo $cursor["_source"]['doi'];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
                        -->
                    
                    <?php
                        if ($use_api_elsevier == true) {
                            $full_citations = API::get_citations_elsevier(trim($cursor["_source"]['doi']),$api_elsevier);
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
                        } 
                    ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="uk-width-3-4@m">
                
                        <article class="uk-article">
                        <!--Type -->
                        <?php if (!empty($cursor["_source"]['type'])): ?>
                            <p class="uk-article-meta">    
                                <a href="result.php?search[]=type.keyword:&quot;<?php echo $cursor["_source"]['type'];?>&quot;"><?php echo $cursor["_source"]['type'];?></a>
                            </p>    
                        <?php endif; ?>                            
                        <h1 class="uk-article-title uk-margin-remove-top" style="font-size:150%"><a class="uk-link-reset" href=""><?php echo $cursor["_source"]["name"];?><?php if (!empty($cursor["_source"]['datePublished'])) { echo ' ('.$cursor["_source"]['datePublished'].')'; } ?></a></h1>
                            
                        <!--List authors -->
                        <?php if (!empty($cursor["_source"]['author'])): ?>
                            <p class="uk-text-small uk-margin-remove"><?php echo $t->gettext('Autores'); ?>:<ul class="uk-list uk-list-striped uk-article-meta">
                            <?php foreach ($cursor["_source"]['author'] as $authors) {
                                if (!empty($authors["person"]["affiliation"]["name"])) {
                                    echo '<li><a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' - '.$authors["person"]["affiliation"]["name"].'</a></li>';
                                } elseif (!empty($authors["person"]["potentialAction"])) {
                                    echo '<li><a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' - '.$authors["person"]["potentialAction"].'</a></li>';
                                } else {
                                    echo '<li><a href="result.php?search[]=author.person.name.keyword:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a></li>';
                                }                                
                            }   
                            ?>
                            </ul></p>
                        <?php endif; ?>
                            
                        <!--Unidades USP -->
                        <?php if (!empty($cursor["_source"]['unidadeUSP'])): ?>
                            <p class="uk-text-small uk-margin-remove">
                                <?php echo $t->gettext('Unidades USP'); ?>:
                                    <?php foreach ($cursor["_source"]['unidadeUSP'] as $unidadeUSP): ?>
                                    <a href="result.php?search[]=unidadeUSP.keyword:&quot;<?php echo $unidadeUSP;?>&quot;"><?php echo $unidadeUSP;?></a>
                                    <?php endforeach;?>
                            </p>
                         <?php endif; ?>                             
                            
                        <!--Authors USP -->
                        <?php if (!empty($cursor["_source"]['authorUSP'])): ?>
                            <p class="uk-text-small uk-margin-remove">
                                <?php echo $t->gettext('Autores USP'); ?>:
                                <ul class="uk-list uk-list-striped uk-article-meta">
                                <?php foreach ($cursor["_source"]['authorUSP'] as $autoresUSP): ?>
                                <li><a href="result.php?search[]=authorUSP.name.keyword:&quot;<?php echo $autoresUSP["name"]; ?>&quot;"><?php echo $autoresUSP["name"];?> - <?php echo $autoresUSP["unidadeUSP"];?> </a></li>
                                <?php endforeach;?>
                                </ul>
                            </p>
                        <?php endif; ?>                                
                            
                        <!--Assuntos -->
                        <?php if (!empty($cursor["_source"]['about'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            <?php echo $t->gettext('Assuntos'); ?>:                            
                            <?php foreach ($cursor["_source"]['about'] as $subject) : ?>
                                <a href="result.php?search[]=about.keyword:&quot;<?php echo $subject;?>&quot;"><?php echo $subject;?></a>
                            <?php endforeach;?>
                        </p>
                        <?php endif; ?>

                        <!--Assuntos proveniente da BDTD -->
                        <?php if (!empty($cursor["_source"]["USP"]['about_BDTD'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            <?php echo $t->gettext('Assuntos provenientes das teses'); ?>:                            
                            <?php foreach ($cursor["_source"]["USP"]['about_BDTD'] as $subject_BDTD) : ?>
                                <a href="result.php?search[]=USP.about_BDTD.keyword:&quot;<?php echo $subject_BDTD;?>&quot;"><?php echo $subject_BDTD;?></a>
                            <?php endforeach;?>
                        </p>
                        <?php endif; ?>                                                   

                        <!-- Idioma -->
                        <?php if (!empty($cursor["_source"]['language'])): ?>
                            <p class="uk-text-small uk-margin-remove">
                                <?php echo $t->gettext('Idioma'); ?>:
                                   <?php foreach ($cursor["_source"]['language'] as $language): ?>
                                        <a href="result.php?search[]=language.keyword:&quot;<?php echo $language;?>&quot;"><?php echo $language;?></a>
                                   <?php endforeach;?>  
                            </p>
                        <?php endif; ?>
                            
                        <!-- Resumo -->
                        <?php if (!empty($cursor["_source"]['description'])): ?>
                            <p class="uk-text-small uk-margin-remove">
                                <?php echo $t->gettext('Resumo'); ?>:
                                   <?php foreach ($cursor["_source"]['description'] as $resumo): ?>
                                        <?php echo $resumo;?>
                                   <?php endforeach;?>     
                            </p>
                        <?php endif; ?>                            
                            
                        <!-- Imprenta -->
                        <?php if (!empty($cursor["_source"]['publisher'])): ?>
                            <p class="uk-text-small uk-margin-remove">Imprenta:<ul class="uk-list uk-list-striped uk-article-meta">                                
                                <?php if (!empty($cursor["_source"]["publisher"]["organization"]["name"])): ?>
                                    <li><?php echo $t->gettext('Editora'); ?>: <a href="result.php?search[]=publisher.organization.name.keyword:&quot;<?php echo $cursor["_source"]['publisher']["organization"]["name"];?>&quot;"><?php echo $cursor["_source"]['publisher']["organization"]["name"];?></a></li>
                                <?php endif; ?>
                                <?php if (!empty($cursor["_source"]["publisher"]["organization"]["location"])): ?>
                                    <li><?php echo $t->gettext('Local'); ?>: <a href="result.php?search[]=publisher.organization.location.keyword:&quot;<?php echo $cursor["_source"]['publisher']["organization"]["location"];?>&quot;"><?php echo $cursor["_source"]['publisher']["organization"]["location"];?></a></li>
                                <?php endif; ?>
                                <?php if (!empty($cursor["_source"]['datePublished'])): ?>
                                    <li><?php echo $t->gettext('Data de publicação'); ?>: <a href="result.php?search[]=datePublished.keyword:&quot;<?php echo $cursor["_source"]['datePublished'];?>&quot;"><?php echo $cursor["_source"]['datePublished'];?></a></li>
                                <?php endif; ?>
                            </ul></p>
                            
                        <?php endif; ?>

                        <!-- Data de defesa -->
                        <?php if (!empty($cursor["_source"]['dateCreated'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Data de defesa: <?php echo $cursor["_source"]['dateCreated'];?></a>
                        </p>
                        <?php endif; ?>                          

                        <!-- Descrição física -->
                        <?php if (!empty($cursor["_source"]['numberOfPages'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            Descrição física: <?php echo $cursor["_source"]['numberOfPages'];?></a>
                        </p>
                        <?php endif; ?>                          

                        <!-- ISBN -->
                        <?php if (!empty($cursor["_source"]['isbn'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            ISBN: <?php echo $cursor["_source"]['isbn'];?></a>
                        </p>
                        <?php endif; ?>

                        <!-- DOI -->
                        <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <p class="uk-text-small uk-margin-remove">
                            DOI: <a href="https://dx.doi.org/<?php echo $cursor["_source"]['doi'];?>"><?php echo $cursor["_source"]['doi'];?></a>
                        </p>
                        <?php endif; ?>                                                         
                            
                        <!-- Source -->
                        <?php if (!empty($cursor["_source"]['isPartOf'])): ?>
                            <p class="uk-text-small uk-margin-remove">
                                <p class="uk-text-small uk-margin-remove"><?php echo $t->gettext('Fonte'); ?>:<ul class="uk-list uk-list-striped uk-article-meta">
                                    <li>Título do periódico: <a href="result.php?search[]=isPartOf.name.keyword:&quot;<?php echo $cursor["_source"]["isPartOf"]["name"];?>&quot;"><?php echo $cursor["_source"]["isPartOf"]["name"];?></a></li>
                                    <?php if (!empty($cursor["_source"]['isPartOf']['issn'])): ?>
                                    <li>ISSN: <a href="result.php?search[]=issn.keyword:&quot;<?php echo $cursor["_source"]['isPartOf']['issn'];?>&quot;"><?php echo $cursor["_source"]['isPartOf']['issn'];?></a></li>
                                    <?php endif; ?>                                    
                                    <?php if (!empty($cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"])): ?>
                                    <li>Volume/Número/Paginação/Ano: <?php print_r($cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"]);?></li>
                                    <?php endif; ?>
                                </ul></p>
                        <?php endif; ?>

                        <!-- Qualis 2015 -->
                        <?php if (intval($cursor["_source"]["datePublished"]) >= 2014 ): ?>
                            <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"])): ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Qualis 2015 do periódico</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["issn"][0]); ?></p>
                                <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2015"] as $metrics_2015) : ?>
                                    <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2015["area_nota"]); ?></p>
                                <?php endforeach; ?>                                
                                </li>
                            </div>
                            <?php endif; ?>                           
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
                                    <a class="uk-button-small uk-button-primary" href="http://dx.doi.org/<?php echo $cursor["_source"]['doi'];?>" target="_blank">DOI</a>
                                <?php endif; ?>
                            </div>
                           <?php if ($use_api_oadoi == true) {
                                    if (!empty($cursor["_source"]['doi'])) {
                                        $oadoi = API::get_oadoi($cursor["_source"]['doi']);
                                        echo '<div class="uk-alert uk-h6">Informações sobre o DOI: '.$cursor["_source"]['doi'].' (Fonte: <a href="http://oadoi.org">oaDOI API</a>)';
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
                                        API::metrics_update($client,$_GET['_id'],$metrics);      
                                    }
                                }
                            ?>                            
                            <?php endif; ?>
                        
                        <!-- < ?php if(!empty($_SESSION['oauthuserdata'])): ?> -->
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
                        <!-- < ?php endif; ?> -->
                        
                        
                        
                        
                        <?php 
                            if(empty($_SESSION['oauthuserdata'])){
                                $_SESSION['oauthuserdata']="teste";
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
                        <?php
                            if ($dedalus_single == true) {
                                processaResultados::load_itens_aleph($cursor["_id"]);
                            }                             
                        ?>                            
  
                            <div class="uk-text-small" style="color:black;">
                                <h5><?php echo $t->gettext('Como citar'); ?></h5>
                                <div class="uk-alert uk-alert-danger">A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
                                <p class="uk-text-small uk-margin-remove">
                                <ul>
                                    <li class="uk-margin-top">
                                        <p><strong>ABNT</strong></p>
                                        <?php
                                            $data = citation::citation_query($cursor["_source"]);
                                            print_r($citeproc_abnt->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>APA</strong></p>
                                        <?php
                                            $data = citation::citation_query($cursor["_source"]);
                                            print_r($citeproc_apa->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>NLM</strong></p>
                                        <?php
                                            $data = citation::citation_query($cursor["_source"]);
                                            print_r($citeproc_nlm->render($data, $mode));
                                        ?>                                    
                                    </li>
                                    <li class="uk-margin-top">
                                        <p><strong>Vancouver</strong></p>
                                        <?php
                                            $data = citation::citation_query($cursor["_source"]);
                                            print_r($citeproc_vancouver->render($data, $mode));
                                        ?>                                    
                                    </li>                                      
                                </ul>
                                </p>
                            </div>                           
                         
        </div>
            </div>
        </div>
        
        
        <hr class="uk-grid-divider">
        
<?php include('inc/footer.php'); ?>    
    </div>
<?php include('inc/offcanvas.php'); ?>

             
        
    </body>
</html>