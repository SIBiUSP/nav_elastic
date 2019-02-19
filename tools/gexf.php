<?php 
    header('Content-type: application/xml');
    //header('Content-disposition: attachment; filename="bdpi.gexf"'); 
?>
<?xml version="1.0" encoding="UTF-8"?>
<gexf xmlns="http://www.gexf.net/1.3" version="1.3" xmlns:viz="http://www.gexf.net/1.3/viz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.gexf.net/1.3 http://www.gexf.net/1.3/gexf.xsd">
    <meta lastmodifieddate="<?php echo date("Y-m-d"); ?>">
        <creator>BDPI USP</creator>
        <description></description>
    </meta>
    <graph defaultedgetype="undirected" mode="static">
    <?php
    require '../inc/config.php'; 

    $result_get = get::analisa_get($_GET);
    $query = $result_get['query'];  
    $limit = 500;
    $page = $result_get['page'];
    $skip = $result_get['skip'];

    if (isset($_GET["sort"])) {        
        $query["sort"][$_GET["sort"]]["unmapped_type"] = "long";
        $query["sort"][$_GET["sort"]]["missing"] = "_last";
        $query["sort"][$_GET["sort"]]["order"] = "desc";
        $query["sort"][$_GET["sort"]]["mode"] = "max";
    } else {

        $query['sort']['datePublished.keyword']['order'] = "desc";
    }

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = $limit;
    $params["from"] = $skip;
    $params["body"] = $query; 

    $field = $_GET["gexf_field"];
    
    gexf($field, 1000, null, "_term", $query);

        //print_r($params);

    function gexf($field,$size,$sort,$sort_type,$get_search = "")
    {
        global $type;
        $query = $get_search;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        $query["aggs"]["counts"]["terms"]["size"] = $size;
        
        $response = elasticsearch::elastic_search($type, null, 0, $query);
        //print_r($response);

        $result_count = count($response["aggregations"]["counts"]["buckets"]);  
        
        if ($result_count == 0) {             

        } else {
            echo '<nodes>';
            $i = 0;
            if ($field == "author.person.affiliation.name") {
                echo '<node id="'.crc32("Universidade de São Paulo").'" label="Universidade de São Paulo">
                <viz:size value="10.0"></viz:size>
                <viz:position x="'.mt_rand(-500, 500).'" y="'.mt_rand(-500, 500).'"></viz:position>
                <viz:color r="'.mt_rand(0, 255).'" g="'.mt_rand(0, 255).'" b="'.mt_rand(0, 255).'"></viz:color>
            </node>';
            }
            $i_network = 0;
            foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {

                if ($field == "author.person.affiliation.name") {
                    $mainArray[] = '<edge id="'.$i_network.'" source="'.crc32("Universidade de São Paulo").'" target="'.(string)crc32($facets['key']).'" weight="'.(string)$facets['doc_count'].'.0"></edge>
                    ';
                }                 

                // Pega todas as facetas e joga como nó
                echo '<node id="'.(string)crc32($facets['key']).'" label="'.$facets['key'].'">
                        <viz:size value="10.0"></viz:size>
                        <viz:position x="'.mt_rand(-500, 500).'" y="'.mt_rand(-500, 500).'"></viz:position>
                        <viz:color r="'.mt_rand(0, 255).'" g="'.mt_rand(0, 255).'" b="'.mt_rand(0, 255).'"></viz:color>
                    </node>';  

                // Consulta para formar os edges
                $query_n = $query;
                $query_n["query"]["bool"]["must"]["query_string"]["query"] = $get_search["query"]["bool"]["must"]["query_string"]["query"] . " +" . $field . ":\"" . $facets['key'] ."\"";
                $response_network = elasticsearch::elastic_search($type, null, 0, $query_n);

                foreach ($response_network["aggregations"]["counts"]["buckets"] as $facets_network) { 
                                                
                    $central_n_string = $facets['key'];
                    if ($facets['key'] != $facets_network['key']) {
                        $array =  array((string)crc32($facets['key']), (string)crc32($facets_network['key']), (string)$facets_network['doc_count']);
                        $array[] = sort($array);
                        $array = array_values($array);
                        $edges[] = $array;
                    }                        
                    
                    $i_network++;
                }

                $i++;
            }
            echo '</nodes>
            <edges>';

            if ($field == "author.person.affiliation.name") {
                $affiliation = implode("", $mainArray);
                echo $affiliation;
            }

            echo '
            ';
            //$edges_unique = array_map('unserialize', array_unique(array_map('serialize', $edges)));
            //print_r($edges_check); 
            $edges_unique = array_unique($edges, SORT_REGULAR);
            //$edges_unique_new = array_unique($edges_unique);
            //$edges_unique = array_diff_key($edges_unique, $edges);
            
            $i_edge = $i_network;

            foreach ($edges_unique as $edge) {
                echo '<edge id="'.$i_edge.'" source="'.$edge[2].'" target="'.$edge[1].'" weight="'.$edge[0].'.0"></edge>
                ';
                $i_edge++;
            }

            echo '</edges>
            ';
            

        }
    }
    ?>
    </graph>
</gexf>
