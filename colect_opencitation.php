<html>
  <body>
 
  <?php
  /* ARC2 static class inclusion */
  include('inc/config.php'); 
  include('inc/functions.php'); 

  $query["query"]["query_string"]["query"] = "+_exists_:doi -_exists_:USP.opencitation";    
  $query['sort'] = [
      ['datePublished.keyword' => ['order' => 'desc']],
  ];      

  $params = [];
  $params["index"] = $index;
  $params["type"] = $type;
  $params["size"] = 50;
  $params["body"] = $query;

  $cursor = $client->search($params);
  $total = $cursor["hits"]["total"];

  echo 'Registros faltantes: '.$total.'';
  echo '<br/><br/>';

  foreach ($cursor["hits"]["hits"] as $r) {

      $result = API::get_opencitation($r["_source"]['doi']);
      $i = 0;
      if (!empty($result)) {
        foreach ($result as $record) {
          $body["doc"]["USP"]["opencitation"]["citation"][$i]["citing"] = (string)$record->citing;
          $body["doc"]["USP"]["opencitation"]["citation"][$i]["title"] = (string)$record->title;
          $i++;
        }
      }    

      $body["doc"]["USP"]["opencitation"]["date"] = date("Ymd");
      $body["doc_as_upsert"] = true;
      print_r($body);
      echo '<br/>';      
      $resultado_opencitation = elasticsearch::store_record($r["_id"],$type,$body);
      print_r($resultado_opencitation);
      echo '<br/><br/>';

      flush();

  }  

  ?>
  </body>
</html>