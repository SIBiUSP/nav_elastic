<!DOCTYPE html>
<?php
    // Set directory to ROOT
    chdir('../');
    // Include essencial files
    include('inc/config.php'); 

    $query["query"]["query_string"]["query"] = "-_exists_:itens_date";    
    $query['sort'] = [
        ['datePublished.keyword' => ['order' => 'desc']],
    ];      

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = 100;
    $params["body"] = $query;

    $cursor = $client->search($params);
    $total = $cursor["hits"]["total"];

    echo 'Registros faltantes: '.$total.'';
    echo '<br/><br/>';

    foreach ($cursor["hits"]["hits"] as $r) {

        itens_aleph($r["_id"]);

    }


    /* Recupera os exemplares do DEDALUS */
    function itens_aleph ($sysno) {
        global $type;
        $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
        if ($xml->error == "No associated items"){
            $query["doc"]["itens_date"] = date("Ymd");
            $resultado_upsert = elasticsearch::elastic_update($sysno,$type,$query);
        } else {

            $i = 0;        
            foreach ($xml->item as $item) {

                $query["doc"]["itens"][$i]["sub-library"] = (string)$item->{'sub-library'};
                $query["doc"]["itens"][$i]["barcode"] = (string)$item->{'barcode'};
                $query["doc"]["itens"][$i]["item-status"] = (string)$item->{'item-status'};
                $query["doc"]["itens"][$i]["call-no-1"] = (string)$item->{'call-no-1'};
                if ($item->{'loan-status'} == "A"){
                    $query["doc"]["itens"][$i]["loan-status"] = "Emprestado";
                } else {
                    $query["doc"]["itens"][$i]["loan-status"] = "Disponível";
                }
                $query["doc"]["itens_date"] = date("Ymd");

                $query["doc_as_upsert"] = true;
                print_r($query);
                $resultado_upsert = elasticsearch::elastic_update($sysno,$type,$query);
                print_r($resultado_upsert);
                echo '<br/><br/>';

                $i++;
            }

        }
    }
    

?>