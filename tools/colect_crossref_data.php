<!DOCTYPE html>
<?php

    include('../inc/config.php'); 
    include('../inc/functions.php');

    $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.crossref"; 
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

    foreach ($cursor["hits"]["hits"] as $r) {

        $doi_data = query_doi($r["_source"]["doi"]);
        
        $body["doc"]["USP"]["crossref"] = $doi_data;
        $body["doc_as_upsert"] = true;
        $resultado_crossref = elasticsearch::store_record($r["_id"],$type,$body);
        print_r($resultado_crossref);
    }


    function query_doi ($doi) {
            global $client; 
            global $index;
            $url = "https://api.crossref.org/v1/works/http://dx.doi.org/$doi";
            $json = file_get_contents($url);
            $data = json_decode($json, TRUE);
        
            return ($data); 
        }   


?>