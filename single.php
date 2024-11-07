<?php
/**
 * Item page
 */

require 'inc/config.php';

/* Citeproc-PHP*/
require 'inc/citeproc-php/CiteProc.php';
$csl_abnt = file_get_contents('inc/citeproc-php/style/abnt-novo.csl');
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
try {	
	if(!empty($_GET['_id'])){
		$cursor = elasticsearch::elastic_get($_GET['_id'], $type, null);
	} else {
		throw new Exception('Id do registro não definido.');
	}

	if(is_bdta() && !is_staffUser() && (!$cursor["_source"]["USP"]["indicado_por_orgao"] || !isset($cursor["_source"]["files"]["database"])) ){
		throw new Exception('Trabalho indisponível.');	
	}
}
catch (exception $e) {
	header('HTTP/1.1 404 Not Found');
	//http_response_code(404);
	//header('Location: '.$url_base .'/item/404.php?_id='.$_GET['_id']);
	header('Location: '.$url_base .'/404.php');
	die();
}
?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
<link rel="canonical" href="<?=$url_base?>/item/<?=$cursor['_id']?>" />
    <!-- Altmetric Script -->
    <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>

    <!-- PlumX Script -->
    <script src="https://cdn.plu.mx/widget-popup.js" integrity="sha256-AXguJKYxgZY9FzwZE8U8EZxUQjcYT6iSQLLGiZTVW84=" crossorigin="anonymous"></script>

  <?php 
  require 'inc/meta-header.php';
  
  /* DSpace */
  if (isset($dspaceRest)) {
      $actual_link = "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      /* Search for existing record on DSpace */
      if(isset($cursor) && is_staffUser()){
	      $itemID = DSpaceREST::searchItemDSpace($cursor["_id"], $_SESSION["DSpaceCookies"]);
	      //$itemID = $cursor["_source"]["files"]["database"][0]["dspace_object_id"];
      }
	
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
          if (is_staffUser()) {
              $uploadForm = '<form id="upload-form" class="uk-form" action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                      <fieldset data-uk-margin>
                      <legend>Enviar um arquivo</legend>
		      <input type="file" id="rep-file" onchange="enableUploadButton();" name="file" required>';

		      if ($uploadVersion){
                      	  $uploadForm .= '<select class="uk-select" name="version" id="file-version" onchange="enableUploadButton();" required>
                              <option selected disabled value="">'. $t->gettext("Selecione a versão do arquivo") .'</option>
                              <option value="publishedVersion">'. $t->gettext("Versão publicada") . '</option>
                              <option value="acceptedVersion">' . $t->gettext("Versão aceita") . '</option>
			  </select>';
		      } else {
			 $uploadForm .= '<input type="hidden" name="version" id="file-version" value="publishedVersion"><br/>';
		      }
		
		      $uploadForm .= '<button class="uk-button uk-button-primary" type="submit" onclick="loadAction(\'upload\');" name="btn_submit" id="upload">Upload</button>
	                  </fieldset>
        	          </form>';
          }
          if (is_staffUser() && isset($_FILES['file'])) {
            if($_FILES['file']['size'] < $maxFileSize && $_FILES['file']['type'] == 'application/pdf'){
              if(checkDSpaceAPI($pythonBdpiApi)){
		$_FILES['file']['name'] = filename_sanitize($_FILES['file']['name']);
                $userBitstream = ''.$_POST["version"].'-'.$_SESSION['oauthuserdata']->{'loginUsuario'};
	        $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemID, $_FILES, $userBitstream, $_SESSION["DSpaceCookies"]);
                //$resultUpdateFilesElastic = elasticsearch::elastic_update($_GET['_id'], $type, $body);
                ElasticPatch::uploader($resultAddBitstream["uuid"]);
                ElasticPatch::publisher($resultAddBitstream["uuid"]);
                ElasticPatch::syncElastic($cursor["_source"]["sysno"]);
                echo "<script type='text/javascript'>
                $(document).ready(function(){
                        //Reload the page
                        window.location = window.location.href;
                });
                </script>";
              } else {
                $responseMessage = getAlertMessage("Não é possível realizar o upload do arquivo", "danger");
              }
            } else {
		$msg='';
		if($_FILES['file']['type'] != 'application/pdf')
			$msg = 'Somente arquivos no formato PDF são autorizados para upload.<br/>';
		if($_FILES['file']['size'] > $maxFileSize)
			$msg .= 'Upload não realizado. O tamanho do arquivo é <strong>'. number_format($_FILES['file']['size'] /1024 /1024, 2, '.', '' ) . 'MB</strong> e o limite para upload é de <strong>' . number_format($maxFileSize /1024/1024, 2, '.', '') . 'MB</strong>.'; 
                $responseMessage = getAlertMessage($msg , "danger", true);
            }
          }

          if (isset($_POST['deleteBitstream']) && is_staffUser()) {
            if(checkDSpaceAPI($pythonBdpiApi)){
              $resultDeleteBitstream = DSpaceREST::deleteBitstreamDSpace($_POST['deleteBitstream'], $_SESSION["DSpaceCookies"]);
	      ElasticPatch::deleter($_POST["deleteBitstream"]);
              ElasticPatch::syncElastic($cursor["_source"]["sysno"]);
              
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
            } else {
              $responseMessage = getAlertMessage("não é possível realizar o upload do arquivo", "danger");
            }
          }

          if (isset($_POST['makePrivateBitstream']) && is_staffUser()) {
            if(checkDSpaceAPI($pythonBdpiApi)){
              /* Delete Annonymous Policy */
              //$resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyID'], $_SESSION["DSpaceCookies"]);
              /* Add Restricted Policy */
              //$resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyAction'], $dspaceRestrictedID, $_POST['policyResourceType'], $_POST['policyRpType'], $_SESSION["DSpaceCookies"]);
              ElasticPatch::doPrivate($_POST["makePrivateBitstream"],$_POST['policyID']);
	      ElasticPatch::privater($_POST["makePrivateBitstream"]);
              ElasticPatch::syncElastic($cursor["_source"]["sysno"]);
              echo "<script type='text/javascript'>
              $(document).ready(function(){
                      //Reload the page
                      window.location = window.location.href;
              });
              </script>";
            } else {
              $responseMessage = getAlertMessage("não é possível realizar o upload do arquivo", "danger");
            }

          }

          if (isset($_POST['makePublicBitstream']) && is_staffUser()) {
            if(checkDSpaceAPI($pythonBdpiApi)){
              /* Delete Annonymous Policy */
              #$resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePublicBitstream'], $_POST['policyID'], $_SESSION["DSpaceCookies"]);
              /* Add Public Policy */
              #$resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePublicBitstream'], $_POST['policyAction'], $dspaceAnnonymousID, $_POST['policyResourceType'], $_POST['policyRpType'], $_SESSION["DSpaceCookies"]);
              ElasticPatch::doPublic($_POST["makePublicBitstream"],$_POST['policyID']);
	      ElasticPatch::publisher($_POST["makePublicBitstream"]);
              ElasticPatch::syncElastic($cursor["_source"]["sysno"]);
              echo "<script type='text/javascript'>
              $(document).ready(function(){
                     //Reload the page
                     window.location = window.location.href;
              });
              </script>";
            } else {
              $responseMessage = getAlertMessage("não é possível realizar o upload do arquivo", "danger");
            }
          }

          if (isset($_POST['doEmbargoBitstream']) && is_staffUser()){
            if(checkDSpaceAPI($pythonBdpiApi)){
              ElasticPatch::doEmbargo($_POST["doEmbargoBitstream"],$_POST['policyID'],$_POST['releaseDate']);
	      ElasticPatch::publisher($_POST["doEmbargoBitstream"]);
              ElasticPatch::syncElastic($cursor["_source"]["sysno"]);
              echo "<script type='text/javascript'>
              $(document).ready(function(){
                      //Reload the page
                      window.location = window.location.href;
              });
              </script>";
            } else {
              $responseMessage = getAlertMessage("não é possível realizar o upload do arquivo", "danger");
            }
          }

      } else {
        if(checkDSpaceAPI($pythonBdpiApi)){
          $createForm  = '<form action="' . $actual_link . '" method="post">
                  <input type="hidden" name="createRecord" value="true" />
                  <button class="uk-button uk-button-danger" name="btn_submit" id="dspace-record" onclick="loadAction(\'dspace-record\');">Criar registro no DSpace</button>
                  </form>';

          if (isset($_POST["createRecord"]) && $_POST["createRecord"]) {
                  $dataString = DSpaceREST::buildDC($cursor, $_GET['_id']);
                  $resultCreateItemDSpace = DSpaceREST::createItemDSpace($dataString, $dspaceCollection, $_SESSION["DSpaceCookies"]);

                  echo "<script type='text/javascript'>
                  $(document).ready(function(){
                          //Reload the page
                          window.location = window.location.href;
                  });
                  </script>";
          }
        } else {
          $responseMessage = getAlertMessage("não é possível realizar o upload do arquivo", "danger");
        }

      }

  }
  ?>
    
    <?php PageSingle::metadataGoogleScholar($cursor["_source"]); ?>

    <title><?=$branch_abrev?> - Detalhe do registro: <?=$cursor["_source"]['name']?></title>

    <?php
    if ($cursor["_source"]["type"] == "ARTIGO DE PERIODICO") {
            PageSingle::jsonLD($cursor["_source"]);
    }
    ?>

</head>
<body onload="checkMobileUpdateURL();" style="min-height: 45em; position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        require 'inc/navbar.php';
        ?>
        <br/><br/><br/>

        <div class="uk-container uk-margin-large-bottom" style="position: relative; padding-bottom: 15em;">
            <div class="uk-grid uk-margin-top" uk-grid>
                <div class="uk-width-1-4@m">
                    <div class="uk-card uk-card-body">
                        <!--<h5 class="uk-panel-title">Ver registro no DEDALUS</h5>-->
                        <ul class="uk-nav uk-margin-top uk-margin-bottom">
                            <!--<hr>-->
                            <li>
                                <a class="uk-button exportacao" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?=$cursor["_id"]?>" target="_blank" rel="noopener noreferrer nofollow">Registro no Dedalus</a>
                            </li>
                        </ul>
                        <h5 class="uk-panel-title">Exportar registro bibliográfico</h5>
                        <ul class="uk-nav uk-margin-top uk-margin-bottom">
                            <hr>
                            <li>
                                <a class="uk-button exportacao" href="<?=$url_base?>/tools/export.php?search[]=sysno.keyword%3A<?=$cursor["_id"]?>&format=ris" rel="noopener noreferrer nofollow">RIS (EndNote)</a>
                            </li>
                            <li class="uk-nav-divider">
                                <a class="uk-button exportacao" href="<?=$url_base?>/tools/export.php?search[]=sysno.keyword%3A<?=$cursor["_id"]?>&format=bibtex" rel="noopener noreferrer nofollow">Bibtex</a>
                            </li>
                            <li class="uk-nav-divider">
                                <a class="uk-button exportacao" href="<?=$url_base?>/tools/export.php?search[]=sysno.keyword%3A<?=$cursor["_id"]?>&format=csvThesis" rel="noopener noreferrer nofollow">Tabela (TSV)</a>
                            </li>
                        </ul>

                        <!-- Métricas - Início -->
                        <?php if (!empty($cursor["_source"]['doi'])) : ?>
                        <h3 class="uk-panel-title"></h3>
                        <hr>
                            <?php if ($show_metrics) : ?>
                                <?php if (!empty($cursor["_source"]['doi'])) : ?>
                            <div class="uk-alert-warning" uk-alert>
                                <p><?=$t->gettext('Métricas')?>:</p>
                                <div uk-grid>
                                    <div data-badge-popover="right" data-badge-type="1" data-doi="<?=$cursor["_source"]['doi']?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
                                    <div><a href="https://plu.mx/plum/a/?doi=<?=$cursor["_source"]['doi']?>" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true" target="_blank" rel="noopener noreferrer nofollow"></a></div>
                                    <div><object data="https://api.elsevier.com/content/abstract/citation-count?doi=<?=$cursor["_source"]['doi']?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object></div>
                                    <div><span class="__dimensions_badge_embed__" data-doi="<?=$cursor["_source"]['doi']?>" data-hide-zero-citations="true" data-style="small_rectangle"></span></div>
                                    <?php if(!empty($cursor["_source"]["USP"]["opencitation"]["num_citations"])) :?>
                                        <div>Citações no OpenCitations: <?=$cursor["_source"]["USP"]["opencitation"]["num_citations"]?></div>
                                    <?php endif; ?>
                                    <?php if(isset($cursor["_source"]["USP"]["aminer"]["num_citation"])) :?>
                                        <div>Citações no AMiner: <?=$cursor["_source"]["USP"]["aminer"]["num_citation"]?></div>
                                    <?php endif; ?>
                                    <div>
                                        <!--
                                        <?php
                                            /*$citations_scopus = get_citations_elsevier($cursor["_source"]['doi'][0],$api_elsevier);
                                            if (!empty($citations_scopus['abstract-citations-response'])) {
                                                echo '<a href="https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp='.$citations_scopus['abstract-citations-response']['identifier-legend']['identifier'][0]['scopus_id'].'&origin=inward">Citações na SCOPUS: '.$citations_scopus['abstract-citations-response']['citeInfoMatrix']['citeInfoMatrixXML']['citationMatrix']['citeInfo'][0]['rowTotal'].'</a>';
                                                echo '<br/><br/>';
					    }*/
                                        ?>-->
                                        
                                    </div>
                                </div>
                            </div>
                            <?php else : ?>
                                <?php if(isset($cursor["_source"]["USP"]["aminer"]["num_citation"])) :?>
                                    <?php if($cursor["_source"]["USP"]["aminer"]["num_citation"] > 0) :?>
                                    <div class="uk-alert-warning" uk-alert>
                                        <p><?=$t->gettext('Métricas')?>:</p>
                                        <div uk-grid>
                                            <div>Citações no AMiner: <?=$cursor["_source"]["USP"]["aminer"]["num_citation"]?></div>
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
                                    if (isset($oadoi['results'][0]['is_subscription_journal']) && $oadoi['results'][0]['is_subscription_journal'] == 1) {
                                        echo '<li>Este periódico é de assinatura</li>';
                                    } else {
                                        echo '<li>Este periódico é de acesso aberto</li>';
                                    }
                                    if (isset($oadoi['results'][0]['is_free_to_read']) && $oadoi['results'][0]['is_free_to_read'] == 1) {
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
                                echo '<p>Título: '.$cursor["_source"]["USP"]["unpaywall"]["journal_name"].'</p>';
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
                        <?php if (isset($cursor["_source"]["datePublished"]) && intval($cursor["_source"]["datePublished"]) >= 2010 ) : ?>
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

                        <!-- JCR - Início
                        <?php if (!empty($cursor["_source"]["USP"]["JCR"])) : ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o JCR</h5>
                                <?=print_r($cursor["_source"]["USP"]["JCR"])?>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: < ?php print_r($cursor["_source"]["USP"]["JCR"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: < ?php print_r($cursor["_source"]["USP"]["JCR"]["issn"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Journal Impact Factor - 2017: < ?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2017"]["Journal_Impact_Factor"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Impact Factor without Journal Self Cites - 2017: < ?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2017"]["IF_without_Journal_Self_Cites"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Eigenfactor Score - 2017: < ?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2017"]["Eigenfactor_Score"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">JCR Rank - 2017: < ?php print_r($cursor["_source"]["USP"]["JCR"]["JCR"]["2017"]["JCR_Rank"]); ?></p>
                                </li>
                            </div>
                        <?php endif; ?>
                        JCR - Fim -->

                        <!-- Citescore - Início -->
                        <?php if (!empty($cursor["_source"]["USP"]["citescore"]["title"])) : ?>
                            <div class="uk-alert-primary" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <h5>Informações sobre o Citescore</h5>
                                <li class="uk-h6">
                                    <p class="uk-text-small uk-margin-remove">Título: <?php print_r($cursor["_source"]["USP"]["citescore"]["title"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">ISSN: <?php print_r($cursor["_source"]["USP"]["citescore"]["issn"][0]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">Citescore - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["citescore"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SJR - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["SJR"]); ?></p>
                                    <p class="uk-text-small uk-margin-remove">SNIP - 2017: <?php print_r($cursor["_source"]["USP"]["citescore"]["citescore"]["2017"][0]["SNIP"]); ?></p>
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
                                echo '<td><small><a href="//www.sibi.usp.br/bibliotecas/fisicas/?search='. $item["Z30_SUB_LIBRARY"] .'" target="_blank" rel="noopener noreferrer nofollow">'.$item["Z30_SUB_LIBRARY"].'</a></small></td>';
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
                        //Confere se o usuário tem vínculo - Início
                        if (isset($_SESSION['oauthuserdata'])) {
                            $user = json_decode(json_encode($_SESSION['oauthuserdata']), true);
                            $isOfThisUnit = USP::isOfThisUnit($user["vinculo"], $cursor["_source"]["unidadeUSP"]);
                        //Confere se o usuário tem vínculo - Fim

                        //Query bitstreams on Dspace - Start
                            if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)) {
                                if ($testDSpace == "true") {

                                    if (!empty($uploadForm)) {
                                        echo '<div class="" uk-alert>';
                                        echo '<a class="uk-alert-close" uk-close></a>';
                                        echo '<h5>Gestão do documento digital</h5>';
                                        echo $uploadForm;
                                        echo '</div>';
                                    }

                                    if (!empty($createForm)) {
                                        echo '<div class="" uk-alert>';
                                        echo '<a class="uk-alert-close" uk-close></a>';
                                        echo '<h5>Gestão do documento digital</h5>';
                                        echo $createForm;
                                        echo '</div>';
                                    }

                                }

                                if(!empty($responseMessage)){
                                    echo $responseMessage;
                                }

                                $table_headers = '
                                                  <th>Responsável</th>
                                                  <th colspan="4">Ações</th>
                                                ';
                            }

			}
			if(isset($cursor["_source"]["files"]["database"])){
                            echo '<div class="" uk-alert>
                            <h4>Download do texto completo</h4>

                            <table class="uk-table uk-table-justify uk-table-divider">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nome</th>
				                            <th>Link</th>

';
				if (isset($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)){
					$bitstreamPolicy = [];
					$bitstreamsDSpace = DSpaceREST::getBitstreamDSpace($itemID, $_SESSION["DSpaceCookies"]);
					foreach($bitstreamsDSpace as $bitstreamPolicyUnit){
						$bitstreamPolicy[$bitstreamPolicyUnit["uuid"]] = DSpaceREST::getBitstreamPolicyDSpace($bitstreamPolicyUnit["uuid"], $_SESSION["DSpaceCookies"])[0];
					}
			}
                            echo isset($table_headers) ? $table_headers : "";
                            echo '</tr>
                            </thead>
			    <tbody>';
				foreach($cursor["_source"]["files"]["database"] as $file){
					$file["link"] = "";
					$file["direct_link"] = "";
					if(isset($bitstreamPolicy[$file["bitstream_id"]]))
						$file["bitstreamPolicy"] = $bitstreamPolicy[$file["bitstream_id"]];
					if(strlen($file["file_name"])>25){
						$file["file_name"] = substr($file["file_name"],0,25).'...';
					}
					if($file["status"] == "public"){
						if($file["file_type"] == "publishedVersion"){
							$file["icon"] = "/inc/images/pdf_publicado.svg";
							$file["iconAlt"] = $t->gettext("Versão Publicada");
						} else if($file["file_type"] == "acceptedVersion"){
							$file["icon"] = "/inc/images/pdf_aceito.svg";
							$file["iconAlt"] = $t->gettext("Versão Aceita");
						} else if($firstFile["status"] != "public"){
							$file["icon"] = "/inc/images/pdf_submetido.svg";
							$file["iconAlt"] = $t->gettext("Versão Submetida");
						}
						$file["link"] = '<a href="https://'.$_SERVER["SERVER_NAME"].'/bitstreams/'.$file["bitstream_id"].'" target="_blank" rel="noopener noreferrer nofollow" uk-tooltip="'. $file["iconAlt"] .'"><img class="register-icons" data-src="'.$url_base. $file["icon"] .'" alt="'. $file["iconAlt"] .'" uk-img></a>';
						$file["direct_link"] = '<a class="uk-button uk-button-primary" id="visualizar-pdf" href="https://'.$_SERVER["SERVER_NAME"].'/directbitstream/'.$file["bitstream_id"].'/'.$file["file_name"].'" target="_blank" rel="noopener noreferrer nofollow">Direct link</a>';
					}
					else if ($file["status"] == "embargoed") {
						if($file["file_type"] == "publishedVersion"){
							$file["icon"] = "/inc/images/pdf_publicado_embargado.svg";
						} else if($file["file_type"] == "acceptedVersion"){
							$file["icon"] = "/inc/images/pdf_aceito_embargado.svg";
						} else if($firstFile["status"] != "public"){
							$file["icon"] = "/inc/images/pdf_submetido_embargado.svg";
						} else {
							$file["icon"] = "/inc/images/pdf_embargado.svg";
						}
						if ($_SESSION['localeToUse'] == 'pt_BR'){
							$file["release_date"] =  date('d/m/Y', strtotime($file["release_date"]));

						} else {
							$file["release_date"] = date('Y/m/d', strtotime($file["release_date"]));
						}
						$file["iconAlt"] = $t->gettext("Disponível em ") . $file["release_date"];

					} else if($file["status"] == "private") {
						if($file["file_type"] == "publishedVersion"){
							$file["icon"] = "/inc/images/pdf_publicado_privado.svg";
						} else if($file["file_type"] == "acceptedVersion"){
							$file["icon"] = "/inc/images/pdf_aceito_privado.svg";
						} else if($firstFile["status"] != "public"){
							$file["icon"] = "/inc/images/pdf_submetido_privado.svg";
						} else {
							$file["icon"] = "/inc/images/pdf_privado.svg";
						}
						$file["iconAlt"] = $t->gettext("Privado");
					}
					if ($file["link"] == ""){
						$file["link"] = '<img class="register-icons" data-src="'.$url_base. $file["icon"] .'" alt="'. $file["iconAlt"] .'" uk-tooltip="'. $file["iconAlt"] .'" uk-img>';
					}
	
					echo '<tr>';                                                                                                                                                                                                                                 echo '<td>'.$file["link"].'</td>';
                                        echo '<td>'.$file["file_name"].'</td>';
                                        echo '<td>'.$file["direct_link"].'</td>';
					
					/*foreach ($bitstreamsDSpace as $key => $value) {*/

					if (isset($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)){
				    echo '<td>'.$file["accountability_info"]["uploader"].'</td>';

                                    $botoes["excluir"]["acao"] = "Excluir";
                                    $botoes["excluir"]["icon"] = "excluir.svg";
                                    $botoes["excluir"]["modal_id"] = "deleteBitstream";
                                    $botoes["excluir"]["title"] = "Excluir arquivo";
                                    $botoes["excluir"]["mensagem"] = "excluir";

                                    $botoes["public"]["acao"] = "Tornar Público";
                                    $botoes["public"]["icon"] = "publico.svg";
                                    $botoes["public"]["modal_id"] = "makePublicBitstream";
                                    $botoes["public"]["title"] = "Tornar Público";
                                    $botoes["public"]["mensagem"] = "tornar público";

                                    $botoes["private"]["acao"] = "Tornar Privado";
                                    $botoes["private"]["icon"] = "privado.svg";
                                    $botoes["private"]["modal_id"] = "makePrivateBitstream";
                                    $botoes["private"]["title"] = "Tornar Privado";
                                    $botoes["private"]["mensagem"] = "tornar privado";

                                    $botoes["embargoed"]["acao"] = "Embargar";
                                    $botoes["embargoed"]["icon"] = "embargo.svg";
                                    $botoes["embargoed"]["modal_id"] = "doEmbargoBitstream";
                                    $botoes["embargoed"]["title"] = "Embargar";
                                    $botoes["embargoed"]["mensagem"] = "embargar";

                        				    foreach($botoes as $funcao => $botao){
                        					   $class_button = "register-icons";
                        					   if ($file["status"] == $funcao)
                        					    $class_button .= " desativado";
                        					   $img_icon = '<img class="'. $class_button .'" data-src="'. $url_base.'/inc/images/'. $botao["icon"] .'" alt="'. $botao["acao"] . '" uk-img>';
                        					   if ($file["status"] != $funcao)
                        					    $img_icon = '<a class="" type="button" uk-toggle="target: #modal-'. $botao["modal_id"] .'-'.$file["bitstream_id"].'" uk-tooltip="'. $botao["acao"] .'">' . $img_icon . '</a>' ;
                                        echo '<td>'. $img_icon .'</td>';
                                        echo '<div id="modal-'. $botao["modal_id"] .'-'.$file["bitstream_id"].'" uk-modal>';
                                        echo '  <div class="uk-modal-dialog">';
                                        echo '    <button class="uk-modal-close-default" type="button" uk-close></button>';
                                        echo '    <div class="uk-modal-header">';
                                        echo '      <h2 class="uk-modal-title">' . $botao["title"] . '</h2>';
                                        echo '    </div>';
                                        echo '    <div class="uk-modal-body">';
                                        echo '      <p>Tem certeza que quer ' . $botao["mensagem"] . " o arquivo " . $file["file_name"].'?</p>';
                                        echo '      <form action="' . $actual_link . '" method="post">';
					if($botao["acao"] == "Embargar"){
                                            echo '    <label for="releaseDate">Selecione a data em que o arquivo será liberado:</label>
                                                      <input type="date" name="releaseDate" required />';
                                        } //if($botao["acao"] == "Embargar")
                                        echo '        <input type="hidden" name="'. $botao["modal_id"] .'" value="'.$file["bitstream_id"].'" />';
					//if(!$botao["acao"] == "Excluir"){
                                            /*echo '    <input type="hidden" name="policyID" value="'.$bitstreamPolicyUnit["id"].'" />
                                                      <input type="hidden" name="policyAction" value="'.$bitstreamPolicyUnit["action"].'" />
                                                      <input type="hidden" name="policyGroupId" value="'.$bitstreamPolicyUnit["groupId"].'" />
                                                      <input type="hidden" name="policyResourceType" value="'.$bitstreamPolicyUnit["resourceType"].'" />
						      <input type="hidden" name="policyRpType" value="'.$bitstreamPolicyUnit["rpType"].'" />';*/

					  echo '    <input type="hidden" name="policyID" value="'.$file["bitstreamPolicy"]["id"].'" />
                                                      <input type="hidden" name="policyAction" value="'. $file["bitstreamPolicy"]["action"] .'" />
                                                      <input type="hidden" name="policyGroupId" value="'. $file["bitstreamPolicy"]["groupId"] .'" />
                                                      <input type="hidden" name="policyResourceType" value="'. $file["bitstreamPolicy"]["resourceType"] .'" />
						      <input type="hidden" name="policyRpType" value="'. $file["bitstreamPolicy"]["rpType"] .'" />';
                                        //} //if(!$botao["acao"] == "Excluir")
                                        echo '    </div>';
                                        echo '    <div class="uk-modal-footer uk-text-right">';
                                        echo '      <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>';
                                        echo '<button onclick="loadAction(\'modal-action\');" class="uk-button uk-button-danger" name="btn_submit" id="modal-action">'. $botao["acao"] .'</button>';
                                        echo '    </div>';
                                        echo '    </form>';
                                        echo '  </div>';
					echo '</div>';					
							    } //foreach($botoes as $botao)
                                  }
                                    echo '<td></td>';
                                }
			    //} //foreach ($bitstreamsDSpace as $key => $value)
			//} //foreach
                            echo '</tbody></table></div>';
                        } //if (!empty($bitstreamsDSpace))
                        ?>
                        <!-- Query bitstreams on Dspace - End -->

                        <!-- Citation - Start -->
                        <div class="uk-text-small" style="color:black;">
                            <h5><?=$t->gettext('Como citar')?></h5>
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
                        <h5><?=$t->gettext('Referências citadas na obra')?></h5>
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
                                            echo ''.$t->gettext("Título: ").''.$crossRefReference["journal-title"].'<br/>';
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
                        if (isset($cursor["_source"]["authorUSP"])) {
			    foreach ($cursor["_source"]["authorUSP"] as $authorUSPArray) {
				if(isset($authorUSPArray["codpes"])){
                                    $authorUSPArrayCodpes[] = $authorUSPArray["codpes"];
				}
                            }
			    if(isset($authorUSPArrayCodpes)){
			        $queryOtherWorks["query"]["bool"]["must"]["query_string"]["query"] = 'authorUSP.codpes:('.implode(" OR ", $authorUSPArrayCodpes).')';
			    }
                            $queryOtherWorks["query"]["bool"]["must_not"]["term"]["name.keyword"] = $cursor["_source"]["name"];
                            $resultOtherWorks = elasticsearch::elastic_search($type, ["_id","name"], 10, $queryOtherWorks);
                            echo '<div class="uk-alert-primary" uk-alert>';
                            echo '<h5>Últimas obras dos mesmos autores vinculados com a USP cadastradas na BDPI:</h5><ul>';
                            foreach ($resultOtherWorks["hits"]["hits"] as $othersTitles) {
                                //print_r($othersTitles);
                                echo '<li><a href="'.$url_base.'/item/'.$othersTitles["_id"].'" target="_blank">'.$othersTitles["_source"]["name"].'</a></li>';
                            }
                            echo '</ul></div>';
                        }
                        ?>
                        <!-- Other works of same authors - End -->

                </div>
            </div>
		
	<?php if(is_staffUser()): ?>
		<div id="modal-center-action" class="uk-flex-top" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-padding-large">
				<div class="uk-margin"><p class="uk-margin">Aguarde enquanto estamos processando a sua requisição...</p></div>
			</div>
			<div class="uk-position-center uk-margin uk-padding"><div uk-spinner></div></div>
		</div>
	<?php endif; ?>
            <hr class="uk-grid-divider">
            
        </div>
        <div style="position: relative; max-width: initial;">
	<?php if(is_staffUser()): ?>
		<script type="text/javascript">
			var uploadButton = document.getElementById('upload');
			uploadButton.disabled=true;
			uploadButton.setAttribute("disabled",true);
			uploadButton.classList.remove("uk-button-primary");
			function enableUploadButton(){
				var uploadButton = document.getElementById('upload');
				var repFileContent = document.getElementById('rep-file').value;
				var fileVersion = document.getElementById('file-version').value;
				if(repFileContent !== null && repFileContent !== '' && fileVersion !== null && fileVersion !== ''){
					uploadButton.disabled = false;
					uploadButton.removeAttribute("disabled");
					uploadButton.classList.add("uk-button-primary");
				}
				else{
					uploadButton.disabled = true;
					uploadButton.setAttribute("disabled",true);
					uploadButton.classList.remove("uk-button-primary");
				}
			};
			function loadAction(id){
				UIkit.modal('#modal-center-action', {
					escClose: false,
					bgClose: false
				}).show();
			};
			document.querySelector("input").addEventListener("keydown",function(e){
				var charCode = e.charCode || e.keyCode || e.which;
				if (charCode == 27){
					return false;
				}
			});
		</script>
	<?php endif; ?>
          <?php require 'inc/footer.php'; ?>
      </div>
        <?php require 'inc/offcanvas.php'; ?>

	<script async src="https://badge.dimensions.ai/badge.js" charset="utf-8"></script>

    </body>
</html>
