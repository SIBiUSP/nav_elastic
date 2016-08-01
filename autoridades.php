<?php

include('inc/config.php'); 
include('inc/functions.php');

/* Consulta n registros ainda não corrigidos */

if (empty($_GET)) {

$query='
{
    "fields" : ["_id","colab_instituicao"],
    "query": {
        "filtered": {
            "query": {
                "match_all": {}
            },
            "filter": {
                "bool": {
                    "must": [{
                        "term": {
                            "colab_instituicao_tematres": false
                        }
                    },
                    {
                        "exists":{
                            "field":"colab_instituicao"
                        }
                    }]
                }
            }
        }
  },
  "size":10000
}
';
} else {

$query='
{
    "fields" : ["_id","colab_instituicao","colab_instituicao_naocorrigido"],
    "query": {
        "filtered": {
            "query": {
                "match_all": {}
            },
            "filter": {
                "bool": {
                    "must": [{
                        "term": {
                            "colab_instituicao_naocorrigido": "'.$_GET["term"].'"
                        }
                    }]
                }
            }
        }
  },
  "size":10000
}
';
}

$cursor = query_elastic($query);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>BDPI USP - Correção de autoridades</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                

                
                <?php 
                    $i = 1;
                    foreach ($cursor["hits"]["hits"] as $colab) {
                        print_r($i);
                        $i++;                        
                        echo ' - ';
                        print_r($colab["_id"]);
                        //echo '<br/><br/>';
                        flush();
                        //print_r($colab);
                        
                        
                        foreach ($colab['fields']['colab_instituicao'] as $termo) {
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
                        
                        $termo_edit = implode("\",\"",$termo_corrigido);
                        $termo_nao_corrigido = implode("\",\"",$termo_naocorrigido);
                        $geocode_edit = implode("\",\"",$termo_geolocalizacao);
                        //echo $termo_edit;
                        //echo '<br/>';
                        //echo $geocode_edit;
                        $conta = count($termo_corrigido);
                        $conta_geo = count($termo_geolocalizacao);
                        $conta_total = count($colab['fields']['colab_instituicao']);    
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

                                   }
                                }
                              ';
                                
                            
                            
                            //print_r($query);
                            $result = update_elastic($colab["_id"],$query);
                            //print_r($result);  
                        

                        
                        } elseif (count($termo_corrigido) > 0) {
                            //echo "<br/><br/>Apenas termo incluído<br/><br/>";
                            
                                                
                                $query = '
                                    {
                                       "doc" : {
                                          "colab_instituicao_corrigido" : [ "'.$termo_edit.'" ],
                                          "colab_instituicao_naocorrigido" : [ "'.$termo_nao_corrigido.'" ],
                                          "colab_instituicao_tematres" : true
                                       }
                                    }
                                    ';
                            
                                
                            //print_r($query);
                            $result = update_elastic($colab["_id"],$query);
                            //print_r($result);                        
                        
                        } else {
                            //echo "<br/><br/>Termo não corrigido incluído<br/><br/>";
                        
                            $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_naocorrigido" : [ "'.$termo_nao_corrigido.'" ],
                                      "colab_instituicao_tematres" : true

                                   }
                                }
                                ';                        
                            //print_r($query);
                            $result = update_elastic($colab["_id"],$query);
                            //print_r($result);
                        
                        }
                        flush();
                        $termo_corrigido = array();
                        $termo_naocorrigido = array();
                        $termo_geolocalizacao = array();
                    } 
                ?>
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>