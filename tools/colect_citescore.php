<!DOCTYPE html>
<?php

    require '../inc/config.php';

    $query["query"]["query_string"]["query"] = "-_exists_:USP.citescore";    
    $query['sort'] = [
        ['datePublished.keyword' => ['order' => 'desc']],
    ];    

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = 500;
    $params["body"] = $query;

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];

    echo "Registros restantes: $total<br/><br/>";

    $hostsCitescore = ['172.31.1.186'];
    $clientCitescore = \Elasticsearch\ClientBuilder::create()->setHosts($hostsCitescore)->build(); 


    $paramsCitescore = [];
    $paramsCitescore["index"] = "citescore";
    $paramsCitescore["type"] = "citescore";
    $paramsCitescore["size"] = 100;
    


    foreach ($cursor["hits"]["hits"] as $r) {

        if (isset($r["_source"]["isPartOf"]["issn"])) {

            echo '<br/>Tem ISSN: '.$r["_source"]["isPartOf"]["issn"][0].'<br/>';
            $queryCitescore["query"]["bool"]["filter"]["term"]["issn.keyword"] = $r["_source"]["isPartOf"]["issn"][0];
            $paramsCitescore["body"] = $queryCitescore;
            $cursorCitescore = $clientCitescore->search($paramsCitescore);            
            if ($cursorCitescore["hits"]["total"] == 1) {

                $bodyUpdateCitescore["doc"]["USP"]["citescore"] = $cursorCitescore["hits"]["hits"][0]["_source"];
                $bodyUpdateCitescore["doc_as_upsert"] = true;
                $resultUpdateCitescore = elasticsearch::store_record($r["_id"], $type, $bodyUpdateCitescore);
                print_r($resultUpdateCitescore);
                unset($bodyUpdateCitescore); 

            } else {

                $bodyUpdateCitescore["doc"]["USP"]["citescore"]["issn_not_found"] = true;
                $bodyUpdateCitescore["doc_as_upsert"] = true;
                print_r($bodyUpdateCitescore);
                $resultUpdateCitescore = elasticsearch::store_record($r["_id"], $type, $bodyUpdateCitescore);
                print_r($resultUpdateCitescore);
                unset($bodyUpdateCitescore);                

            }

        } elseif (isset($r["_source"]["isPartOf"]["name"])) {

            $queryCitescoreTitle["query"]["bool"]["filter"]["term"]["title.keyword"] = $r["_source"]["isPartOf"]["name"];
            $paramsCitescoreTitle["body"] = $queryCitescoreTitle;
            $cursorCitescoreTitle = $clientCitescore->search($paramsCitescoreTitle);

            if ($cursorCitescoreTitle["hits"]["total"] == 1) {

                $bodyUpdateCitescore["doc"]["USP"]["citescore"] = $cursorCitescoreTitle["hits"]["hits"][0]["_source"];
                $bodyUpdateCitescore["doc_as_upsert"] = true;
                $resultUpdateCitescore = elasticsearch::store_record($r["_id"], $type, $bodyUpdateCitescore);
                print_r($resultUpdateCitescore);
                unset($bodyUpdateCitescore);   

            } else {
            
                $bodyUpdateCitescore["doc"]["USP"]["citescore"]["issn_not_found"] = true;
                $bodyUpdateCitescore["doc_as_upsert"] = true;
                $resultUpdateCitescore = elasticsearch::store_record($r["_id"], $type, $bodyUpdateCitescore);
                print_r($resultUpdateCitescore);
                unset($bodyUpdateCitescore);  

            }

            echo '<br/>Tem só TITULO: '.$r["_source"]["isPartOf"]["name"].'<br/>';

        } else {

            $bodyUpdateCitescore["doc"]["USP"]["citescore"]["not_found"] = true;
            $bodyUpdateCitescore["doc_as_upsert"] = true;
            print_r($bodyUpdateCitescore);
            $resultUpdateCitescore = elasticsearch::store_record($r["_id"], $type, $bodyUpdateCitescore);
            print_r($resultUpdateCitescore);
            unset($bodyUpdateCitescore);

        }




       
    }

?>
