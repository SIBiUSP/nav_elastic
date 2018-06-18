<!DOCTYPE html>
<?php

    require '../inc/config.php'; 
    require '../inc/functions.php';

    $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.crossref";
    $query['sort'] = [
        ['datePublished.keyword' => ['order' => 'desc']],
    ];    

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = 1000;
    $params["body"] = $query; 

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];

    echo "Registros restantes: $total<br/><br/>";

    foreach ($cursor["hits"]["hits"] as $r) {

        $clientCrossref = new RenanBr\CrossRefClient();
        $clientCrossref->setUserAgent('GroovyBib/1.1 (https://bdpi.usp.br/; mailto:tiago.murakami@dt.sibi.usp.br)');
        $work = $clientCrossref->request('works/'.$r["_source"]["doi"].'');
        echo "<br/><br/><br/><br/>";
        $body["doc"]["USP"]["crossref"] = $work;
        $body["doc_as_upsert"] = true;
        $resultado_crossref = elasticsearch::store_record($r["_id"], $type, $body);
        print_r($resultado_crossref);
        sleep(25);
    }

?>
