#!/bin/bash

id=$(curl -XGET 'http://localhost:9200/sibi/producao/_search?pretty=true&fields=_id' -d '
{
  "query": { 
    "match_all": {} 
    },
    "size":5000,
    "from":1    
}' | jq '.hits .hits[] ._id' | sed 's/\"//g') 
    
#IFS=$'\s'       # make newlines the only separator
for line in $id;
    do
        echo "http://bdpi.usp.br/single.php?_id=$line" >> ../data/sitemap_1_5000.txt
    done