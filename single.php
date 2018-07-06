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

            /* Login in DSpace */
            $cookies = DSpaceREST::loginREST();

            /* Search for existing record on DSpace */
            $itemID = DSpaceREST::searchItemDSpace($cursor["_id"], $cookies);
            
            /* Verify if item exists on DSpace */
            if (!empty($itemID)) {

                function removeElementWithValue($array, $key, $value) 
                {
                    foreach ($array as $subKey => $subArray) {
                        if ($subArray[$key] == $value) {
                            unset($array[$subKey]);
                        }
                    }
                    return $array;
                }
                  
                if (isset($_SESSION['oauthuserdata'])) {
                    $uploadForm = '<form class="uk-form" action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <fieldset data-uk-margin>
                            <legend>Enviar um arquivo</legend>
                            <input type="file" name="file">
                            <input type="text" name="codpes" value="'.$_SESSION['oauthuserdata']->{'loginUsuario'}.'" hidden>
                            <button class="uk-button uk-button-primary" name="btn_submit">Upload</button>                                    
                        </fieldset>
                        </form>';
                }
            
                if (isset($_FILES['file'])) {
                    $userBitstream = $_POST["codpes"];
                    $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemID, $_FILES, $userBitstream, $cookies);
                    if (isset($cursor["_source"]["USP"]["fullTextFiles"])) {
                        $body["doc"]["USP"]["fullTextFiles"] = $cursor["_source"]["USP"]["fullTextFiles"];
                    }
                    $body["doc"]["USP"]["fullTextFiles"][] =  $resultAddBitstream;
                    //$body["doc"]["USP"]["fullTextFiles"]["count"] = count($body["doc"]["USP"]["fullTextFiles"]);
                    $resultUpdateFilesElastic = elasticsearch::elastic_update($_GET['_id'], $type, $body);
                    echo "<script type='text/javascript'>
                    $(document).ready(function(){  
                            //Reload the page
                            window.location = window.location.href;
                    });
                    </script>";
                }
                if (isset($_POST['deleteBitstream'])) {
                    $resultDeleteBitstream = DSpaceREST::deleteBitstreamDSpace($_POST['deleteBitstream'], $cookies);
                    if (isset($cursor["_source"]["USP"]["fullTextFiles"])) { 
                        $body["doc"]["USP"]["fullTextFiles"] = $cursor["_source"]["USP"]["fullTextFiles"];
                        $body["doc"]["USP"]["fullTextFiles"] = removeElementWithValue($body["doc"]["USP"]["fullTextFiles"], "uuid", $_POST['deleteBitstream']);
                        //$body["doc"]["USP"]["fullTextFiles"] = [];
                        $resultUpdateFilesElastic = elasticsearch::elastic_update($_GET['_id'], $type, $body);
                        print_r($resultUpdateFilesElastic);
                    }
                    
                    echo '<div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Arquivo excluído com sucesso</p>
                    </div>';

                    echo "<script type='text/javascript'>
                    $(document).ready(function(){  
                            //Reload the page
                            window.location = window.location.href;
                    });
                    </script>";

    
                }

                if (isset($_POST['makePrivateBitstream'])) {
                    
                    /* Delete Annonymous Policy */
                    $resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyID'], $cookies);
                    /* Add Restricted Policy */
                    $resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyAction'], $dspaceRestrictedID, $_POST['policyResourceType'], $_POST['policyRpType'], $cookies);
    
                }

                if (isset($_POST['makePublicBitstream'])) {

                    /* Delete Annonymous Policy */
                    $resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePublicBitstream'], $_POST['policyID'], $cookies);
                    /* Add Public Policy */
                    $resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePublicBitstream'], $_POST['policyAction'], $dspaceAnnonymousID, $_POST['policyResourceType'], $_POST['policyRpType'], $cookies);
    
                }                  
                
                $bitstreamsDSpace = DSpaceREST::getBitstreamDSpace($itemID, $cookies);

            } else {

                $createForm  = '<form action="' . $actual_link . '" method="post">
                        <input type="hidden" name="createRecord" value="true" />
                        <button class="uk-button uk-button-danger" name="btn_submit">Criar registro no DSpace</button>
                        </form>';                
                
                if (isset($_POST["createRecord"])) {
                    if ($_POST["createRecord"] == "true") {
                        
                        $dataString = DSpaceREST::buildDC($cursor, $_GET['_id']);
                        $resultCreateItemDSpace = DSpaceREST::createItemDSpace($dataString, $dspaceCollection, $cookies);
                        
                        echo "<script type='text/javascript'>
                        $(document).ready(function(){  
                                //Reload the page
                                window.location = window.location.href;
                        });
                        </script>";
                    } 
                }

            }
           
        }
        ?>

        <?php PageSingle::metadataGoogleScholar($cursor["_source"]); ?>
        <?php 
        if ($cursor["_source"]["type"] == "ARTIGO DE PERIODICO") {
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
                                <a class="uk-button uk-button-primary" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $cursor["_id"];?>" target="_blank" rel="noopener noreferrer nofollow">Ver no Dedalus</a>                    
                            </li>
                        </ul>
                        <h5 class="uk-panel-title">Exportar registro bibliográfico</h5>
                        <ul class="uk-nav uk-margin-top uk-margin-bottom">
                            <hr>                   
                            <li>
                                <a class="uk-button uk-button-primary" href="<?php echo $url_base; ?>/tools/export.php?search[]=sysno.keyword%3A<?php echo $cursor["_id"];?>&format=ris" rel="noopener noreferrer nofollow">RIS (EndNote)</a>
                            </li>
                            <li>
                                <a class="uk-button uk-button-primary" href="<?php echo $url_base; ?>/tools/export.php?search[]=sysno.keyword%3A<?php echo $cursor["_id"];?>&format=bibtex" rel="noopener noreferrer nofollow">Bibtex</a>
                            </li>                            
                            <li>
                                <a class="uk-button uk-button-primary" href="<?php echo $url_base; ?>/tools/export.php?search[]=sysno.keyword%3A<?php echo $cursor["_id"];?>&format=csvThesis" rel="noopener noreferrer nofollow">Tabela (TSV)</a>
                            </li>                            
                        </ul>

                        <!-- Métricas - Início -->
                        <?php if (!empty($cursor["_source"]['doi'])) : ?>
                        <h3 class="uk-panel-title"><?php echo $t->gettext('Métricas'); ?></h3>                        
                        <hr>                        
                            <?php if ($show_metrics == true) : ?>
                                <?php if (!empty($cursor["_source"]['doi'])) : ?>
                            <div class="uk-alert-warning" uk-alert>
                                <p><?php echo $t->gettext('Métricas'); ?>:</p>
                                <div uk-grid>
                                    <div data-badge-popover="right" data-badge-type="1" data-doi="<?php echo $cursor["_source"]['doi'];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                    <div><a href="https://plu.mx/plum/a/?doi=<?php echo $cursor["_source"]['doi'];?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true" target="_blank" rel="noopener noreferrer nofollow"></a></div>
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
                        $record->completeRecordMetadata($t, $url_base);
                        ?>                              
                     
                        <?php 
                        if (!empty($cursor["_source"]['url'])||!empty($cursor["_source"]['doi'])) {
                            if ($use_api_oadoi == true) {
                                if (!empty($cursor["_source"]['doi'])) {
                                    $oadoi = metrics::get_oadoi($cursor["_source"]['doi']);
                                    echo '<div class="uk-alert-primary uk-h6 uk-padding-small">Informações sobre o DOI: '.$cursor["_source"]['doi'].' (Fonte: <a href="http://oadoi.org" target="_blank" rel="noopener noreferrer nofollow">oaDOI API</a>)';
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

                            if (isset($cursor["_source"]["USP"]["unpaywall"])) {
                                echo '<div class="uk-alert-danger uk-h6 uk-padding-small">Versões disponíveis em Acesso Aberto do: '.$cursor["_source"]['doi'].' (Fonte: <a href="http://unpaywall.org" target="_blank" rel="noopener noreferrer nofollow">Unpaywall API</a>)';
                                echo '<p>Título do periódico: '.$cursor["_source"]["USP"]["unpaywall"]["journal_name"].'</p>';
                                echo '<p>ISSN: '.$cursor["_source"]["USP"]["unpaywall"]["journal_issns"].'</p>';
                                echo '<ul>';
                                if (!empty($cursor["_source"]["USP"]["unpaywall"]["best_oa_location"])) {
                                    echo '<li>Melhor URL em Acesso Aberto:<ul>';
                                    if (isset($cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["url_for_landing_page"])) {
                                        echo '<li><b><a href="'.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["url_for_landing_page"].'">Página do artigo</a></b></li>';
                                    }
                                    if (isset($cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["url_for_pdf"])) {
                                        echo '<li><b><a href="'.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["url_for_pdf"].'">Link para o PDF</a></b></li>';
                                    }
                                    echo '<li>Evidência: '.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["evidence"].'</li>';
                                    echo '<li>Licença: '.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["license"].'</li>';
                                    echo '<li>Versão: '.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["version"].'</li>';
                                    echo '<li>Tipo de hospedagem: '.$cursor["_source"]["USP"]["unpaywall"]["best_oa_location"]["host_type"].'</li>';
                                    echo '</ul></li>';
                                } 
                                echo "<br/><br/>";
                                if (!empty($cursor["_source"]["USP"]["unpaywall"]["oa_locations"])) {
                                    echo '<li>Outras alternativas de URLs em Acesso Aberto:<ul>';
                                    foreach ($cursor["_source"]["USP"]["unpaywall"]["oa_locations"] as $oa_locations) {
                                        echo '<li><ul>';
                                        if (isset($oa_locations["url_for_landing_page"])) {
                                            echo '<li><b><a href="'.$oa_locations["url_for_landing_page"].'">Página do artigo</a></b></li>';
                                        }
                                        if (isset($oa_locations["url_for_pdf"])) {
                                            echo '<li><b><a href="'.$oa_locations["url_for_pdf"].'">Link para o PDF</a></b></li>';
                                        }
                                        echo '<li>Evidência: '.$oa_locations["evidence"].'</li>';
                                        echo '<li>Licença: '.$oa_locations["license"].'</li>';
                                        echo '<li>Versão: '.$oa_locations["version"].'</li>';
                                        echo '<li>Tipo de hospedagem: '.$oa_locations["host_type"].'</li>';
                                        echo '</ul></li>';   
                                        //print_r($oa_locations);
                                        echo "<br/><br/>";
                                    }
                                    echo '</ul></li>';
                                    
                                } else {
                                    echo "Não possui versão em Acesso aberto";
                                }
                                echo '</ul></div>';                                
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
                        <?php if (intval($cursor["_source"]["datePublished"]) >= 2010 ) : ?>
                            <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"])) : ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Qualis do periódico</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["serial_metrics"]["issn"][0]); ?></p>

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2012"])) : ?>
                                        <p>Qualis 2010-2012</p>
                                        <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2012"] as $metrics_2012) : ?>
                                            <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2012["area_nota"]); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>  

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2015"])) : ?>
                                        <p>Qualis 2015</p>
                                        <?php foreach ($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2015"] as $metrics_2015) : ?>
                                            <p class="uk-text-small uk-margin-remove">Área / Nota: <?php print_r($metrics_2015["area_nota"]); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($cursor["_source"]["USP"]["serial_metrics"]["qualis"]["2016"])) : ?>
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
                        <?php if (!empty($cursor["_source"]["USP"]["JCR"])) : ?>
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
                        <?php if (!empty($cursor["_source"]["USP"]["citescore"])) : ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Citescore</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["citescore"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["citescore"]["issn"][0]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Citescore - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["citescore"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SJR - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["SJR"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SNIP - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["SNIP"]); ?></p>                               
                                    <p class="uk-text-small uk-margin-remove">Open Access: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["open_access"]); ?></p> 
                                </li>
                            </div>
                        <?php endif; ?>  
                        <!-- Citescore - Fim -->                        
                        
                        <hr>

                        <!-- Query itens on Aleph - Start -->                            
                        <?php
                        if (!empty($cursor["_source"]["item"])) {
                            echo '<div id="exemplares'.$cursor["_id"].'">';
                            echo "<table class=\"uk-table uk-table-small uk-text-small uk-table-striped\">";
                            echo "<caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th><small>Biblioteca</small></th>";
                            echo "<th><small>Cód. de barras</small></th>";
                            echo "<th><small>Núm. de chamada</small></th>";
                            echo "</tr>";  
                            echo "</thead>";
                            echo "<tbody>";                               

                            foreach ($cursor["_source"]["item"] as $item) {
                                echo '<tr>';
                                echo '<td><small><a href="http://www.sibi.usp.br/bibliotecas/fisicas/?char='. $item["Z30_SUB_LIBRARY"] .'" target="_blank" rel="noopener noreferrer nofollow">'.$item["Z30_SUB_LIBRARY"].'</a></small></td>';
                                echo '<td><small>'.$item["Z30_BARCODE"].'</small></td>';
                                echo '<td><small>'.$item["Z30_CALL_NO"].'</small></td>';
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

                        <?php
                        if (isset($_SESSION['oauthuserdata'])) {
                            $user = json_decode(json_encode($_SESSION['oauthuserdata']), true);
                        }
                        if (isset($user['vinculo'])) {
                            foreach ($user['vinculo'] as $key => $value) {
                                if (in_array($value["siglaUnidade"], $cursor["_source"]["unidadeUSP"])) {
                                    $isOfThisUnit = true;
                                } else {
                                    $isOfThisUnit = false;
                                }
                            }
                        } else {
                            $isOfThisUnit = false;
                        }
                        ?>

                        

                        <!-- Query bitstreams on Dspace - Start -->   
                        <?php

                        if (isset($_SESSION['oauthuserdata'])) {
                            if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)) {
                                if ($isOfThisUnit == true) {
                                    if ($testDSpace == "true") {
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

                                }

                            }

                        }                      

                        if (!empty($bitstreamsDSpace)) {
                            echo '<div class="uk-alert-primary" uk-alert>
                            <h4>Download do texto completo</h4>
                            <a class="uk-alert-close" uk-close></a>

                            <table class="uk-table uk-table-justify uk-table-divider">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nome do arquivo</th>
                                    <th>Tipo de acesso</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>';

                            foreach ($bitstreamsDSpace as $key => $value) {

                                $bitstreamPolicy = DSpaceREST::getBitstreamPolicyDSpace($value["uuid"], $cookies);
                                
                                foreach ($bitstreamPolicy as $bitstreamPolicyUnit) {
                                    if ($bitstreamPolicyUnit["groupId"] == $dspaceAnnonymousID) {

                                        if (isset($_SESSION['oauthuserdata'])) {
                                            if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)) {
                                                echo '<tr>';
                                                echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/bitstreams/'.$value["uuid"].'" target="_blank" rel="noopener noreferrer nofollow"><img data-src="'.$url_base.'/inc/images/pdf.png" width="70" height="70" alt="" uk-img></a></th>';
                                                echo '<th>'.$value["name"].'</th>';
                                                echo '<th><img width="48" alt="Open Access logo PLoS white" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Open_Access_logo_PLoS_white.svg/64px-Open_Access_logo_PLoS_white.svg.png"></th>';
                                                echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/directbitstream/'.$value["uuid"].'/'.$value["name"].'" target="_blank" rel="noopener noreferrer nofollow">Direct link</a></th>';
                                                if ($isOfThisUnit == true) {
                                                    echo '<th><button class="uk-button uk-button-danger uk-margin-small-right" type="button" uk-toggle="target: #modal-deleteBitstream-'.$value["uuid"].'">Excluir</button></th>';
                                                    echo '<div id="modal-deleteBitstream-'.$value["uuid"].'" uk-modal>';
                                                    echo '<div class="uk-modal-dialog uk-modal-body">';
                                                    echo '<h2 class="uk-modal-title">Excluir arquivo</h2>';
                                                    echo '<p>Tem certeza que quer excluir o arquivo '.$value["name"].'?</p>';
                                                    echo '<p class="uk-text-right">';
                                                    echo '<button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>';
                                                    echo '<form action="' . $actual_link . '" method="post">';
                                                    echo '<input type="hidden" name="deleteBitstream" value="'.$value["uuid"].'" />';
                                                    echo '<button class="uk-button uk-button-danger" name="btn_submit">Excluir</button>';
                                                    echo '</form>';
                                                    echo '</p>';
                                                    echo '</div>';
                                                    echo '</div>';
    
    
                                                    echo '<th><button class="uk-button uk-button-secondary uk-margin-small-right" type="button" uk-toggle="target: #modal-Private-'.$value["uuid"].'">Tornar privado</button></th>';
                                                
                                                    echo '<div id="modal-Private-'.$value["uuid"].'" uk-modal>
                                                        <div class="uk-modal-dialog uk-modal-body">
                                                            <h2 class="uk-modal-title">Tornar privado</h2>
                                                            <p>Tem certeza que quer tornar privado o arquivo '.$value["name"].'?</p>
                                                            <p class="uk-text-right">
                                                                <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
                                                                <form action="' . $actual_link . '" method="post">
                                                                    <input type="hidden" name="makePrivateBitstream" value="'.$value["uuid"].'" />
                                                                    <input type="hidden" name="policyID" value="'.$bitstreamPolicyUnit["id"].'" />
                                                                    <input type="hidden" name="policyAction" value="'.$bitstreamPolicyUnit["action"].'" />
                                                                    <input type="hidden" name="policyGroupId" value="'.$bitstreamPolicyUnit["groupId"].'" />
                                                                    <input type="hidden" name="policyResourceType" value="'.$bitstreamPolicyUnit["resourceType"].'" />
                                                                    <input type="hidden" name="policyRpType" value="'.$bitstreamPolicyUnit["rpType"].'" />
                                                                    <button class="uk-button uk-button-secondary" name="btn_submit">Tornar privado</button>
                                                                </form>
                                                            </p>
                                                        </div>
                                                    </div>'; 
    
    
                                                }
                                                echo '<th></th>';
                                            } else {
                                                echo '<tr>';
                                                echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/bitstreams/'.$value["uuid"].'" target="_blank" rel="noopener noreferrer nofollow"><img data-src="'.$url_base.'/inc/images/pdf.png" width="70" height="70" alt="" uk-img></a></th>';
                                                echo '<th>'.$value["name"].'</th>';
                                                echo '<th><img width="48" alt="Open Access logo PLoS white" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Open_Access_logo_PLoS_white.svg/64px-Open_Access_logo_PLoS_white.svg.png"></th>';
                                                echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/directbitstream/'.$value["uuid"].'/'.$value["name"].'" target="_blank" rel="noopener noreferrer nofollow">Direct link</a></th>';  
                                            }
                                                                                
                                        } else {
                                            echo '<tr>';
                                            echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/bitstreams/'.$value["uuid"].'" target="_blank" rel="noopener noreferrer nofollow"><img data-src="'.$url_base.'/inc/images/pdf.png" width="70" height="70" alt="" uk-img></a></th>';
                                            echo '<th>'.$value["name"].'</th>';
                                            echo '<th><img width="48" alt="Open Access logo PLoS white" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Open_Access_logo_PLoS_white.svg/64px-Open_Access_logo_PLoS_white.svg.png"></th>';
                                            echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/directbitstream/'.$value["uuid"].'/'.$value["name"].'" target="_blank" rel="noopener noreferrer nofollow">Direct link</a></th>';

                                        }
    
                                    } elseif ($bitstreamPolicyUnit["groupId"] == $dspaceRestrictedID) {
    
                                  
                                        if (isset($_SESSION['oauthuserdata'])) {

                                            echo '<tr>';
                                            echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/bitstreams/'.$value["uuid"].'" target="_blank" rel="noopener noreferrer nofollow"><img data-src="'.$url_base.'/inc/images/pdf.png" width="70" height="70" alt="" uk-img></a></th>';
                                            echo '<th>'.$value["name"].'</th>';
                                            echo '<th><img width="48" alt="Closed Access logo white" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Closed_Access_logo_white.svg/64px-Closed_Access_logo_white.svg.png"></th>';
                                            echo '<th><a href="http://'.$_SERVER["SERVER_NAME"].'/directbitstream/'.$value["uuid"].'/'.$value["name"].'" target="_blank" rel="noopener noreferrer nofollow">Direct link</a></th>';
                                            
                                            if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)) {
    
                                                echo '<th><button class="uk-button uk-button-danger uk-margin-small-right" type="button" uk-toggle="target: #modal-deleteBitstream-'.$value["uuid"].'">Excluir</button></th>';
                                                
                                                echo '<div id="modal-deleteBitstream-'.$value["uuid"].'" uk-modal>
                                                    <div class="uk-modal-dialog uk-modal-body">
                                                        <h2 class="uk-modal-title">Excluir arquivo</h2>
                                                        <p>Tem certeza que quer excluir o arquivo '.$value["name"].'?</p>
                                                        <p class="uk-text-right">
                                                            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
                                                            <form action="' . $actual_link . '" method="post">
                                                                <input type="hidden" name="deleteBitstream" value="'.$value["uuid"].'" />
                                                                <button class="uk-button uk-button-danger" name="btn_submit">Excluir</button>
                                                            </form>
                                                        </p>
                                                    </div>
                                                </div>';
    
                                                echo '<th><button class="uk-button uk-button-secondary uk-margin-small-right" type="button" uk-toggle="target: #modal-Public-'.$value["uuid"].'">Tornar público</button></th>';
                                                
                                                echo '<div id="modal-Public-'.$value["uuid"].'" uk-modal>
                                                    <div class="uk-modal-dialog uk-modal-body">
                                                        <h2 class="uk-modal-title">Tornar público</h2>
                                                        <p>Tem certeza que quer tornar público o arquivo '.$value["name"].'?</p>
                                                        <p class="uk-text-right">
                                                            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
                                                            <form action="' . $actual_link . '" method="post">
                                                                <input type="hidden" name="makePublicBitstream" value="'.$value["uuid"].'" />
                                                                <input type="hidden" name="policyID" value="'.$bitstreamPolicyUnit["id"].'" />
                                                                <input type="hidden" name="policyAction" value="'.$bitstreamPolicyUnit["action"].'" />
                                                                <input type="hidden" name="policyGroupId" value="'.$bitstreamPolicyUnit["groupId"].'" />
                                                                <input type="hidden" name="policyResourceType" value="'.$bitstreamPolicyUnit["resourceType"].'" />
                                                                <input type="hidden" name="policyRpType" value="'.$bitstreamPolicyUnit["rpType"].'" />
                                                                <button class="uk-button uk-button-secondary" name="btn_submit">Tornar público</button>
                                                            </form>
                                                        </p>
                                                    </div>
                                                </div>'; 
    
                                                echo '<th></th>';
                                            }                                    
                                        }                                    
    
                                    } else {
    
                                    }                                    

                                }

                            }                               
                            echo '</tbody></table></div>';
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

                        <!-- References - CrossRef - Start -->
                        <?php if (!empty($cursor["_source"]["USP"]["crossref"]["message"]["reference"])) : ?>
                        <div class="uk-alert-primary" uk-alert>
                        <h5><?php echo $t->gettext('Referências citadas na obra'); ?></h5>
                        <a class="uk-alert-close" uk-close></a>
                        <table class="uk-table uk-table-justify uk-table-divider uk-table-striped">
                            <tbody>
                                <?php 
                                foreach ($cursor["_source"]["USP"]["crossref"]["message"]["reference"] as $crossRefReference) {
                                    echo "<tr><th>";
                                    if (isset($crossRefReference["unstructured"])) {
                                        print_r($crossRefReference["unstructured"]); 
                                    } else {
                                        if (isset($crossRefReference["author"])) {
                                            echo ''.$t->gettext("Autor: ").''.$crossRefReference["author"].'<br/>';
                                        }
                                        if (isset($crossRefReference["article-title"])) {
                                            echo ''.$t->gettext("Título: ").''.$crossRefReference["article-title"].'<br/>';
                                        }                                        
                                        if (isset($crossRefReference["journal-title"])) {
                                            echo ''.$t->gettext("Título do periódico: ").''.$crossRefReference["journal-title"].'<br/>';
                                        }
                                        if (isset($crossRefReference["volume"])) {
                                            echo ''.$t->gettext("Volume: ").''.$crossRefReference["volume"].'<br/>';
                                        }
                                        if (isset($crossRefReference["issue"])) {
                                            echo ''.$t->gettext("Fascículo: ").''.$crossRefReference["issue"].'<br/>';
                                        }
                                        if (isset($crossRefReference["first-page"])) {
                                            echo ''.$t->gettext("Primeira página: ").''.$crossRefReference["first-page"].'<br/>';
                                        }
                                        if (isset($crossRefReference["year"])) {
                                            echo ''.$t->gettext("Ano: ").''.$crossRefReference["year"].'<br/>';
                                        }
                                        if (isset($crossRefReference["DOI"])) {
                                            echo ''.$t->gettext("DOI: ").'<a href="https://doi.org/'.$crossRefReference["DOI"].'" target="_blank" rel="noopener noreferrer">'.$crossRefReference["DOI"].'</a><br/>';
                                        }                                        
                                        //print_r($crossRefReference);
                                    }

                                    echo "</th></tr>";
                                }  
                                ?>
                            </tbody>
                        </table>

                        </div>
                        <?php endif; ?>
                        <!-- References - CrossRef - End -->      

                        <!-- Other works of same authors - Start -->
                        <?php 
                        foreach ($cursor["_source"]["authorUSP"] as $authorUSPArray) {
                            $authorUSPArrayCodpes[] = $authorUSPArray["codpes"];
                        }

                        $queryOtherWorks["query"]["bool"]["must"]["query_string"]["query"] = 'authorUSP.codpes:('.implode(" OR ", $authorUSPArrayCodpes).')';
                        $queryOtherWorks["query"]["bool"]["must_not"]["term"]["name.keyword"] = $cursor["_source"]["name"];
                        $resultOtherWorks = elasticsearch::elastic_search($type, ["_id","name"], 10, $queryOtherWorks);
                        echo '<div class="uk-alert-primary" uk-alert>';
                        echo '<h5>Últimas obras dos mesmos autores vinculados com a USP cadastradas na BDPI:</h5><ul>';
                        foreach ($resultOtherWorks["hits"]["hits"] as $othersTitles) {
                            //print_r($othersTitles);
                            echo '<li><a href="'.$url_base.'/item/'.$othersTitles["_id"].'" target="_blank">'.$othersTitles["_source"]["name"].'</a></li>';
                        }                        
                        echo '</ul></div>';
                        ?>
                        <!-- Other works of same authors - End -->                                            
                            
                </div>
            </div>
            <hr class="uk-grid-divider">        
            <?php require 'inc/footer.php'; ?> 
        </div>
  

        <?php require 'inc/offcanvas.php'; ?>
        <?php ob_flush(); flush(); ?>
        <script async src="https://badge.dimensions.ai/badge.js" charset="utf-8"></script>   
        <?php  DSpaceREST::logoutREST($cookies); ?>
        
    </body>
</html>
