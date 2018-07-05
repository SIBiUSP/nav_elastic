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
            $body["query"]["bool"]["must"]["query_string"]["query"] = "+_exists_:isPartOf -isPartOf.tematresOK:true";
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
            $body["query"]["bool"]["must"]["query_string"]["query"] = 'isPartOf.name:'.$_GET["term"].' -isPartOf.tematresOK:true';
        }          

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["_source"] = ["_id","isPartOf"];
        $params["size"] = 500;        
        $params["body"] = $body;   

        $response = $client->search($params);
            
        echo 'Total de registros faltantes: '.$response['hits']['total'].'';
        
        ?> 
        <title>Autoridades - Título do periódico</title>
    </head>
    <body> 
        
        <div class="uk-container uk-container-center uk-margin-large-bottom">           
            
        <?php             
        // Pega cada um dos registros da resposta
        foreach ($response["hits"]["hits"] as $registro) {   

            // Para cada autor no registro
           // print_r($registro);
           // echo "<br/><br/>";

            $termCleaned = str_replace("&", "e", $registro['_source']["isPartOf"]["name"]);
            $result_tematres = authorities::tematres($termCleaned, $tematres_url);
            print_r($result_tematres["term_not_found"]);
            echo "<br/>";

            if (!empty($result_tematres["found_term"])) {
                // echo '<br/>Encontrado: '.$result_tematres["found_term"].'<br/>';  
                $body_upsert["doc"]["isPartOf"]["name"] = $result_tematres["found_term"];
                $body_upsert["doc"]["isPartOf"]["tematresOK"] = true;

                $body_upsert["doc_as_upsert"] = true;
              //  echo '<br/>';
             //   print_r($body_upsert);
                $resultado_upsert = elasticsearch::elastic_update($registro["_id"], $type, $body_upsert);
             //   echo '<br/><br/>'; 
             //   print_r($resultado_upsert);
                unset($body_upsert);

            }

           // echo "<br/>=========================================================<br/><br/>";
        } 
    
        ?> 
   
        </div>
    </body>
</html>