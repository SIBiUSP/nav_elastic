#!/bin/bash
#rm ../data/records.json
#catmandu convert MARC --type ALEPHSEQ to JSON --line_delimited 1 < ../data/records.seq --fix ../fixes/fixes.txt >> ../data/records.json
#catmandu import -v JSON --multiline 1 to sibi_elastic --bag producao < ../data/records.json 
#
catmandu import -v MARC --type ALEPHSEQ --fix ../fixes/fixes.txt to sibi_elastic -bag producao < ../data/records.seq  

while [ -f $1 ]
do

#Pegar o ID
id=$(head -n 1 $1 | grep -o '^.........')
#Gerar o Aleph Sequencial
grep "^$id" $1 >> ../data/output.seq

#Converter e importar no ElasticSearch
echo '{ "index" : { "_index" : "sibi", "_type" : "producao", "_id" : "'$id'" } }' >> ../data/records.json
grep "^$id" $1 | catmandu convert MARC --type ALEPHSEQ --fix ../fixes/fixes.txt to JSON | jq -c '.[0]' >> ../data/records.json

#| curl -XPUT "http://localhost:9200/sibi/producao/$id" -d @- 2>&1

#Deletar o Aleph Sequencial
sed -i '/'^$id'/d' $1 

done


#curl -s -XPOST localhost:9200/_bulk --data-binary @../data/records.json; echo