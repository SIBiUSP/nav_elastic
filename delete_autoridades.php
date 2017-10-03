<?php
include('inc/config.php'); 
include('inc/functions.php');

$query["query"]["query_string"]["query"] = "+_exists_:USP.opencitation";    
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
print_r($total);
echo '<br/><br/>';


foreach ($cursor["hits"]["hits"] as $r) {

  unset($r["_source"]["USP"]["opencitation"]);

  //print_r($r["_source"]);

  $query_update["doc"] = $r["_source"];
  $query_update["doc_as_upsert"] = true;  

  elasticsearch::elastic_delete($r["_id"],$type); 
  $resultado = elasticsearch::elastic_update($r["_id"],$type,$query_update);  
  print_r($resultado);
  echo '<br/><br/>';

}

?>