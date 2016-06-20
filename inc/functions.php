<?php

function query_elastic ($query) {
    $ch = curl_init();
    $method = "POST";
    $url = "http://localhost/sibi/producao/_search";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, TRUE);
    return $data;
}

function query_one_elastic ($_id) {
    $ch = curl_init();
    $method = "GET";
    $url = "http://localhost/sibi/producao/$_id";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS);

    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, TRUE);
    return $data;
}



function counter ($_id) {
    $ch = curl_init();
    $method = "POST";
    $url = "http://localhost/sibi/producao_metrics/$_id/_update";
    $query = 
             '{
                "script" : {
                    "inline": "ctx._source.counter += count",
                    "params" : {
                        "count" : 1
                    }
                },
                "upsert" : {
                    "counter" : 1
                }
            }';
    
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, TRUE);
    return $data;
}


function contar_registros () {
    $ch = curl_init();
    $method = "POST";
    $url = "http://localhost/sibi/producao/_count";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, TRUE);
    print_r($data["count"]);


}

function ultimos_registros() {
    
     $query = '{
                "query": {
                    "match_all": {}
                 },
                "size": 10,
                "sort" : [
                    {"_uid" : {"order" : "desc"}}
                    ]
                }';
    $data = query_elastic($query);

echo '<h3>Últimos registros</h3>';
echo '<div class="ui divided items">';
foreach ($data["hits"]["hits"] as $r){
#print_r($r);
echo '<div class="item">
<div class="ui tiny image">';
if (!empty($r["_source"]['unidadeUSP'])) {
$file = 'inc/images/logosusp/'.$r["_source"]['unidadeUSP'][0].'.jpg';
}
if (file_exists($file)) {
echo '<img src="'.$file.'"></a>';
} else {
#echo ''.$r['unidadeUSP'].'</a>';
};
echo '</div>';
echo '<div class="content">';
if (!empty($r["_source"]['title'])){
echo '<a class="ui small header" href="single.php?_id='.$r['_id'].'">'.$r["_source"]['title'].' ('.$r["_source"]['year'].')</a>';
};
echo '<div class="extra">';
if (!empty($r["_source"]['authors'])) {
foreach ($r["_source"]['authors'] as $autores) {
echo '<div class="ui label" style="color:black;"><i class="user icon"></i><a href="result.php?authors='.$autores.'">'.$autores.'</a></div>';
}
};
echo '</div></div>';
echo '</div>';
}
echo '</div>';
     
}


function criar_unidadeUSP_inicio () {

    $query = '{
        "size": 0,
        "aggs": {
            "group_by_state": {
                "terms": {
                    "field": "unidadeUSPtrabalhos",
                    "order" : { "_term" : "asc" },
                    "size" : 100,
                    "missing": "Sem unidade cadastrada"
                }
            }
        }
    }';
    
    $data = query_elastic($query);
    
    echo '<h3>Unidades USP</h3>';
    echo '<div class="ui five stackable doubling cards">';
    foreach ($data["aggregations"]["group_by_state"]["buckets"] as $facets) {
        echo ' <a href="result.php?unidadeUSP='.strtoupper($facets['key']).'"><div class="ui card" data-title="'.trim(strtoupper($facets['key'])).'" style="box-shadow:none;"><div class="image">';
                $file = 'inc/images/logosusp/'.strtoupper($facets['key']).'.jpg';
                if (file_exists($file)) {
                echo '<img src="inc/images/logosusp/'.strtoupper($facets['key']).'.jpg" style="height: 65px;width:65px">';
                } else {
                  echo ''.strtoupper($facets['key']).'</a>';
              };
              echo'</div></a>';
        echo '<div class="content" style="padding:0.3em;"><a class="ui center aligned tiny header" href="result.php?'.substr($facet_name, 1).'='.strtoupper($facets['key']).'">'.strtoupper($facets['key']).'</a></div>
                <div id="imagelogo" class="floating ui mini teal label" style="z-index:0;">
                '.$facets['doc_count'].'
                </div>';
        echo '</div>';

    };
    echo '</div>'; 

}

function gerar_faceta($consulta,$url,$campo,$tamanho,$nome_do_campo,$sort) {

    if (!empty($sort)){
         
         $sort_query = '"order" : { "_term" : "'.$sort.'" },';  
        }
    $query = '
    {
        "size": 0,
        '.$consulta.'
        "aggregations": {
          "counts": {
            "terms": {
              "field": "'.$campo.'",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';
    
    $data = query_elastic($query);
    
   
    echo '<div class="item">';
    echo '<a class="active title"><i class="dropdown icon"></i>'.$nome_do_campo.'</a>';
    echo '<div class="content">';
    echo '<div class="ui list">';
    foreach ($data["aggregations"]["counts"]["buckets"] as $facets) {
        echo '<div class="item">';
        echo '<a href="'.$url.'&'.$campo.'='.$facets['key'].'">'.$facets['key'].'</a><div class="ui label">'.$facets['doc_count'].'</div>';
        echo '</div>';
    };
    echo   '</div>
      </div>
  </div>';

}

/* Recupera os exemplares do DEDALUS */
function load_itens ($sysno) {
    $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
    if ($xml->error == "No associated items"){

    } else {
            echo "<h4 class=\"ui sub header\">Exemplares físicos disponíveis nas Bibliotecas</h4>
            <table class=\"ui celled table\">
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
          }
  }

/* Pegar o tipo de material */
function get_type($material_type){
  switch ($material_type) {
  case "ARTIGO DE PERIODICO":
      return "article-journal";
      break;
  case "PARTE DE MONOGRAFIA/LIVRO":
      return "chapter";
      break;
  case "APRESENTACAO SONORA/CENICA/ENTREVISTA":
      return "interview";
      break;
  case "TRABALHO DE EVENTO-RESUMO":
      return "paper-conference";
      break;
  case "TEXTO NA WEB":
      return "post-weblog";
      break;
  }
}

/* Function to generate Graph Bar */
function generateDataGraphBar($url, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {

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
              "field": "'.$campo.'",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';
    
    $facet = query_elastic($query);    
    
    $data_array= array();
    foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
        array_push($data_array,'{"name":"'.$facets['key'].'","value":'.$facets['doc_count'].'}');
    };
    $comma_separated = implode(",", $data_array);
    return $comma_separated;

};

/* Function to generate Tables */
function generateDataTable($url, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {
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
              "field": "'.$campo.'",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';
    
    $facet = query_elastic($query);    



echo "<table class=\"ui celled table\">
  <thead>
    <tr>
      <th>".$facet_display_name."</th>
      <th>Quantidade</th>
    </tr>
  </thead>
  <tbody>";

    foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
        echo "<tr>
              <td>".$facets['key']."</td>
              <td>".$facets['doc_count']."</td>
            </tr>";
    };

  echo"</tbody>
    </table>";


};


/* Function to generate CSV */
function generateCSV($url, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {

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
              "field": "'.$campo.'",
              "missing": "N/D",
              '.$sort_query.'
              "size":'.$tamanho.'
            }
          }
        }
     }
     ';
    
    $facet = query_elastic($query);   

    $data_array= array();
    foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
        array_push($data_array,''.$facets["key"].'\\t'.$facets["doc_count"].'');
    };
    $comma_separated = implode("\\n", $data_array);
    return $comma_separated;

};

/* Comparar registros */

function compararRegistros ($query_type,$query_year,$query_title,$query_authors) {

    $query = '
    {
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$query_title.'",
                            "type":       "cross_fields",
                            "fields":     [ "title" ],
                            "minimum_should_match": "90%" 
                         }
                    },
                    {
                        "multi_match" : {
                            "query":      "'.$query_authors.'",
                            "type":       "best_fields",
                            "fields":     [ "authors" ],
                            "minimum_should_match": "10%" 
                        }
                    }
                ],
                "minimum_should_match" : 1                
            }
        }
    }
    ';
    
    $result = query_elastic($query);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
            echo '
                <tr>
                  <td>'.$query_type.'</td>
                  <td>'.$query_year.'</td>
                  <td>'.$query_title.'</td>
                  <td>'.implode("|",$query_authors).'</td>
                  <td>'.$results["_source"]["type"].'</td>
                  <td>'.$results["_source"]["title"].'</td>
                  <td>'. implode("|",$results["_source"]["authors"]).'</td>
                  <td>'.$results["_source"]["year"].'</td>
                  <td>'.$results["_score"].'</td>
                  <td>'.$results["_id"].'</td>
                </tr>                
                ';
        }
    } else {
            echo '
                <tr>
                  <td>'.$query_type.'</td>
                  <td>'.$query_year.'</td>
                  <td>'.$query_title.'</td>
                  <td><p style="color:red">Não encontrado</p></td>
                  <td><p style="color:red">Não encontrado</p></td>
                  <td><p style="color:red">Não encontrado</p></td>
                  <td><p style="color:red">Não encontrado</p></td>
                  <td><p style="color:red">Não encontrado</p></td>
                  <td><p style="color:red">Não encontrado</p></td>
                </tr>
                ';
    }
}

?>
