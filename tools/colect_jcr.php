<!DOCTYPE html>
<?php

    require '../inc/config.php'; 

    $query["query"]["query_string"]["query"] = "-_exists_:USP.JCR";    
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

    $hostsJCR = ['172.31.1.186'];
    $clientJCR = \Elasticsearch\ClientBuilder::create()->setHosts($hostsJCR)->build(); 


    $paramsJCR = [];
    $paramsJCR["index"] = "serial_jcr";
    $paramsJCR["type"] = "JCR";
    $paramsJCR["size"] = 100;
    


    foreach ($cursor["hits"]["hits"] as $r) {

        if (isset($r["_source"]["isPartOf"]["issn"])) {

            echo '<br/>Tem ISSN: '.$r["_source"]["isPartOf"]["issn"][0].'<br/>';
            $queryJCR["query"]["bool"]["filter"]["term"]["issn.keyword"] = $r["_source"]["isPartOf"]["issn"][0];
            $paramsJCR["body"] = $queryJCR;
            $cursorJCR = $clientJCR->search($paramsJCR);
            print_r($cursorJCR);            
            if ($cursorJCR["hits"]["total"] == 1) {

                $bodyUpdateJCR["doc"]["USP"]["JCR"] = $cursorJCR["hits"]["hits"][0]["_source"];
                $bodyUpdateJCR["doc_as_upsert"] = true;
                //print_r($bodyUpdateJCR);
                $resultUpdateJCR = elasticsearch::store_record($r["_id"], $type, $bodyUpdateJCR);
                //print_r($resultUpdateJCR);
                unset($bodyUpdateJCR); 

            } else {

                $bodyUpdateJCR["doc"]["USP"]["JCR"]["issn_not_found"] = true;
                $bodyUpdateJCR["doc_as_upsert"] = true;
                //print_r($bodyUpdateJCR);
                $resultUpdateJCR = elasticsearch::store_record($r["_id"], $type, $bodyUpdateJCR);
                //print_r($resultUpdateJCR);
                unset($bodyUpdateJCR);                

            }

        } elseif (isset($r["_source"]["isPartOf"]["name"])) {

            $queryJCRTitle["query"]["bool"]["filter"]["term"]["title.keyword"] = $r["_source"]["isPartOf"]["name"];
            $paramsJCRTitle["body"] = $queryJCRTitle;
            $cursorJCRTitle = $clientJCR->search($paramsJCRTitle);

            if ($cursorJCRTitle["hits"]["total"] == 1) {

                $bodyUpdateJCR["doc"]["USP"]["JCR"] = $cursorJCRTitle["hits"]["hits"][0]["_source"];
                $bodyUpdateJCR["doc_as_upsert"] = true;
                //print_r($bodyUpdateJCR);
                $resultUpdateJCR = elasticsearch::store_record($r["_id"], $type, $bodyUpdateJCR);
                //print_r($resultUpdateJCR);
                unset($bodyUpdateJCR);   

            } else {
            
                $bodyUpdateJCR["doc"]["USP"]["JCR"]["issn_not_found"] = true;
                $bodyUpdateJCR["doc_as_upsert"] = true;
                //print_r($bodyUpdateJCR);
                $resultUpdateJCR = elasticsearch::store_record($r["_id"], $type, $bodyUpdateJCR);
                //print_r($resultUpdateJCR);
                unset($bodyUpdateJCR);  

            }

            echo '<br/>Tem só TITULO: '.$r["_source"]["isPartOf"]["name"].'<br/>';

        } else {

            $bodyUpdateJCR["doc"]["USP"]["JCR"]["not_found"] = true;
            $bodyUpdateJCR["doc_as_upsert"] = true;
            //print_r($bodyUpdateJCR);
            $resultUpdateJCR = elasticsearch::store_record($r["_id"], $type, $bodyUpdateJCR);
            //print_r($resultUpdateJCR);
            unset($bodyUpdateJCR);

        }




       
    }

?>
