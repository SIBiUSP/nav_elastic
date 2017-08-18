<!DOCTYPE html>
<?php

    include('inc/config.php'); 
    include('inc/functions.php');
    $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.scopus_api_data.valid_date";    
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
        $result = API::get_citations_elsevier($r["_source"]["doi"],$api_elsevier);   

        $body["doc"]["USP"]["scopus_api_data"] = $result;
        $body["doc"]["USP"]["scopus_api_data"]["valid_date"] = date("Ymd");
        $body["doc_as_upsert"] = true;

        print_r($body);
        echo '<br/><br/>';

        if (empty($body["doc"]["USP"]["scopus_api_data"]["service-error"])) {
            $result_update = elasticsearch:: elastic_update($r['_id'],$type,$body);
            print_r($result_update);
            echo '<br/><br/>';
        }

        sleep(5);


    }   
    



?>