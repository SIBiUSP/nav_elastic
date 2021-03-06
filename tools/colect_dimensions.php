<!DOCTYPE html>
<?php

    require '../inc/config.php'; 

    $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.dimensions"; 
    $query['sort'] = [
        ['datePublished.keyword' => ['order' => 'desc']],
    ];    

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = 20;
    $params["body"] = $query; 

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];

    echo 'Quantidade de registros restantes: '.($total - $params["size"]).'';
    echo '<br/><br/>';

    foreach ($cursor["hits"]["hits"] as $r) {

        $dimensionsData = API::dimensionsAPI($r["_source"]["doi"]);
        
        $body["doc"]["USP"]["dimensions"] = $dimensionsData;
        $body["doc"]["USP"]["dimensions"]["date"] = date("Ymd");
        $body["doc_as_upsert"] = true;      
        $resultado_dimensions = elasticsearch::store_record($r["_id"], $type, $body);
        print_r($resultado_dimensions);
    }
?>
