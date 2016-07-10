#!/bin/bash

count=1
while [ $count -lt 2 ]
do

id=$(curl -XGET 'http://localhost:9200/sibi/producao/_search?pretty=true&fields=_id' -d '
{
  "query": {
    "bool": {
      "must_not": {
        "exists": {
          "field": "colab_instituicao_corrigido"
        }
      },
      "must": {
           "exists" : { "field" : "colab_instituicao_trab" }
      }
    }
  },
  "size":1
}' | jq '.hits .hits[] ._id' | sed 's/\"//g') 


url="http://bdpife2.sibi.usp.br/nav_elastic/autoridades.php?_id=$id"

curl -s -G -L $url

count=`expr $count + 1`
echo $count
done