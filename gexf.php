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
        include('inc/config.php'); 
        include('inc/functions.php');

        $result_get = get::analisa_get($_GET);
        $query = $result_get['query'];  
        $limit = 1000;
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

        $cursor = $client->search($params);
        $total = $cursor["hits"]["total"];
        
        gexf($field,1000,null,"_term",$query);

        //print_r($params);

        function gexf($field,$size,$sort,$sort_type,$get_search) {
            global $type;
            $query = $get_search;
            $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
            $query["aggs"]["counts"]["terms"]["missing"] = "Não preenchido";
            if (isset($sort)) {
                $query["aggs"]["counts"]["terms"]["order"][$sort_type] = $sort;
            }
            $query["aggs"]["counts"]["terms"]["size"] = $size;
            
            $response = elasticsearch::elastic_search($type,null,0,$query);
        
            $result_count = count($response["aggregations"]["counts"]["buckets"]);        
            
            if ($result_count == 0) {             

            } else {
                echo '<nodes>';
                $i = 0;
                foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {                    
                    //print_r($facets);
                    echo '<node id="'.sha1($facets['key']).'" label="'.$facets['key'].'">
                            <viz:size value="10.0"></viz:size>
                            <viz:position x="'.mt_rand(1,500).'" y="'.mt_rand(1,500).'"></viz:position>
                        </node>';

                    
                    if ($i == 0) {
                        // Pega o central
                        $central_string = $facets['key'];
                        $central = sha1($facets['key']);
                    } else {
                        // Joga os outros no array
                        $edges[] = array($central, sha1($facets['key']),$facets['doc_count']);

                        // Nova consulta para formar a rede
                        $query["query"]["query_string"]["query"] = $get_search["query"]["query_string"]["query"] . " " . str_replace($central_string,$facets['key'],$get_search["query"]["query_string"]["query"]);
                        $response_network = elasticsearch::elastic_search($type,null,0,$query);

                        $i_network = 0;                        
                        foreach ($response_network["aggregations"]["counts"]["buckets"] as $facets_network) {
                            if ($i_network == 0) {
                                $central_n_string = $facets_network['key'];
                                $central_n = sha1($facets_network['key']);
                            } else{
                                $edges[] = array($central_n, sha1($facets_network['key']),$facets_network['doc_count']);
                            }
                            $i_network++;
                        }                        
                    }
                    $i++;
                }
                echo '</nodes>';
                echo '<edges>';
                
                $edges_unique = array_unique($edges,SORT_REGULAR);
                $i_edge = 0;
                foreach ($edges_unique as $edge) {
                    echo '<edge id="'.$i_edge.'" source="'.$edge[0].'" target="'.$edge[1].'" weight="'.$edge[2].'.0"></edge>';
                    $i_edge++;
                }
                echo '</edges>';
                
        
            }
        }
    ?>
    </graph>
</gexf>
