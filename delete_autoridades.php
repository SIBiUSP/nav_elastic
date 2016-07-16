<?php

include('inc/functions.php');

$query='
{
  "fields" : ["_id"],
  "query": {
    "filtered": {
      "query": {
        "match_all": {}
      },
      "filter": {
        "bool": {
          "must": [
                {
                "term":{
                    "colab_instituicao_tematres":true
                }
            }
          ],
          "must_not": [
             {"term":
                {"colab_instituicao_corrigido" : ""}
             }
          ]
        }
      }
    }
  },
  "size":10000
}
';

$cursor = query_elastic($query);


/*
$query_delete = '
{
    "doc" : {
        "colab_instituicao_tematres" : false
    }
}
';

*/


$query_delete = '
{
    "doc" : {
        "colab_instituicao_corrigido" : "",
        "colab_instituicao_naocorrigido" : "",
        "colab_instituicao_geocode" : "",
        "colab_instituicao_tematres" : false
    }
}
';



foreach ($cursor["hits"]["hits"] as $colab) {
    update_elastic($colab["_id"],$query_delete);
}



?>