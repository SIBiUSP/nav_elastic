<!DOCTYPE html>
<?php

require '../inc/config.php';
require '../inc/functions.php';

$query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.opencitation.coci";
$query['sort'] = [
    ['datePublished.keyword' => ['order' => 'desc']],
];

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["size"] = 10;
$params["body"] = $query;

$cursor = $client->search($params);
$total = $cursor["hits"]["total"];

echo 'Registros faltantes: '.$total.'';
echo '<br/><br/>';

foreach ($cursor["hits"]["hits"] as $r) {

    $jsonUrlReferences = 'http://opencitations.net/index/coci/api/v1/references/'.$r["_source"]["doi"].'';
    $jsonReferences = file_get_contents($jsonUrlReferences);
    $dataReferences = json_decode($jsonReferences, true);

    $jsonUrlCitations = 'http://opencitations.net/index/coci/api/v1/citations/'.$r["_source"]["doi"].'';
    $jsonCitations = file_get_contents($jsonUrlCitations);
    $dataCitations = json_decode($jsonCitations, true);

    if (!empty($dataReferences)) {
        $body["doc"]["USP"]["opencitation"]["coci"]["references"] = $dataReferences;
    } else {
        $body["doc"]["USP"]["opencitation"]["coci"]["references"]["not_found"] = true;
    }

    if (!empty($dataCitations)) {
        $body["doc"]["USP"]["opencitation"]["coci"]["citations"] = $dataCitations;
    } else {
        $body["doc"]["USP"]["opencitation"]["coci"]["citations"]["not_found"] = true;
    }
    $body["doc_as_upsert"] = true;
    $resultado_opencitation_references = elasticsearch::store_record($r["_id"], $type, $body);
    ob_flush();
    flush();
    unset($body);

}

?>
