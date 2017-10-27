<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            // Set directory to ROOT
            chdir('../');         
            include('inc/config.php'); 
            include('inc/functions.php');            
            include('inc/meta-header.php');

            /* Consulta n registros ainda não corrigidos */
            if (empty($_GET)) {
                $body["query"]["query_string"]["query"] = "+_exists_:author.person.affiliation.name_not_found";
            } 

            if (isset($_GET["sort"])) {        
                $body["sort"][$_GET["sort"]]["unmapped_type"] = "long";
                $body["sort"][$_GET["sort"]]["missing"] = "_last";
                $body["sort"][$_GET["sort"]]["order"] = "desc";
                $body["sort"][$_GET["sort"]]["mode"] = "max";
            } else {
                //$query['sort']['facebook.facebook_total']['order'] = "desc";
                $body['sort']['_uid']['order'] = "desc";
            }                

            $params = [];
            $params["index"] = $index;
            $params["type"] = $type;
            $params["_source"] = ["_id","author"];
            $params["size"] = 10;        
            $params["body"] = $body;   

            $response = $client->search($params);
                
            echo 'Total de registros faltantes: '.$response['hits']['total'].'';
        
        ?> 
        <title>Autoridades</title>
    </head>
    <body> 
        
        <div class="uk-container uk-container-center uk-margin-large-bottom">
            
            
                <?php 
            
                    // Pega cada um dos registros da resposta
                    foreach ($response["hits"]["hits"] as $registro) {   
                        
                        $i = 0;                        
                        // Para cada autor no registro
                        foreach ($registro['_source']['author'] as $autor) {
                            
                                if (isset($autor["person"]["affiliation"]["name_not_found"])) {
                                    $autor["person"]["affiliation"]["name_not_found"] = preg_replace("/\s+/"," ",$autor["person"]["affiliation"]["name_not_found"]);
                                    $termo_limpo = str_replace (array("\r\n", "\n", "\r"), "", $autor["person"]["affiliation"]["name_not_found"]);
                                    $termo_limpo = preg_replace('/^\s+|\s+$/', '', $termo_limpo);
                                    $termo_limpo = str_replace ("\t\n\r\0\x0B\xc2\xa0"," ",$termo_limpo);
                                    $termo_limpo = trim($termo_limpo, " \t\n\r\0\x0B\xc2\xa0");
                                    $termo_limpo_p = $autor["person"]["affiliation"]["name_not_found"];
                                    $termo_limpo = rawurlencode($termo_limpo);
                                    $termo_limpo = str_replace("%C2%A0","%20",$termo_limpo);

                                    $ch = curl_init();
                                    $method = "GET";
                                    $url = 'http://vocab.sibi.usp.br/instituicoes/vocab/services.php?task=fetch&arg='.$termo_limpo.'&output=json';                            
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                                    $result_get_id_tematres = curl_exec($ch);
                                    $resultado_get_id_tematres = json_decode($result_get_id_tematres, true);
                                    curl_close($ch);

                                    if ($resultado_get_id_tematres["resume"]["cant_result"] != 0) {                                        
                                        foreach($resultado_get_id_tematres["result"] as $key => $val) {
                                            $term_key = $key;
                                        }
                                        
                                        $ch = curl_init();
                                        $method = "GET";
                                        $url = 'http://vocab.sibi.usp.br/instituicoes/vocab/services.php?task=fetchTerm&arg='.$term_key.'&output=json';
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                                        $result_term = curl_exec($ch);
                                        $resultado_term = json_decode($result_term, true);
                                        $termo_correto = $resultado_term["result"]["term"]["string"];
                                        curl_close($ch);
    
                                        if(!empty($autor["person"]["name"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                                        }
                                        if(!empty($autor["person"]["affiliation"]["location"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                                        }
                                        if(!empty($autor["person"]["date"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                                        }
                                        if(!empty($autor["person"]["potentialAction"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                                        }
                                        if(!empty($autor["person"]["USP"]["autor_funcao"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                                        }
                                        echo '<br/>Encontrado: '.$termo_correto.'<br/>';                                                                            
                                        if(!empty($termo_correto)){
                                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name"] = $termo_correto;
                                        }                                    
                                        
                                    } else {
                                        
                                        //echo "Não obteve resultados no tematres<br/>";
    
                                        if(!empty($autor["person"]["name"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                                        }
                                        if(!empty($autor["person"]["affiliation"]["location"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                                        }
                                        if(!empty($autor["person"]["date"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                                        }
                                        if(!empty($autor["person"]["potentialAction"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                                        }
                                        if(!empty($autor["person"]["USP"]["autor_funcao"])){
                                            $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                                        }                                                                            
                                        if(!empty($autor["person"]["affiliation"]["name_not_found"])){
                                            echo '<br/>Sem resultado: '.$termo_limpo_p.'<br/>';
                                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name_not_found"] = $termo_limpo_p;
                                        }
    
                                    } 

                                } else {
                                    $resultado_get_id_tematres["resume"]["cant_result"] = 0;
                                    if(!empty($autor["person"]["name"])){
                                        $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                                    }
                                    if(!empty($autor["person"]["affiliation"]["location"])){
                                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                                    }
                                    if(!empty($autor["person"]["date"])){
                                        $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                                    }
                                    if(!empty($autor["person"]["potentialAction"])){
                                        $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                                    }
                                    if(!empty($autor["person"]["USP"]["autor_funcao"])){
                                        $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                                    }                                                         
                                    if(!empty($autor["person"]["affiliation"]["name"])){
                                        echo '<br/>Termo existente: '.$autor["person"]["affiliation"]["name"].'<br/>';
                                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name"] = $autor["person"]["affiliation"]["name"];
                                    }                                       
                                }
                            
 
                            

                            $i++;
                            
                        }

                        $body_upsert["doc_as_upsert"] = true;
                        echo '<br/>';
                        print_r($body_upsert);
                        $resultado_upsert = elasticsearch::elastic_update($registro["_id"],$type,$body_upsert);
                        echo '<br/><br/>'; 
                        print_r($resultado_upsert);
                        unset($body_upsert);
                                                   
                        echo "<br/>=========================================================<br/><br/>";
                    } 
            
                ?> 
   
        </div>
    </body>
</html>