<!DOCTYPE html>
<?php

    include('inc/config.php'); 
    include('inc/functions.php');
    $query["query"]["query_string"]["query"] = "+_exists_:url -_exists_:USP.wikipedia";    
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

        foreach ($r["_source"]['relation'] as $url) {
            metrics::get_wikipedia(str_replace("https://","",str_replace("http://","",$url)));
        }       

        $body["doc"]["USP"]["wikipedia"] = $result;
        $body["doc"]["USP"]["wikipedia"]["data_coleta"] = date("Ymd");
        $body["doc_as_upsert"] = true;

        print_r($body);
        echo '<br/><br/>';

        sleep(5);


    }   
    



?>