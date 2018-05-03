<?php
/**
 * Item page
 */

require 'inc/config.php';
require 'inc/functions.php';

/* Citeproc-PHP*/
require 'inc/citeproc-php/CiteProc.php';
$csl_abnt = file_get_contents('inc/citeproc-php/style/abnt.csl');
$csl_apa = file_get_contents('inc/citeproc-php/style/apa.csl');
$csl_nlm = file_get_contents('inc/citeproc-php/style/nlm.csl');
$csl_vancouver = file_get_contents('inc/citeproc-php/style/vancouver.csl');
$lang = "br";
$citeproc_abnt = new citeproc($csl_abnt, $lang);
$citeproc_apa = new citeproc($csl_apa, $lang);
$citeproc_nlm = new citeproc($csl_nlm, $lang);
$citeproc_vancouver = new citeproc($csl_nlm, $lang);
$mode = "reference";

/* Montar a consulta */
$cursor = elasticsearch::elastic_get($_GET['_id'], $type, null);

?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php require 'inc/meta-header.php'; ?>
        <title><?php echo $branch_abrev; ?> - Detalhe do registro: <?php echo $cursor["_source"]['name'];?></title>
        
        <?php
        /* DSpace */ 
        if (isset($dspaceRest)) { 

            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $cookies = DSpaceREST::loginREST();

            /* Search for existing record on DSpace */
            $itemID = DSpaceREST::searchItemDSpace($cursor["_id"], $cookies);
            
            if (!empty($itemID)) {

                $uploadForm = '<form class="uk-form" action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <fieldset data-uk-margin>
                        <legend>Enviar um arquivo</legend>
                        <input type="file" name="file">
                        <button class="uk-button uk-button-primary" name="btn_submit">Upload</button>                                    
                    </fieldset>
                    </form>';
            
                if (isset($_FILES['file'])) {
                    $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemID, $_FILES, $cookies);
                }
                $bitstreamsDSpace = DSpaceREST::getBitstreamDSpace($itemID, $cookies);

            } else {

                $createForm  = '<form action="' . $actual_link . '" method="post">
                        <input type="hidden" name="createRecord" value="true" />
                        <button class="uk-button uk-button-danger" name="btn_submit">Criar registro no DSpace</button>
                        </form>';                
                
                if (isset($_POST["createRecord"])) {
                    if ($_POST["createRecord"] == "true") {
                        $collectionDSpace = "4269b9a7-e2ad-4c01-a49e-b444bc96f951";
                        $dataString = DSpaceREST::buildDC($cursor,$_GET['_id']);
                        $resultCreateItemDSpace = DSpaceREST::createItemDSpace($dataString,$collectionDSpace,$cookies);
                        
                        echo "<script type='text/javascript'>
                        $(document).ready(function(){  
                                //Reload the page
                                window.location = window.location.href;
                        });
                        </script>";
                    } 
                }

            }
            DSpaceREST::logoutREST($cookies);
        }
        ?>

        <?php PageSingle::metadataGoogleScholar($cursor["_source"]); ?>
        <?php 
        if($cursor["_source"]["type"] == "ARTIGO DE PERIODICO") {
                PageSingle::jsonLD($cursor["_source"]);
        } 
        ?>
        <!-- Altmetric Script -->
        <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>        
        <!-- PlumX Script -->
        <script type="text/javascript" src="//d39af2mgp1pqhg.cloudfront.net/widget-popup.js"></script>        
    </head>
    <body>
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        require 'inc/navbar.php';
        ?>
        <br/><br/><br/>

        <div class="uk-container uk-margin-large-bottom">
            <div class="uk-grid uk-margin-top" uk-grid>
                <div class="uk-width-1-4@m">
                    <div class="uk-card uk-card-body">                                     
                        <h5 class="uk-panel-title">Ver registro no DEDALUS</h5>
                        <ul class="uk-nav uk-margin-top uk-margin-bottom">
                            <hr>
                            <li>
                                <a class="uk-button uk-button-primary" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $cursor["_id"];?>" target="_blank">Ver no Dedalus</a>                    
                            </li>
                        </ul>
                        <h5 class="uk-panel-title">Exportar registro bibliográfico</h5>
                        <ul class="uk-nav uk-margin-top uk-margin-bottom">
                            <hr>                   
                            <li>
                                <a class="uk-button uk-button-primary" href="http://bdpi.usp.br/tools/export.php?search[]=sysno.keyword%3A<?php echo $cursor["_id"];?>&format=ris" >RIS (EndNote)</a>
                            </li>
                        </ul>

                        <!-- Métricas - Início -->
                        <?php if (!empty($cursor["_source"]['doi'])): ?>
                        <h3 class="uk-panel-title"><?php echo $t->gettext('Métricas'); ?></h3>                        
                        <hr>                        
                        <?php if ($show_metrics == true) : ?>
                            <?php if (!empty($cursor["_source"]['doi'])) : ?>
                            <div class="uk-alert-warning" uk-alert>
                                <p><?php echo $t->gettext('Métricas'); ?>:</p>
                                <div uk-grid>
                                    <div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $cursor["_source"]['doi'];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                    <div><a href="https://plu.mx/plum/a/?doi=<?php echo $cursor["_source"]['doi'];?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true"></a></div>
                                    <div><object data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $cursor["_source"]['doi'];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=image/jpeg"></object></div>
                                    <div><span class="__dimensions_badge_embed__" data-doi="<?php echo $cursor["_source"]['doi'];?>" data-hide-zero-citations="true" data-style="small_rectangle"></span></div>
                                    <?php if(!empty($cursor["_source"]["USP"]["opencitation"]["num_citations"])) :?>
                                        <div>Citações no OpenCitations: <?php echo $cursor["_source"]["USP"]["opencitation"]["num_citations"]; ?></div>
                                    <?php endif; ?>
                                    <?php if(isset($cursor["_source"]["USP"]["aminer"]["num_citation"])) :?>
                                        <div>Citações no AMiner: <?php echo $cursor["_source"]["USP"]["aminer"]["num_citation"]; ?></div>
                                    <?php endif; ?>                                                            
                                    <div>
                                        <!--
                                        < ?php 
                                            $citations_scopus = get_citations_elsevier($cursor["_source"]['doi'][0],$api_elsevier);
                                            if (!empty($citations_scopus['abstract-citations-response'])) {
                                                echo '<a href="https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp='.$citations_scopus['abstract-citations-response']['identifier-legend']['identifier'][0]['scopus_id'].'&origin=inward">Citações na SCOPUS: '.$citations_scopus['abstract-citations-response']['citeInfoMatrix']['citeInfoMatrixXML']['citationMatrix']['citeInfo'][0]['rowTotal'].'</a>';
                                                echo '<br/><br/>';
                                            } 
                                        ? >
                                        -->                                                
                                    </div>
                                </div>
                            </div>
                            <?php else : ?>
                                <?php if(isset($cursor["_source"]["USP"]["aminer"]["num_citation"])) :?>
                                    <?php if($cursor["_source"]["USP"]["aminer"]["num_citation"] > 0) :?>
                                    <div class="uk-alert-warning" uk-alert>
                                        <p><?php echo $t->gettext('Métricas'); ?>:</p>
                                        <div uk-grid>                                                    
                                            <div>Citações no AMiner: <?php echo $cursor["_source"]["USP"]["aminer"]["num_citation"]; ?></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>                                                      
                                <?php endif; ?>                                                                                                            

                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!-- Métricas - Fim -->   
                    </div>
                </div>
                <div class="uk-width-3-4@m">
                    <article class="uk-article">
                        <?php 
                        $record = new Record($cursor, $show_metrics);
                        $record->completeRecordMetadata($t,$url_base);
                        ?>                              
                     
                        <?php 
                        if (!empty($cursor["_source"]['url'])||!empty($cursor["_source"]['doi'])) {
                            if ($use_api_oadoi == true) {
                                if (!empty($cursor["_source"]['doi'])) {
                                    $oadoi = metrics::get_oadoi($cursor["_source"]['doi']);
                                    echo '<div class="uk-alert-primary uk-h6 uk-padding-small">Informações sobre o DOI: '.$cursor["_source"]['doi'].' (Fonte: <a href="http://oadoi.org">oaDOI API</a>)';
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
                                    //API::metrics_update($_GET['_id'], $metrics);      
                                }
                            }
                        }
                        ?>                            

                        <!-- Opencitation - Início -->
                        <?php 
                        if (!empty($cursor["_source"]["USP"]["opencitation"]["citation"])) {
                            echo '<div class="uk-alert-primary uk-h6">';
                            echo "<p>Citações recebidas (Fonte: OpenCitation)</p>";
                            echo '<ul class="uk-list uk-list-bullet">'; 
                            foreach ($cursor["_source"]["USP"]["opencitation"]["citation"] as $opencitation) {
                                echo '<li><a href="'.$opencitation["citing"].'">'.$opencitation["title"].'</a></li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                        } 
                        ?>
                        <!-- Opencitation - Fim -->


                        <!-- Qualis - Início -->
                        <?php if (intval($cursor["_source"]["datePublished"]) >= 2010 ): ?>
                            <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"])): ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Qualis do periódico</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["issn"][0]); ?></p>

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2012"])): ?>
                                        <p>Qualis 2010-2012</p>
                                        <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2012"] as $metrics_2012) : ?>
                                            <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2012["area_nota"]); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>  

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2015"])): ?>
                                        <p>Qualis 2015</p>
                                        <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2015"] as $metrics_2015) : ?>
                                            <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2015["area_nota"]); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2016"])): ?>
                                        <p>Qualis 2013-2016</p>
                                        <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2016"] as $metrics_2016) : ?>
                                            <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2016["area_nota"]); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?> 

                                </li>
                            </div>
                            <?php endif; ?>                           
                        <?php endif; ?>
                        <!-- Qualis  - Fim -->
                            
                        <!-- JCR - Início -->
                        <?php if (!empty($cursor["_source"]["USP"]["JCR"])): ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o JCR</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["JCR"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["JCR"]["issn"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Journal Impact Factor - 2016: <?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2016"][0]["Journal_Impact_Factor"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Impact Factor without Journal Self Cites - 2016: <?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2016"][0]["IF_without_Journal_Self_Cites"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Eigenfactor Score - 2016: <?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2016"][0]["Eigenfactor_Score"]); ?></p>                               
                                    <p class="uk-text-small uk-margin-remove">JCR Rank - 2016: <?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2016"][0]["JCR_Rank"]); ?></p> 
                                </li>
                            </div>
                        <?php endif; ?>  
                        <!-- JCR - Fim --> 

                        <!-- Citescore - Início -->
                        <?php if (!empty($cursor["_source"]["USP"]["citescore"])): ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Citescore</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["citescore"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["citescore"]["issn"][0]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Citescore - 2016: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2016"][0]["citescore"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SJR - 2016: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2016"][0]["SJR"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SNIP - 2016: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2016"][0]["SNIP"]); ?></p>                               
                                    <p class="uk-text-small uk-margin-remove">Open Access: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2016"][0]["open_access"]); ?></p> 
                                </li>
                            </div>
                        <?php endif; ?>  
                        <!-- Citescore - Fim -->                        
                        
                        <hr>

                        <!-- Query itens on Aleph - Start -->                            
                        <?php
                        if (!empty($cursor["_source"]["itens"])) {
                            echo '<div id="exemplares'.$cursor["_id"].'">';
                            echo "<table class=\"uk-table uk-table-small uk-text-small uk-table-striped\">";
                            echo "<caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th><small>Biblioteca</small></th>";
                            echo "<th><small>Cód. de barras</small></th>";
                            echo "<th><small>Status</small></th>";
                            echo "<th><small>Núm. de chamada</small></th>";
                            echo "<th><small>Disponibilidade</small></th>";
                            echo "</tr>";  
                            echo "</thead>";
                            echo "<tbody>";                               

                            foreach ($cursor["_source"]["itens"] as $item) {
                                echo '<tr>';
                                echo '<td><small>'.$item["sub-library"].'</small></td>';
                                echo '<td><small>'.$item["barcode"].'</small></td>';
                                echo '<td><small>'.$item["item-status"].'</small></td>';
                                echo '<td><small>'.$item["call-no-1"].'</small></td>';
                                echo '<td><small>'.$item["loan-status"].'</small></td>';
                                echo '</tr>';
                            }

                            echo "</tbody></table></div>";
                            
                        } else {
                            if ($dedalus_single == true) {
                                Results::load_itens_aleph($cursor["_id"]);
                            }     
                        }                        
                        ?>
                        <!-- Query itens on Aleph - End -->

                        <!-- Query bitstreams on Dspace - Start -->   
                        <?php

                        if(!empty($_SESSION['oauthuserdata'])){
                            if (!empty($uploadForm)) {
                                echo '<div class="uk-alert-danger" uk-alert>';
                                echo '<a class="uk-alert-close" uk-close></a>';
                                echo '<h5>Gestão do documento digital</h5>';
                                echo $uploadForm;
                                echo '</div>';
                            }
    
                            if (!empty($createForm)) {
                                echo '<div class="uk-alert-danger" uk-alert>';
                                echo '<a class="uk-alert-close" uk-close></a>';
                                echo '<h5>Gestão do documento digital</h5>';
                                echo $createForm;
                                echo '</div>';
                            }
                        }                      

                        if (!empty($bitstreamsDSpace)) {
                            echo '<div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <h5>Download do texto completo</h5>
                            <div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>';
                                foreach ($bitstreamsDSpace as $bitstreamDSpace) {
                                    //print_r($bitstreamDSpace);
                                    echo '<div>
                                            <div class="uk-card uk-card-default uk-card-body">
                                                <b>'.$bitstreamDSpace["name"].'</b>
                                                <p><a href="'.$dspaceRest.''.$bitstreamDSpace["retrieveLink"].'" target="_blank"><img src="'.$url_base.'/inc/images/pdf.png"  height="70" width="70"></img></a></p>
                                            </div>
                                        </div>';
                                }
                            echo '</div></div>';
                        } 
                        ?>
                        <!-- Query bitstreams on Dspace - End -->                               
                            
                        <!-- Citation - Start -->
                        <div class="uk-text-small" style="color:black;">
                            <h5><?php echo $t->gettext('Como citar'); ?></h5>
                            <div class="uk-alert-danger">A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>
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
                        <!-- Citation - End -->                           
                            
                </div>
            </div>
            <hr class="uk-grid-divider">        
            <?php require 'inc/footer.php'; ?> 
        </div>
  

        <?php require 'inc/offcanvas.php'; ?>
        <?php ob_flush(); flush(); ?>
        <script async src="https://badge.dimensions.ai/badge.js" charset="utf-8"></script>   
        
    </body>
</html>