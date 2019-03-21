<?php

chdir('../');
require 'inc/config.php';

$folioCookies = FolioREST::loginREST();

# Harvester All

$offset = 0;
$limit = 100;
$query = null;

$harvestResult = FolioREST::queryInstances($folioCookies, $offset, $limit, $query);

echo 'Total de registros: '.$harvestResult["totalRecords"].'<br/><br/>';

foreach ($harvestResult["instances"] as $instance) {
    $recordArray = FolioREST::folioCodexToElasticSchemaOrg($instance);
    $updateResult = elasticsearch::elastic_update($instance["id"], $type, $recordArray);
    print_r($updateResult);
    echo "<br/>"; 
}

while (isset($harvestResult["instances"]) && count($harvestResult["instances"]) > 0) {
    $offset = $offset + $limit;
    $harvestResult = FolioREST::queryInstances($folioCookies, $offset, $limit, $query);

    foreach ($harvestResult["instances"] as $instance) {
        $recordArray = FolioREST::folioCodexToElasticSchemaOrg($instance);
        $updateResult = elasticsearch::elastic_update($instance["id"], $type, $recordArray);
        print_r($updateResult);
        echo "<br/>";                
    }    

}







?>