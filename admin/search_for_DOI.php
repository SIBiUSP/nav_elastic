<?php

// Set directory to ROOT
chdir('../');         
require 'inc/config.php'; 
require 'inc/functions.php';            
require 'inc/meta-header.php';

/* Consulta n registros ainda não corrigidos */
if (empty($_GET)) {
    $body["query"]["bool"]["must"]["query_string"]["query"] = "-_exists_:doi -_exists_:USP.titleSearchCrossrefDOI -_exists_:USP.titleSearchCrossrefDOInotFound";
} 
$body["query"]["bool"]["filter"]["term"]["type.keyword"] = "ARTIGO DE PERIODICO";

if (isset($_GET["sort"])) {        
    $body["sort"][$_GET["sort"]]["unmapped_type"] = "long";
    $body["sort"][$_GET["sort"]]["missing"] = "_last";
    $body["sort"][$_GET["sort"]]["order"] = "desc";
    $body["sort"][$_GET["sort"]]["mode"] = "max";
} else {
    $body['sort']['_uid']['order'] = "desc";
}

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["_source"] = ["_id","name","author"];
$params["size"] = 50;        
$params["body"] = $body;   

$response = $client->search($params);
            
echo 'Total de registros faltantes: '.$response['hits']['total'].'<br/><br/>';

foreach ($response["hits"]["hits"] as $r) {

    print_r($r);
    echo "<br/>";

    $title = rawurlencode($r["_source"]["name"]);
    $author = rawurlencode($r["_source"]["author"][0]["person"]["name"]);
    $json_url = 'https://api.crossref.org/works?rows=5&query.title='.$title.'&query.author='.$author.'&mailto=tiago.murakami@dt.sibi.usp.br';
    $json = file_get_contents($json_url);
    $data = json_decode($json, true);    
    
    //print_r($data["message"]);
    foreach ($data["message"]["items"] as $recordCrossref) {
        similar_text($recordCrossref["title"][0], $r["_source"]["name"], $percent);
        if ($percent > 95) {
            echo 'DOI encontrado: '.$recordCrossref["DOI"].'';
            $doiFound = $recordCrossref["DOI"];
        } 

    }
    if (isset($doiFound)) {
        $bodyUpdate["doc"]["USP"]["titleSearchCrossrefDOI"] = $doiFound;
        $bodyUpdate["doc_as_upsert"] = true;
        print_r($bodyUpdate);
        $resultado_crossref_update = elasticsearch::elastic_update($r["_id"], $type, $bodyUpdate);
        print_r($resultado_crossref_update);
    } else {
        $bodyNot["doc"]["USP"]["titleSearchCrossrefDOInotFound"] = true;
        $bodyNot["doc_as_upsert"] = true;
        $resultado_crossref_update_not = elasticsearch::elastic_update($r["_id"], $type, $bodyNot);
        print_r($resultado_crossref_update_not);
    }
    echo "<br/>";
    sleep(11);
    ob_flush();
    flush();
    unset($doiFound);
    unset($bodyUpdate);
    unset($percent);

}


       


?>