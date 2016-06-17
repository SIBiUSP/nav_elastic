#!/bin/bash
rm ../data/records.json
rm ../data/records.seq
sort $1 > ../data/records.seq
catmandu convert MARC --type ALEPHSEQ to JSON --line_delimited 1 < ../data/records.seq --fix ../fixes/fixes.txt >> ../data/records.json
catmandu import -v JSON --multiline 1 to sibi_elastic --bag producao < ../data/records.json 
