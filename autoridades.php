<?php

include('inc/functions.php');

/* Montar a consulta */
$cursor = query_one_elastic($_GET['_id']);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Correção de autoridades</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <?php 
                
                $termo_corrigido = [];
                $termo_geolocalizacao = [];
                
                foreach ($cursor['_source']['colab_instituicao'] as $colab){
                    echo 'Termo buscado: '.$colab.'';
                    echo '<br/>';
                    $termo_limpo = limpar($colab);
                    echo 'Termo limpo: '.$termo_limpo.'';
                    echo '<br/>';
                    
                    $xml = simplexml_load_file('http://bdpife2.sibi.usp.br/instituicoes/vocab/services.php?task=fetch&arg='.$termo_limpo.'');                    
                    
                    if ($xml->{'resume'}->{'cant_result'} != 0) {                                         
                        $termo_xml = simplexml_load_file('http://bdpife2.sibi.usp.br/instituicoes/vocab/services.php?task=fetchTerm&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
                        $termo_corrigido[] = $termo_xml->{'result'}->{'term'}->{'string'};
                        echo 'Termo recuperado : '.$termo_xml->{'result'}->{'term'}->{'string'}.'<br/>';
                        if ($termo_xml->{'result'}->{'term'}->{'code'} != "") {
                           $termo_geolocalizacao[] = $termo_xml->{'result'}->{'term'}->{'code'};
                        } 
                    } else {
                        
                    }
                     
                    }
                    
                    $termo_edit = implode("\",\"",$termo_corrigido);
                    $geocode_edit = implode("\",\"",$termo_geolocalizacao);
                    echo $termo_edit;
                    echo '<br/>';
                    echo $geocode_edit;
                    $conta = count($termo_corrigido);
                    $conta_geo = count($termo_geolocalizacao);
                    echo $conta;
                    echo $conta_geo;
                
                    if (count($termo_corrigido) > 0 && count($termo_geolocalizacao) > 0) {
                        echo "<br/><br/>conta 1<br/><br/>";
                        
                        $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_corrigido" : [ "'.$termo_edit.'" ],
                                      "colab_instituicao_geocode" : [ "'.$geocode_edit.'" ]

                                   }
                                }
                                ';
                      
                        $result = update_elastic($_GET['_id'],$query);
                        print_r($result);  
                        

                        
                    } elseif (count($termo_corrigido) > 0) {
                        echo "<br/><br/>conta 2<br/><br/>";
                        
                        $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_corrigido" : [ "'.$termo_edit.'" ]

                                   }
                                }
                                ';
                      
                        $result = update_elastic($_GET['_id'],$query);
                        print_r($result);                        
                        
                    } else {
                        echo "<br/><br/>conta 3<br/><br/>";
                        
                                                $query = '
                                {
                                   "doc" : {
                                      "colab_instituicao_naocorrigido" : [ "'.$termo_corrigido.'" ]

                                   }
                                }
                                ';
                      
                        $result = update_elastic($_GET['_id'],$query);
                        print_r($result);  
                        
                    }
                 ?>
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>