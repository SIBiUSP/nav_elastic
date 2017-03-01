<?php 

class processaResultados {
    
    /* Function to generate Graph Bar */
    static function generateDataGraphBar($query, $field, $sort, $sort_orientation, $facet_display_name, $size) {
        global $index;
        global $client;
        
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"][$sort] = $sort_orientation;
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;
        
        $params = [
            'index' => $index,
            'type' => 'producao',
            'size'=> 0, 
            'body' => $query
        ]; 

        $facet = $client->search($params);  

        $data_array= array();
        foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
            array_push($data_array,'{"name":"'.$facets['key'].'","value":'.$facets['doc_count'].'}');
        };

        if ($field == "year" ) {
            $data_array_inverse = array_reverse($data_array);
            $comma_separated = implode(",", $data_array_inverse);
        } else {
            $comma_separated = implode(",", $data_array);
        }

        return $comma_separated;

    }
    
    /* Recupera os exemplares do DEDALUS */
    static function load_itens_new ($sysno) {
        $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
        if ($xml->error == "No associated items"){

        } else {


            echo '<div id="exemplares'.$sysno.'">';
            echo "<table class=\"uk-table uk-table-small uk-text-small uk-table-striped\">
                        <caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>
                        <thead>
                          <tr>
                            <th>Biblioteca</th>
                            <th>Código de barras</th>
                            <th>Status</th>
                            <th>Número de chamada</th>";
                            if ($xml->item->{'loan-status'} == "A"){
                            echo "<th>Status</th>
                            <th>Data provável de devolução</th>";
                          } else {
                            echo "<th>Status</th>";
                          }
                          echo "</tr>
                        </thead>
                      <tbody>";
              foreach ($xml->item as $item) {
                echo '<tr>';
                echo '<td>'.$item->{'sub-library'}.'</td>';
                echo '<td>'.$item->{'barcode'}.'</td>';
                echo '<td>'.$item->{'item-status'}.'</td>';
                echo '<td>'.$item->{'call-no-1'}.'</td>';
                if ($item->{'loan-status'} == "A"){
                echo '<td>Emprestado</td>';
                echo '<td>'.$item->{'loan-due-date'}.'</td>';
              } else {
                echo '<td>Disponível</td>';
              }
                echo '</tr>';
              }
              echo "</tbody></table></div>";
              }
              flush();
      }

}

function consultar_vcusp($termo) {
    echo '<h4>Vocabulário Controlado do SIBiUSP</h4>';
    $xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');
    
    if ($xml->{'resume'}->{'cant_result'} != 0) {

        $termo_xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchUp&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
        foreach (($termo_xml->{'result'}->{'term'}) as $string_up) {
            $string_up_array[] = '<a href="result.php?assunto='.$string_up->{'string'}.'">'.$string_up->{'string'}.'</a>';    
        };
        echo 'Você também pode pesquisar pelos termos mais genéricos: ';
        print_r(implode(" -> ",$string_up_array));
        echo '<br/>';
        $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
        if (!empty($termo_xml_down->{'result'}->{'term'})){
            foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                $string_down_array[] = '<a href="result.php?assunto='.$string_down->{'string'}.'">'.$string_down->{'string'}.'</a>';     
            };
            echo 'Ou pesquisar pelo assuntos mais específicos: ';
            print_r(implode(" - ",$string_down_array));            
        }


    } else {
        $termo_naocorrigido[] = $termo_limpo;
    }
}

function get_fulltext_file($id,$session){
    $files_upload = glob('upload/'.$id[0].'/'.$id[1].'/'.$id[2].'/'.$id[3].'/'.$id[4].'/'.$id[5].'/'.$id[6].'/'.$id[7].'/'.$id.'/*.{pdf,pptx}', GLOB_BRACE);    
    $links_upload = "";
    if (!empty($files_upload)){       
        foreach($files_upload as $file) {
            $delete = "";    
            if (!empty($session)){
                $delete = '<form method="POST" action="single.php?_id='.$id.'">
                               <input name="delete_file" value="'.$file.'"  type="hidden">
                               <button class="uk-close uk-close-alt uk-alert-danger" alt="Deletar arquivo"></button>
                           </form>';
            }
            
            if( strpos( $file, '.pdf' ) !== false ) {
                $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$file.'" target="_blank"><img src="inc/images/pdf.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
            } else {
                $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$file.'" target="_blank"><img src="inc/images/pptx.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
            }
        }
    }
    return $links_upload;
}

/* Recupera os exemplares do DEDALUS */
function load_itens_single ($sysno) {
    $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
    if ($xml->error == "No associated items"){

    } else {
        echo "<h4>Exemplares físicos disponíveis nas Bibliotecas</h4>";
        echo "<table class=\"uk-table uk-table-hover uk-table-striped uk-table-condensed\">
                    <thead>
                      <tr>
                        <th>Biblioteca</th>
                        <th>Código de barras</th>
                        <th>Status</th>
                        <th>Número de chamada</th>";
                        if ($xml->item->{'loan-status'} == "A"){
                        echo "<th>Status</th>
                        <th>Data provável de devolução</th>";
                      } else {
                        echo "<th>Status</th>";
                      }
                      echo "</tr>
                    </thead>
                  <tbody>";
          foreach ($xml->item as $item) {
            echo '<tr>';
            echo '<td>'.$item->{'sub-library'}.'</td>';
            echo '<td>'.$item->{'barcode'}.'</td>';
            echo '<td>'.$item->{'item-status'}.'</td>';
            echo '<td>'.$item->{'call-no-1'}.'</td>';
            if ($item->{'loan-status'} == "A"){
            echo '<td>Emprestado</td>';
            echo '<td>'.$item->{'loan-due-date'}.'</td>';
          } else {
            echo '<td>Disponível</td>';
          }
            echo '</tr>';
          }
          echo "</tbody></table>";
          echo '<hr>';
          }
          flush();
  }

/* Function to generate Tables */
function generateDataTable($client, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {
    if (!empty($sort)){
        $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';  
    }
    $query = '
    {
        "size": 0,
        '.$consulta.'
        "aggregations": {
          "counts": {
            "terms": {
              "field": "'.$campo.'.keyword",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';

    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,          
        'body' => $query
    ];
    
    $response = $client->search($params);  

echo "<table class=\"uk-table\">
  <thead>
    <tr>
      <th>".$facet_display_name."</th>
      <th>Quantidade</th>
    </tr>
  </thead>
  <tbody>";

    foreach ($response['aggregations']['counts']['buckets'] as $facets) {
        echo "<tr>
              <td>".$facets['key']."</td>
              <td>".$facets['doc_count']."</td>
            </tr>";
    };

  echo"</tbody>
    </table>";


}

/* Function to generate CSV */
function generateCSV($client, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {

    if (!empty($sort)){
        $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';  
    }
    $query = '
    {
        "size": 0,
        '.$consulta.'
        "aggregations": {
          "counts": {
            "terms": {
              "field": "'.$campo.'.keyword",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,          
        'body' => $query
    ];
    
    $response = $client->search($params); 
    $data_array= array();
    foreach ($response['aggregations']['counts']['buckets'] as $facets) {
        array_push($data_array,''.$facets["key"].'\\t'.$facets["doc_count"].'');
    };
    $comma_separated = implode("\\n", $data_array);
    return $comma_separated;

}

function limpar($text) {
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '//'           =>   '', // UTF-8 hyphen to "normal" hyphen
        '/[’‘]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        '/[^A-Za-z0-9\\s]/' => '',
        '/( )+/' => ' ',
    );
    
    
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function get_title_elsevier($issn,$api_elsevier) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.elsevier.com/content/serial/title/issn/'.$issn.'?apiKey='.$api_elsevier.'',
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $data = json_decode($resp, TRUE);
    return $data;
    // Close request to clear up some resources
    curl_close($curl);    
}

function get_articlefull_elsevier($doi,$api_elsevier) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.elsevier.com/content/article/doi/'.$doi.'?apiKey='.$api_elsevier.'&httpAccept=text%2Fhtml',
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);    
    return $resp;
    // Close request to clear up some resources
    curl_close($curl);    
} 

function get_citations_elsevier($doi,$api_elsevier) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.elsevier.com/content/abstract/citations?doi='.$doi.'&apiKey='.$api_elsevier.'&httpAccept=application%2Fjson',
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $data = json_decode($resp, TRUE);
    return $data;
    // Close request to clear up some resources
    curl_close($curl);    
} 

function get_oadoi($doi) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://api.oadoi.org/v1/publication/doi/'.$doi.'',
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $data = json_decode($resp, TRUE);
    return $data;
    // Close request to clear up some resources
    curl_close($curl);    
}

function metrics_update($client,$_id,$metrics_array){    

    $query = 
    '
    {
        "doc":{
            "metrics" : {
                '.implode(",",$metrics_array).'
            },
            "date":"'.date("Y-m-d").'"
        },                    
        "doc_as_upsert" : true
    }
    ';  
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'id' => $_id,
        'body' => $query
    ];
    $response = $client->update($params);        
    
}

function store_issn_info($client,$issn,$issn_info){    

    $query = 
    '
    {
        "doc":{
            "issn_info" : 
                '.$issn_info.'
            ,
            "date":"'.date("Y-m-d").'"
        },                    
        "doc_as_upsert" : true
    }
    ';
    
    $params = [
        'index' => 'sibi',
        'type' => 'issn',
        'id' => $issn,
        'body' => $query
    ];
    $response = $client->update($params);
    
}


?>