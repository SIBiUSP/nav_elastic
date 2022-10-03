<?php

chdir('../');
require 'inc/config.php';


$oaiUrl = $oaiDspace;
$client_harvester = new \Phpoaipmh\Client(''.$oaiUrl.'');
$myEndpoint = new \Phpoaipmh\Endpoint($client_harvester);

$recs = $myEndpoint->listRecords('xoai', null, null, "col_BDPI_4");

foreach ($recs as $rec) {

    $json = json_encode($rec);
    $array = json_decode($json, true);
    foreach ($array["metadata"]["metadata"]["element"]as $element) {
		switch ($element["@attributes"]["name"]) {
			case "usp":
				$sysno = $element["element"][0]["element"]["field"];
				$itemID = DSpaceREST::searchItemDSpace($sysno, $_SESSION["DSpaceCookies"]);
				$bitstreamsDSpace = DSpaceREST::getBitstreamDSpace($itemID, $_SESSION["DSpaceCookies"]);
				$cursor = elasticsearch::elastic_get($sysno, $type, null);
				if (!empty($cursor["_source"]["files"]["database"])) {
					if (count($bitstreamsDSpace) != count($cursor["_source"]["files"]["database"])) {
						$body["doc"]["files"]["database"] =  $bitstreamsDSpace;
						$resultUpdateFilesElastic = elasticsearch::elastic_update($sysno, $type, $body);
						var_dump($resultUpdateFilesElastic);
						unset($body);
					}
				} elseif (!empty($bitstreamsDSpace)) {
					$body["doc"]["files"]["database"] =  $bitstreamsDSpace;
					$resultUpdateFilesElastic = elasticsearch::elastic_update($sysno, $type, $body);
					var_dump($resultUpdateFilesElastic);
					unset($body);
				}
			break;
		}
    }
}


?>
