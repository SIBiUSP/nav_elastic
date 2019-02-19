<!DOCTYPE html>
<?php

    require '../inc/config.php'; 

    $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.unpaywall";
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

    foreach ($cursor["hits"]["hits"] as $r) {

        $json_url = 'https://api.unpaywall.org/v2/'.$r["_source"]["doi"].'?email=tiago.murakami@dt.sibi.usp.br';
        $json = file_get_contents($json_url);
        $data = json_decode($json, true);

        if (!empty($data)) {
            $body["doc"]["USP"]["unpaywall"] = $data;
            $body["doc_as_upsert"] = true;
            $resultado_crossref = elasticsearch::store_record($r["_id"], $type, $body);
            print_r($resultado_crossref);
            ob_flush();
            flush();             
        } else {
            echo "Não encontrado";
            $body["doc"]["USP"]["unpaywall"]["not_found"] = true;
            $body["doc_as_upsert"] = true;
            $resultado_crossref = elasticsearch::store_record($r["_id"], $type, $body);
            print_r($resultado_crossref);
            ob_flush();
            flush();
        }
    }

?>
