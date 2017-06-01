<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        

/* Consulta n registros ainda não corrigidos */

if (empty($_GET)) {

$query='
{    
    "query": {
        "query_string" : {
            "fields" : ["colab_instituicao","colab_instituicao_tematres"],
            "query" : "+_exists_:colab_instituicao -_exists_:colab_instituicao_tematres"
        }
    },
    "size":1000
}
';
    
/*
{
    "term": {
        "colab_instituicao_tematres": false
    }
},


*/    
    
} else {

$query='
{
    "query": {
        "query_string" : {
            "fields" : ["colab_instituicao","colab_instituicao_naocorrigido"],
            "query" : "colab_instituicao_naocorrigido:\"'.$_GET["term"].'\""
        }
    },
    "size":1000
}
';
    //print_r($query);
}

$params = [
    'index' => 'sibi',
    'type' => 'producao',
    '_source' => [
      '_id','colab_instituicao','colab_instituicao_naocorrigido'  
    ],    
    'body' => $query
];
$response = $client->search($params);       
echo 'Total de registros faltantes: '.$response['hits']['total'].'';
//print_r($response);        
        
        ?> 
        <title>BDPI USP - Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo</title>
    </head>
    <body> 
        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-large-bottom">
            

                
                <?php 
                    $i = 1;
                    foreach ($response["hits"]["hits"] as $colab) {
                        print_r($i);
                        $i++;                        
                        echo ' - ';
                        print_r($colab["_id"]);
                        //echo '<br/><br/>';
                        flush();
                        //print_r($colab);
                        
                        
                        foreach ($colab['_source']['colab_instituicao'] as $termo) {
                            //echo 'Termo original: '.$termo.'<br/>';
                            $termo_limpo = limpar($termo);
                            //echo 'Termo limpo: '.$termo_limpo.'<br/>';
                            
                            $xml = simplexml_load_file('http://bdpife2.sibi.usp.br/instituicoes/vocab/services.php?task=fetch&arg='.$termo_limpo.'');
                            
                            if ($xml->{'resume'}->{'cant_result'} != 0) {                                         
                                $termo_xml = simplexml_load_file('http://bdpife2.sibi.usp.br/instituicoes/vocab/services.php?task=fetchTerm&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
                                $termo_corrigido[] = $termo_xml->{'result'}->{'term'}->{'string'};
                                //echo 'Termo recuperado : '.$termo_xml->{'result'}->{'term'}->{'string'}.'<br/>';
                                if ($termo_xml->{'result'}->{'term'}->{'code'} != "") {
                                   $termo_geolocalizacao[] = $termo_xml->{'result'}->{'term'}->{'code'};
                                } 
                            } else {
                                $termo_naocorrigido[] = $termo_limpo;
                            }
                            //echo '<br/>';
                            flush();
                        }
                        
                        if (empty($termo_corrigido)){
                            $termo_corrigido = [];
                        }
                        
                        if (empty($termo_naocorrigido)){
                            $termo_naocorrigido = [];
                        }
                        
                        if (empty($termo_geolocalizacao)){
                            $termo_geolocalizacao = [];
                        }
                        
                        
                        $termo_edit = implode("\",\"",$termo_corrigido);
                        $termo_nao_corrigido = implode("\",\"",$termo_naocorrigido);
                        $geocode_edit = implode("\",\"",$termo_geolocalizacao);
                        //echo $termo_edit;
                        //echo '<br/>';
                        //echo $geocode_edit;
                        $conta = count($termo_corrigido);
                        $conta_geo = count($termo_geolocalizacao);
                        $conta_total = count($colab['_source']['colab_instituicao']);    
                        //echo '<br/>Quantidade de termos corrigidos: '.$conta.'<br/>';
                        //echo 'Quantidade de termos consultados: '.$conta_total.'<br/>';
                        //echo 'Quantidade de termos geolocalizados: '.$conta_geo.'';                        
                        
                        echo '<br/><br/>';
                        
                        if (count($termo_corrigido) > 0 && count($termo_geolocalizacao) > 0) {  
                            //echo "<br/><br/>Termo e Geolocalização Incluídos<br/><br/>";
                        
                            
                               $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_corrigido" : [ "'.$termo_edit.'" ],
                                      "colab_instituicao_geocode" : [ "'.$geocode_edit.'" ],
                                      "colab_instituicao_naocorrigido" : [ "'.$termo_nao_corrigido.'" ],
                                      "colab_instituicao_tematres" : true

                                   },
                                   "doc_as_upsert" : true
                                }
                              ';
                                
                            $params = [
                                'index' => 'sibi',
                                'type' => 'producao',
                                'id' => $colab["_id"],
                                'body' => $query
                            ];
                            $response = $client->update($params);  
                            
                            print_r($query);
                            echo '<br/>';
                            print_r($response);
                            echo '<br/>';
                        

                        
                        } elseif (count($termo_corrigido) > 0) {
                            //echo "<br/><br/>Apenas termo incluído<br/><br/>";
                            
                                                
                                $query = '
                                    {
                                       "doc" : {
                                          "colab_instituicao_corrigido" : [ "'.$termo_edit.'" ],
                                          "colab_instituicao_naocorrigido" : [ "'.$termo_nao_corrigido.'" ],
                                          "colab_instituicao_tematres" : true
                                       },
                                       "doc_as_upsert" : true
                                    }
                                    ';
                            
                            $params = [
                                'index' => 'sibi',
                                'type' => 'producao',
                                'id' => $colab["_id"],
                                'body' => $query
                            ];
                            $response = $client->update($params);  
                            
                            print_r($query);
                            echo '<br/>';
                            print_r($response);
                            echo '<br/>';
                        
                        } else {
                            //echo "<br/><br/>Termo não corrigido incluído<br/><br/>";
                        
                            $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_naocorrigido" : [ "'.$termo_nao_corrigido.'" ],
                                      "colab_instituicao_tematres" : true

                                   },
                                   "doc_as_upsert" : true
                                }
                                ';
                            
                            $params = [
                                'index' => 'sibi',
                                'type' => 'producao',
                                'id' => $colab["_id"],
                                'body' => $query
                            ];
                            $response = $client->update($params);                              
                            print_r($query);
                            echo '<br/>';
                            print_r($response);
                            echo '<br/>';
                        
                        }
                        flush();
                        $termo_corrigido = array();
                        $termo_naocorrigido = array();
                        $termo_geolocalizacao = array();
                    } 
                ?> 
   
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>