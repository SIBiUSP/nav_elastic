<?php

require '../inc/config.php'; 

$query["query"]["query_string"]["query"] = "+_exists_:USP.citescore";    
$query['sort'] = [
    ['datePublished.keyword' => ['order' => 'desc']],
];      

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["size"] = 2500;
$params["body"] = $query;

$cursor = $client->search($params);
$total = $cursor["hits"]["total"];
print_r($total);
echo '<br/><br/>';

//print_r($cursor);


foreach ($cursor["hits"]["hits"] as $r) {

    $r["_source"]["USP"]["citescore"] = [];   

    $query_update["doc"] = $r["_source"];
    $query_update["doc_as_upsert"] = true; 

    //print_r($query_update);


    //$resultado = elasticsearch::elastic_update($r["_id"], $type, $query_update);  
    print_r($resultado);
    echo '<br/><br/>';

}

?>