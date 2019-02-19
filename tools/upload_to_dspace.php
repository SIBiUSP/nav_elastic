<?php

// Set directory to ROOT
chdir('../');
// Include essencial files
include 'inc/config.php';

if ($handle = opendir('/var/www/html/bdpi/upload')) {

    /* Login in DSpace */
    $DSpaceCookies = DSpaceREST::loginREST();

    while (false !== ($fileWork = readdir($handle))) {

        print_r($fileWork);

        $sysno = getSysno($fileWork);
        if ($sysno != "Invalid") {
          $cursor = elasticsearch::elastic_get($sysno, $type, null);

          /* Search for existing record on DSpace */
          $itemID = DSpaceREST::searchItemDSpace($cursor["_id"], $DSpaceCookies);

          /* Verify if item exists on DSpace */
          if (empty($itemID)) {
            $dataString = DSpaceREST::buildDC($cursor, $sysno);
            $resultCreateItemDSpace = DSpaceREST::createItemDSpace($dataString, $dspaceCollection, $DSpaceCookies);
            $userBitstream = 'publishedVersion - BulkUpload';

            /* Verify if item exists on DSpace again */
            $itemIDCreated = DSpaceREST::searchItemDSpace($cursor["_id"], $DSpaceCookies);
            print_r($itemIDCreated);
            echo "<br/><br/>";

            /* Upload Bitstream */
            $file["file"]["name"] = $fileWork;
            $file["file"]["tmp_name"] = "/var/www/html/bdpi/upload/$fileWork";
            print_r($file);
            echo "<br/><br/>";

            if (isset($_SESSION['oauthuserdata'])) {
                $uploadForm = '<form class="uk-form" action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <fieldset data-uk-margin>
                        <legend>Enviar um arquivo</legend>
                        <input type="file" name="file">
                        <select class="uk-select" name="version">
                            <option disabled selected value>Selecione a versão</option>
                            <option value="publishedVersion">publishedVersion</option>
                            <option value="submittedVersion">submittedVersion</option>
                            <option value="acceptedVersion">acceptedVersion</option>
                            <option value="updatedVersion">updatedVersion</option>
                        </select>
                        <input type="text" name="codpes" value="'.$_SESSION['oauthuserdata']->{'loginUsuario'}.'" hidden>
                        <button class="uk-button uk-button-primary" name="btn_submit">Upload</button>
                    </fieldset>
                    </form>';
            }            

            if (isset($_FILES['file'])) {
                $userBitstream = ''.$_POST["version"].'-'.$_POST["codpes"].'';
                echo "<br/><br/>";
                print_r($userBitstream);
                echo "<br/><br/>";
                $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemIDCreated, $_FILES, $userBitstream, $DSpaceCookies);
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



            $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemIDCreated, $file, $userBitstream, $DSpaceCookies);
            print_r($resultAddBitstream);
            echo "<br/><br/>";

            /* Delete Annonymous Policy */
            //$resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyID'], $DSpaceCookies);
            /* Add Restricted Policy */
            //$resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyAction'], $dspaceRestrictedID, $_POST['policyResourceType'], $_POST['policyRpType'], $DSpaceCookies);

          } else {
            Echo "Registro já existe";
          }

            exit();
        }


    }

    closedir($handle);
}

function getSysno ($fileWork) {

    preg_match('/(.*?)\.(.*)/', $fileWork, $sysnoArray);
    switch (strlen($sysnoArray[1])) {
      case 3:
          $sysno = '000000' . $sysnoArray[1];
          break;
      case 4:
          $sysno = '00000' . $sysnoArray[1];
          break;
      case 5:
          $sysno = '0000' . $sysnoArray[1];
          break;
      case 6:
          $sysno = '000' . $sysnoArray[1];
          break;
      case 7:
          $sysno = '00' . $sysnoArray[1];
          //echo "i equals 7";
          break;
      case 8:
          $sysno = '0' . $sysnoArray[1];
          break;
      case 9:
          $sysno = $sysnoArray[1];
          break;
      default:
          $sysno = "Invalid";
    }
    return $sysno;
}

?>
