<?php 
include 'inc/config.php';
include 'inc/functions.php';

$campo = "unidadeUSP";
$consulta = 'FM';


$query_unidade = '
{
	"query": {
		"query_string": {
			"default_field": "'.$campo.'",
			"query": "'.$consulta.'"
		}
	},
	"controls": {
		"use_significance": true,
		"sample_size": 500000,
		"timeout": 10000
	},
	"connections": {
		"vertices": [
			{
				"field": "'.$campo.'",
				"size": 500000,
				"min_doc_count": 1
			}
		]
	},
	"vertices": [
		{
			"field": "'.$campo.'",
			"size": 10,
			"min_doc_count": 1
		}
	]
}
';


$cursor = query_graph($query_unidade,$server);

print_r($cursor);

$node_counter = 0;
foreach ($cursor["vertices"] as $nodes) {    
    $node[] = '{
        "id":"'.$node_counter.'",
        "label":"'.$nodes["term"].'",
        "x":'.rand().',
        "y":'.rand().',
        "size":10
    }';
    $node_counter++;
}

$edge_counter = 0;
foreach ($cursor["connections"] as $edges) {
    $edge[] = '{
        "id":"'.$edge_counter.'",
        "source":"'.$edges["source"].'",
        "target":"'.$edges["target"].'"
    }';
    $edge_counter++;
}


$data_json = '
{
  "nodes": [
    '.implode(",",$node).'    
  ],
  "edges": [
    '.implode(",",$edge).'  
  ]
}

';

print_r($data_json);

?>