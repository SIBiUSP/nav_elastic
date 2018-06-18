<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
        // Set directory to ROOT
        chdir('../');         
        require 'inc/config.php'; 
        require 'inc/functions.php';            
        require 'inc/meta-header.php';

        /* Consulta n registros ainda não corrigidos */
        if (empty($_GET)) {
            $body["query"]["bool"]["must"]["query_string"]["query"] = "+_exists_:author.person.affiliation.name_not_found";
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
        
        if (isset($_GET["term"])) {        
            $body["query"]["bool"]["must"]["query_string"]["query"] = 'author.person.affiliation.name_not_found:'.$_GET["term"].'';
        }          

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["_source"] = ["_id","author"];
        $params["size"] = 20;        
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
                    $termCleaned = str_replace("&", "e", $autor["person"]["affiliation"]["name_not_found"]);

                    $result_tematres = authorities::tematres($termCleaned, $tematres_url);
                    //print_r($result_tematres);

                    if (!empty($result_tematres["found_term"])) {

                        if (!empty($autor["person"]["name"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                        }
                        if (!empty($autor["person"]["affiliation"]["location"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                        }
                        if (!empty($autor["person"]["date"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                        }
                        if (!empty($autor["person"]["potentialAction"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                        }
                        if (!empty($autor["person"]["USP"]["autor_funcao"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                        }
                        echo '<br/>Encontrado: '.$result_tematres["found_term"].'<br/>';                                                                            
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name"] = $result_tematres["found_term"];
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["locationTematres"] = $result_tematres["country"];
                    
                    } else {

                        if (!empty($autor["person"]["name"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                        }
                        if (!empty($autor["person"]["affiliation"]["location"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                        }
                        if (!empty($autor["person"]["date"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                        }
                        if (!empty($autor["person"]["potentialAction"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                        }
                        if (!empty($autor["person"]["USP"]["autor_funcao"])) {
                            $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                        }              
                        echo '<br/>Sem resultado: '.$result_tematres["term_not_found"].'<br/>';
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name_not_found"] = $result_tematres["term_not_found"];
                    
                    }
                } else {

                    $resultado_get_id_tematres["resume"]["cant_result"] = 0;
                    if (!empty($autor["person"]["name"])) {
                        $body_upsert["doc"]["author"][$i]["person"]["name"] = $autor["person"]["name"];
                    }
                    if (!empty($autor["person"]["affiliation"]["location"])) {
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["location"] = $autor["person"]["affiliation"]["location"];
                    }
                    if (!empty($autor["person"]["date"])) {
                        $body_upsert["doc"]["author"][$i]["person"]["date"] = $autor["person"]["date"];
                    }
                    if (!empty($autor["person"]["potentialAction"])) {
                        $body_upsert["doc"]["author"][$i]["person"]["potentialAction"] = $autor["person"]["potentialAction"];
                    }
                    if (!empty($autor["person"]["USP"]["autor_funcao"])) {
                        $body_upsert["doc"]["author"][$i]["person"]["USP"]["autor_funcao"] = $autor["person"]["USP"]["autor_funcao"];
                    }                                                         
                    if (!empty($autor["person"]["affiliation"]["name"])) {
                        echo '<br/>Termo existente: '.$autor["person"]["affiliation"]["name"].'<br/>';
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["name"] = $autor["person"]["affiliation"]["name"];
                    }
                    if (!empty($author["person"]["affiliation"]["locationTematres"])) {
                        echo '<br/>Local existente: '.$author["person"]["affiliation"]["locationTematres"].'<br/>';
                        $body_upsert["doc"]["author"][$i]["person"]["affiliation"]["locationTematres"] = $author["person"]["affiliation"]["locationTematres"];
                    }                                                     

                }
                $i++;
                
            }

            $body_upsert["doc_as_upsert"] = true;
            echo '<br/>';
            print_r($body_upsert);
            $resultado_upsert = elasticsearch::elastic_update($registro["_id"], $type, $body_upsert);
            echo '<br/><br/>'; 
            print_r($resultado_upsert);
            unset($body_upsert);
                                        
            echo "<br/>=========================================================<br/><br/>";
        } 
    
        ?> 
   
        </div>
    </body>
</html>