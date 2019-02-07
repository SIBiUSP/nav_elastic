<?php

// Set directory to ROOT
chdir('../');
// Include essencial files
include 'inc/config.php';
include 'inc/functions.php';

if ($handle = opendir('/var/www/html/bdpi/upload')) {

    /* Login in DSpace */
    $cookies = DSpaceREST::loginREST();

    while (false !== ($fileWork = readdir($handle))) {

        print_r($fileWork);

        $sysno = getSysno($fileWork);
        if ($sysno != "Invalid") {
          $cursor = elasticsearch::elastic_get($sysno, $type, null);

          /* Search for existing record on DSpace */
          $itemID = DSpaceREST::searchItemDSpace($cursor["_id"], $cookies);

          /* Verify if item exists on DSpace */
          if (empty($itemID)) {
            $dataString = DSpaceREST::buildDC($cursor, $sysno);
            $resultCreateItemDSpace = DSpaceREST::createItemDSpace($dataString, $dspaceCollection, $cookies);
            $userBitstream = 'publishedVersion - BulkUpload';

            /* Verify if item exists on DSpace again */
            $itemIDCreated = DSpaceREST::searchItemDSpace($cursor["_id"], $cookies);
            print_r($itemIDCreated);
            echo "<br/><br/>";

            /* Upload Bitstream */
            $file["file"]["name"] = $fileWork;
            $file["file"]["tmp_name"] = "/var/www/html/bdpi/upload/$fileWork";
            print_r($file);
            echo "<br/><br/>";



            $resultAddBitstream = DSpaceREST::addBitstreamDSpace($itemIDCreated, $file, $userBitstream, $cookies);
            print_r($resultAddBitstream);
            echo "<br/><br/>";

            /* Delete Annonymous Policy */
            //$resultDeleteBitstreamPolicyDSpace = DSpaceREST::deleteBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyID'], $cookies);
            /* Add Restricted Policy */
            //$resultAddBitstreamPolicyDSpace = DSpaceREST::addBitstreamPolicyDSpace($_POST['makePrivateBitstream'], $_POST['policyAction'], $dspaceRestrictedID, $_POST['policyResourceType'], $_POST['policyRpType'], $cookies);

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
