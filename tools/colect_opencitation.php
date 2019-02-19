<html>
  <body>
 
<?php
/* ARC2 static class inclusion */
require '../inc/config.php';

$query["query"]["query_string"]["query"] = "-_exists_:USP.opencitation";    
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

echo 'Registros faltantes: '.$total.'';
echo '<br/><br/>';

foreach ($cursor["hits"]["hits"] as $r) {

    echo 'Sysno: '.$r["_id"].'';

    if (isset($r["_source"]['doi'])) {
        $result = metrics::get_opencitation_doi($r["_source"]['doi']);
        $i = 0;
        if (!empty($result)) {
            foreach ($result as $record) {
                print_r($record);
                $body["doc"]["USP"]["opencitation"]["citation"][$i]["citing"] = (string)$record->citing;
                $body["doc"]["USP"]["opencitation"]["citation"][$i]["title"] = (string)$record->title;
                $i++;
            }
            $body["doc"]["USP"]["opencitation"]["num_citations"] = count($result);
        }    

        $body["doc"]["USP"]["opencitation"]["date"] = date("Ymd");
        $body["doc"]["USP"]["opencitation"]["match"] = "doi";
        $body["doc_as_upsert"] = true;
        print_r($body);
        echo '<br/>';      
        $resultado_opencitation = elasticsearch::store_record($r["_id"], $type, $body);
        print_r($resultado_opencitation);
        unset($body);
        unset($result);
        echo '<br/><br/>';

        flush();
    } else {
        $result = API::get_opencitation_title(htmlspecialchars($r["_source"]['name']));
        $i = 0;
        if (!empty($result)) {
            foreach ($result as $record) {
                print_r($record);
                $body["doc"]["USP"]["opencitation"]["citation"][$i]["citing"] = (string)$record->citing;
                $body["doc"]["USP"]["opencitation"]["citation"][$i]["title"] = (string)$record->title;
                $i++;
            }
            $body["doc"]["USP"]["opencitation"]["num_citations"] = count($result);
        }    

        $body["doc"]["USP"]["opencitation"]["date"] = date("Ymd");
        $body["doc"]["USP"]["opencitation"]["match"] = "title";
        $body["doc_as_upsert"] = true;
        print_r($body);
        echo '<br/>';      
        $resultado_opencitation = elasticsearch::store_record($r["_id"], $type, $body);
        print_r($resultado_opencitation);
        unset($body);
        unset($result);
        echo '<br/><br/>';

        flush();        

    }



}  

?>
  </body>
</html>