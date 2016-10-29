<?php

//function query_elastic ($query,$server) {
//    $ch = curl_init();
//    $method = "POST";
//    $url = "http://$server/sibi/producao/_search";
//
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_PORT, 9200);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
//
//    $result = curl_exec($ch);
//    curl_close($ch);
//    $data = json_decode($result, TRUE);
//    return $data;
//}

function query_one_elastic ($_id,$client) {
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'id' => ''.$_id.''
    ];
    $response = $client->get($params);
    return $response;    

}

//function query_graph ($query,$server) {
//    $ch = curl_init();
//    $method = "GET";
//    $url = "http://$server/sibi/_graph/explore";
//
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_PORT, 9200);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
//
//    $result = curl_exec($ch);
//    curl_close($ch);
//    $data = json_decode($result, TRUE);
//    return $data;
//}

function update_elastic ($_id,$query,$server) {
    $ch = curl_init();
    $method = "POST";
    $url = "http://$server/sibi/producao/$_id/_update";

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



function counter ($_id,$server) {
    $ch = curl_init();
    $method = "POST";
    $url = "http://$server/sibi/producao_metrics/$_id/_update";
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


function contar_registros ($client) {
    $query_all = '
        {
            "query": {
                "match_all": {}
            }
        }        
    ';
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,
        'body' => $query_all
    ];
    $response = $client->search($params);
    return $response['hits']['total'];
    print_r($response);


}

function contar_unicos ($field,$client) {

    $count_distinct_query = '
    {
        "aggs" : {
            "distinct_authors" : {
                "cardinality" : {
                  "field" : "'.$field.'"
                }
            }
        }
    }
    ';
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size' => 0,
        'body' => $count_distinct_query
    ];
    $response = $client->search($params);
    print_r($response);
    return $response["aggregations"]["distinct_authors"]["value"];
    
}

function store_user ($userdata,$server){
    $ch = curl_init();
    $method = "POST";
    $url = "http://$server/sibi/users/".$userdata->{'loginUsuario'}."/_update";
    
    $query_array[] = '"nomeUsuario" : "'.$userdata->{'nomeUsuario'}.'"';
    $query_array[] = '"tipoUsuario" : "'.$userdata->{'tipoUsuario'}.'"';
    $query_array[] = '"emailPrincipalUsuario" : "'.$userdata->{'emailPrincipalUsuario'}.'"';
    $query_array[] = '"emailAlternativoUsuario" : "'.$userdata->{'emailAlternativoUsuario'}.'"';
    $query_array[] = '"emailUspUsuario" : "'.$userdata->{'emailUspUsuario'}.'"';
    $query_array[] = '"numeroTelefoneFormatado" : "'.$userdata->{'numeroTelefoneFormatado'}.'"';
    
    foreach ($userdata->{'vinculo'} as $vinculo) {
        $query_vinculo[] = '{
                "tipoVinculo" : "'.$vinculo->{'tipoVinculo'}.'",
                "codigoSetor" : "'.$vinculo->{'codigoSetor'}.'",
                "nomeAbreviadoSetor" : "'.$vinculo->{'nomeAbreviadoSetor'}.'",
                "nomeSetor" : "'.$vinculo->{'nomeSetor'}.'",
                "codigoUnidade" : "'.$vinculo->{'codigoUnidade'}.'",
                "siglaUnidade" : "'.$vinculo->{'siglaUnidade'}.'",
                "nomeUnidade" : "'.$vinculo->{'nomeUnidade'}.'"
            }';         
    }
    
    $query = 
             '{
                "doc":{
                    "vinculo" : [
                        '.implode(",",$query_vinculo).'
                    ],
                    '.implode(",",$query_array).'
                },
                "doc_as_upsert" : true
            }';
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($ch);
    curl_close($ch);
    //$data = json_decode($result, TRUE);
    //print_r($data);    
}

function ultimos_registros($client) {
    
    $query = '{
                "query": {
                    "match_all": {}
                 },
                "size": 5,
                "sort" : [
                    {"_uid" : {"order" : "desc"}}
                    ]
                }';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size' => 5,
        'body' => $query
    ];
    $response = $client->search($params);    

    foreach ($response["hits"]["hits"] as $r){
        echo '<article class="uk-comment">
        <header class="uk-comment-header">';    
        if (!empty($r["_source"]['unidadeUSP'])) {
        $file = 'inc/images/logosusp/'.$r["_source"]['unidadeUSP'][0].'.jpg';
        }
        if (file_exists($file)) {
        echo '<img class="uk-comment-avatar" src="'.$file.'">';
        } else {
        #echo ''.$r['unidadeUSP'].'</a>';
        };
        if (!empty($r["_source"]['title'])){
        echo '<a class="ui small header" href="single.php?_id='.$r['_id'].'"><h4 class="uk-comment-title">'.$r["_source"]['title'].' ('.$r["_source"]['year'].')</h4></a>';
        };
        echo '<div class="extra">';
        if (!empty($r["_source"]['authors'])) {
        echo '<div class="uk-comment-meta";">';    
        foreach ($r["_source"]['authors'] as $autores) {
        echo '<a href="result.php?authors[]='.$autores.'">'.$autores.'</a>, ';
        }
        echo '</div>';     
        };
        echo '</header>';
        echo '</article>';
    }
    
}

function unidadeUSP_inicio($client) {

    $query = '{
        "aggs": {
            "group_by_state": {
                "terms": {
                    "field": "unidadeUSPtrabalhos.keyword",
                    "order" : { "_term" : "asc" },
                    "size" : 150
                }
            }
        }
    }';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,
        'body' => $query
    ];    
    
    $response = $client->search($params); 
    
    $programas = [];
    $count = 1;
    $programas_pos = array('BIOENG', 'BIOENGENHARIA', 'BIOINFORM', 'BIOINFORMÁTICA', 'BIOTECNOL','BIOTECNOLOGIA','ECOAGROEC','ECOLOGIA APLICA','ECOLOGIA APLICADA','EE/EERP','EESC/IQSC/FMRP','ENERGIA','ENFERM','ENFERMA','ENG DE MATERIAI','ENG DE MATERIAIS','ENGMAT','ENSCIENC','ENSINO CIÊNCIAS','EP/FEA/IEE/IF','ESTHISART','INTER - ENFERMA','IPEN','MAE/MAC/MP/MZ','MODMATFIN','MUSEOLOGIA','NUTHUMANA','NUTRIÇÃO HUMANA','PROCAM','PROLAM','ESTÉTICA HIST.','FCF/FEA/FSP','IB/ICB','HRACF','LASERODON');
    foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {        
        if (in_array($facets['key'],$programas_pos)) {        
          $programas[] =  '<li><a href="result.php?unidadeUSPtrabalhos[]='.strtoupper($facets['key']).'">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        } else { 
            echo '<li><a href="result.php?unidadeUSPtrabalhos[]='.strtoupper($facets['key']).'">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }
        
       if ($count == 12)
            {  
                 echo '<div id="unidades" class="uk-hidden uk-list uk-list-striped">';
            }
        $count++;
    }
    
    if (!empty($programas)) {
        echo '<li><b>Programas de Pós-Graduação</b></li>';
        echo implode("",$programas);
    }
    
    if ($count > 7) {
        echo '</div>';
        echo '<button class="uk-button" data-uk-toggle="{target:\'#unidades\'}">Ver todas as unidades</button>';
    }
     
}

function base_inicio($client) {

    $query = '{
        "aggs": {
            "group_by_state": {
                "terms": {
                    "field": "base.keyword",                    
                    "size" : 5
                }
            }
        }
    }';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,
        'body' => $query
    ];    
    
    $response = $client->search($params);
    foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
        echo '<li><a href="result.php?base[]='.$facets['key'].'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
    }   
    
}


function gerar_faceta($consulta,$url,$client,$field,$tamanho,$field_name,$sort) {
    $sort_query="";
    if (!empty($sort)){
         $sort_query = '"order" : { "_term" : "'.$sort.'" },';  
    }     

    
    $query = '{
        '.$consulta.'
        "aggs": {
            "counts": {
                "terms": {
                    "field": "'.$field.'.keyword",
                    '.$sort_query.'
                    "size" : '.$tamanho.'
                }
            }
        }
    }';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size'=> 0,          
        'body' => $query
    ];
    
    
    
    $response = $client->search($params);    
    
    echo '<li class="uk-parent">';    
    echo '<a href="#">'.$field_name.'</a>';
    echo ' <ul class="uk-nav-sub">';
    echo '<form>';
    //$count = 1;
    foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
        echo '<li class="uk-h6 uk-form-controls uk-form-controls-text">';
        echo '<p class="uk-form-controls-condensed">';
        echo '<input type="checkbox" name="'.$field.'[]" value="'.$facets['key'].'"><a href="'.$url.'&'.$field.'[]='.$facets['key'].'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a>';
        echo '</p>';
        echo '</li>';
        
        //if ($count == 11)
        //    {  
        //         echo '<div id="'.$campo.'" class="uk-hidden">';
        //    }
        //$count++;
    };
    //if ($count > 12) {
        //echo '</div>';
        //echo '<button class="uk-button" data-uk-toggle="{target:\'#'.$campo.'\'}">Ver mais</button>';
    //}

    echo '<input type="hidden" checked="checked" name="operator" value="AND">';
    echo '<button type="submit" class="uk-button-primary">Limitar facetas</button>';
    echo '</form>';
    echo   '</ul></li>';


}

function corrigir_faceta($consulta,$url,$server,$campo,$tamanho,$nome_do_campo,$sort) {

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
       
    $data = query_elastic($query,$server);
    
    echo '<li class="uk-parent">';
    echo '<a href="#">'.$nome_do_campo.'</a>';
    echo ' <ul class="uk-nav-sub">';
    foreach ($data["aggregations"]["counts"]["buckets"] as $facets) {
        echo '<li class="uk-h6">';        
        echo '<a href="autoridades.php?term='.$facets['key'].'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a>';
        echo '</li>';
    };
    echo   '</ul>
      </li>';

}

function gerar_faceta_range($consulta,$url,$server,$campo,$tamanho,$nome_do_campo,$sort) {

    if (!empty($sort)){
         
         $sort_query = '"order" : { "_term" : "'.$sort.'" },';  
        }
    $query = '
    {
        "size": 0,
        '.$consulta.'
        "aggs" : {
            "ranges" : {
                "range" : {
                    "field" : "'.$campo.'",
                    "ranges" : [
                        { "to" : 50 },
                        { "from" : 50, "to" : 100 },
                        { "from" : 100 }
                    ]
                }
            }
        }
     }
     ';
    
            
    $data = query_elastic($query,$server);
    
   
    echo '<div class="item">';
    echo '<a class="active title"><i class="dropdown icon"></i>'.$nome_do_campo.'</a>';
    echo '<div class="content">';
    echo '<div class="ui list">';
    foreach ($data["aggregations"]["counts"]["buckets"] as $facets) {
        echo '<div class="item">';
        echo '<a href="'.$url.'&'.$campo.'[]='.$facets['key'].'">'.$facets['key'].'</a><div class="ui label">'.$facets['doc_count'].'</div>';
        echo '</div>';
    };
    echo   '</div>
      </div>
  </div>';

}


/* Pegar o tipo de material */
function get_type($material_type){
  switch ($material_type) {
  case "ARTIGO DE JORNAL":
      return "article-newspaper";
      break;
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
  case "TRABALHO DE EVENTO":
      return "paper-conference";
      break;     
  case "TESE":
      return "thesis";
      break;          
  case "TEXTO NA WEB":
      return "post-weblog";
      break;
  }
}

/* Recupera os exemplares do DEDALUS */
function load_itens_new ($sysno) {
    $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
    if ($xml->error == "No associated items"){

    } else {
        echo "<h5>Exemplares físicos disponíveis nas Bibliotecas da USP</h5>";
        echo '<div id="exemplares'.$sysno.'">';
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
          echo "</tbody></table></div>";
          }
          flush();
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

/* Function to generate Graph Bar */
function generateDataGraphBar($client, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {

    if (!empty($sort)){
        $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';  
    }
    $query = '
    {
        '.$consulta.'
        "aggregations": {
          "counts": {
            "terms": {
              "field": "'.$campo.'.keyword",
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
    
    $facet = $client->search($params);    
    
    $data_array= array();
    foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
        array_push($data_array,'{"name":"'.$facets['key'].'","value":'.$facets['doc_count'].'}');
    };
    
    if ($campo == "year" ) {
        $data_array_inverse = array_reverse($data_array);
        $comma_separated = implode(",", $data_array_inverse);
    } else {
        $comma_separated = implode(",", $data_array);
    }

    return $comma_separated;

};

/* Function to generate Tables */
function generateDataTable($server,$url, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {
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
    
    $facet = query_elastic($query,$server);    



echo "<table class=\"uk-table\">
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
function generateCSV($server,$url, $consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {

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
    
    $facet = query_elastic($query,$server);   

    $data_array= array();
    foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
        array_push($data_array,''.$facets["key"].'\\t'.$facets["doc_count"].'');
    };
    $comma_separated = implode("\\n", $data_array);
    return $comma_separated;

};

/* Comparar registros */

function compararRegistros ($server,$query_type,$query_year,$query_title,$query_doi,$query_authors) {

    $query = '
    {
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$query_doi.'",
                            "type":       "cross_fields",
                            "fields":     [ "doi" ],
                            "minimum_should_match": "100%" 
                         }
                    },
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
    
    $result = query_elastic($query,$server);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
            echo '
                <tr>
                  <td>'.$query_type.'</td>
                  <td>'.$query_year.'</td>
                  <td>'.$query_title.'</td>
                  <td>'.$query_doi.'</td>
                  <td>'.$query_authors.'</td>
                  <td>'.$results["_source"]["type"].'</td>
                  <td>'.$results["_source"]["title"].'</td>
                  <td>'.$results["_source"]["doi"].'</td>
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
                  <td>'.$query_doi.'</td>
                  <td><p style="color:red">Não encontrado</p></td>
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

function compararRegistrosLattes ($server,$query_type,$query_year,$query_title,$query_doi,$query_authors,$codpes) {

    $query = '
    {
        "min_score": 0.7,
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$codpes.'",
                            "type":       "cross_fields",
                            "fields":     [ "codpesbusca" ],
                            "minimum_should_match": "100%" 
                         }
                    },
                    {
                        "multi_match" : {
                            "query":      "'.$query_doi.'",
                            "type":       "cross_fields",
                            "fields":     [ "doi","isbn" ]
                         }
                    },
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
    
    $result = query_elastic($query,$server);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
            echo '
                <tr>
                  <td>'.$query_type.'</td>
                  <td>'.$query_year.'</td>
                  <td>'.$query_title.'</td>';
                  if (!empty($query_doi)) {
                      echo '<td>'.$query_doi.'</td>';
                  } else {
                      echo '<td>Sem DOI</td>';
                  }  
                  
                  echo '<td>'.$query_authors.'</td>
                  <td>'.$results["_source"]["type"].'</td>
                  <td>'.$results["_source"]["title"].'</td>
                  <td>'.$results["_source"]["doi"][0].'</td>
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
                  <td>'.$query_title.'</td>';
                  if (!empty($query_doi)) {
                      echo '<td>'.$query_doi.'</td>';
                  } else {
                      echo '<td>Sem DOI</td>';
                  } 
                  echo '
                  <td><p style="color:red">Não encontrado</p></td>
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



function compararRegistrosScopus ($server,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

    $query = '
    {
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$query_DOI.'",
                            "type":       "cross_fields",
                            "fields":     [ "DOI" ]                            
                         }
                    },                
                    {
                        "multi_match" : {
                            "query":      "'.$query_title.'",
                            "type":       "cross_fields",
                            "fields":     [ "title" ],
                            "minimum_should_match": "90%" 
                         }
                    }
                ],
                "minimum_should_match" : 1                
            }
        }
    }
    ';
    
    $result = query_elastic($query,$server);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
        $row = [];
        $row[]= $query_year;
        $row[]= $query_type;
        $row[]= $query_title;
        $row[]= $query_DOI;
        $row[]= $query_authors;
        $row[]= $results["_source"]["type"];
        $row[]= $results["_source"]["title"];
        if (!empty($results["_source"]["doi"])){
            $row[]= implode("|",$results["_source"]["doi"]);
        } else {
            $row[] = "Sem DOI";
        }        
        $row[]= implode("|",$results["_source"]["authors"]);
        $row[]= $results["_source"]["year"];
        $row[]= $results["_score"];
        $row[]= $results["_id"];
        $row[]= implode("|",$results["_source"]["unidadeUSPtrabalhos"]);
        $result_row = implode("\\t", $row);
        $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $result_row ); 
        return $result_row;
        }
    } else {
            $row = ''.$query_year.'\\t'.$query_type.'\\t'.$query_title.'\\t'.$query_DOI.'\\t'.$query_authors.'\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado';
            $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $row );       
            return $result_row;
    }
}


function compararCSVScopus ($server,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

    $query = '
    {
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$query_DOI.'",
                            "type":       "cross_fields",
                            "fields":     [ "DOI" ]                            
                         }
                    },                
                    {
                        "multi_match" : {
                            "query":      "'.$query_title.'",
                            "type":       "cross_fields",
                            "fields":     [ "title" ],
                            "minimum_should_match": "90%" 
                         }
                    }
                ],
                "minimum_should_match" : 1                
            }
        }
    }
    ';
    
    $result = query_elastic($query,$server);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
        $row = [];
        $row[]= $query_year;
        $row[]= $query_type;
        $row[]= $query_title;
        $row[]= $query_DOI;
        $row[]= $query_authors;
        $row[]= $results["_source"]["type"];
        $row[]= $results["_source"]["title"];
        if (!empty($results["_source"]["doi"])){
            $row[]= implode("|",$results["_source"]["doi"]);
        } else {
            $row[] = "Sem DOI";
        }        
        $row[]= implode("|",$results["_source"]["authors"]);
        $row[]= $results["_source"]["year"];
        $row[]= $results["_score"];
        $row[]= $results["_id"];
        $row[]= implode("|",$results["_source"]["unidadeUSPtrabalhos"]);
        $result_row = implode("\\t", $row);
        $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $result_row ); 
        return $result_row;
        }
    } else {
            $row = ''.$query_year.'\\t'.$query_type.'\\t'.$query_title.'\\t'.$query_DOI.'\\t'.$query_authors.'\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado';
            $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $row );       
            return $result_row;
    }
}


function compararRegistrosWos ($server,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

    $query = '
    {
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$query_DOI.'",
                            "type":       "cross_fields",
                            "fields":     [ "DOI" ]                            
                         }
                    },                
                    {
                        "multi_match" : {
                            "query":      "'.$query_title.'",
                            "type":       "cross_fields",
                            "fields":     [ "title" ],
                            "minimum_should_match": "90%" 
                         }
                    }
                ],
                "minimum_should_match" : 1                
            }
        }
    }
    ';
    
    $result = query_elastic($query,$server);
        
    if ($result["hits"]["total"] > 0) {
    
    foreach ($result['hits']['hits'] as $results) {
        $row = [];
        $row[]= $query_year;
        $row[]= $query_type;
        $row[]= $query_title;
        $row[]= $query_DOI;
        $row[]= $query_authors;
        $row[]= $results["_source"]["type"];
        $row[]= $results["_source"]["title"];
        if (!empty($results["_source"]["doi"])){
            $row[]= implode("|",$results["_source"]["doi"]);
        } else {
            $row[] = "Sem DOI";
        }        
        $row[]= implode("|",$results["_source"]["authors"]);
        $row[]= $results["_source"]["year"];
        $row[]= $results["_score"];
        $row[]= $results["_id"];
        $row[]= implode("|",$results["_source"]["unidadeUSPtrabalhos"]);
        $result_row = implode("\\t", $row);
        $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $result_row ); 
        return $result_row;
        }
    } else {
            $row = ''.$query_year.'\\t'.$query_type.'\\t'.$query_title.'\\t'.$query_DOI.'\\t'.$query_authors.'\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado\\tNão encontrado';
            $result_row = preg_replace( "/\r|\n|\'|\)|\(|\>|\"|\"\"/", "", $row );       
            return $result_row;
    }
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

function analisa_get($get) {
    
    $new_get = $get;
    
    /* Missing query */
    foreach ($get as $k => $v){
        if($v == 'N/D'){
            $filter[] = '{"missing" : { "field" : "'.$k.'" }}';
            unset($get[$k]);
        }
    }    
    
    /* limpar base all */
    if (isset($get['base']) && $get['base'][0] == 'all'){
        unset($get['base']);
        unset($new_get['base']);
    }    

    /* Subject */
    if (isset($get['assunto'])){   
        $get['subject'][] = $get['assunto'];
        $new_get['subject'][] = $get['assunto'];
        unset($get['assunto']);
        unset($new_get['assunto']);
    }    
    
    /* Pagination */
    if (isset($get['page'])) {
        $page = $get['page'];
        unset($get['page']);
        unset($new_get['page']);
    } else {
        $page = 1;
    }
    
    /* Empty search_index */
    if (empty($get['search_index'])){
        unset($get['search_index']);
        unset($new_get['search_index']);
    }
    
    /* Pagination variables */

    $limit = 20;
    $skip = ($page - 1) * $limit;
    $next = ($page + 1);
    $prev = ($page - 1);
    $sort = array('year' => -1);    
    
     if (!empty($get["date_init"])||(!empty($get["date_end"]))) {
        $filter[] = '
        {
            "range" : {
                "year" : {
                    "gte" : '.$get["date_init"].',
                    "lte" : '.$get["date_end"].'
                }
            }
        }
        ';
        $novo_get[] = 'date_init='.$new_get['date_init'].'';
        $novo_get[] = 'date_end='.$new_get['date_end'].''; 
        $data_inicio = $get["date_init"];
        $data_fim = $get["date_end"];
        unset($new_get["date_init"]);
        unset($new_get["date_end"]);         
        unset($get["date_init"]);
        unset($get["date_end"]);
    }
    
    if (count($get) == 0) {
        
        $query_complete = '{   
            "query": {    
                 "match_all": {}
             },
            "sort":[
                {"year.keyword":"desc"},
                {"_uid":"desc"}
            ]
        }';
        

        $query_aggregate = '
           "query": {    
                 "match_all": {}
             },
        ';        
       
        
    } elseif (!empty($get['search_index'])) {
        $search_term = '
            "multi_match" : {
                "query":      "'.$get['search_index'].'",
                "type":       "cross_fields",
                "fields":     [ "title", "authors_index", "subject", "resumo" ],
                "operator":   "and"
            }   
        ';
        
        unset($get['search_index']);

        $filter = []; 
        foreach ($get as $key => $value) {
           if (count($value) > 1){
               foreach ($value as $valor){
                    $filter[] = '{"term":{"'.$key.'.keyword":"'.$valor.'"}}';
                }               
           } else {
               $filter[] = '{"term":{"'.$key.'.keyword":"'.$value[0].'"}}';
           }
            
        }
        
        if (count($filter) > 0) {
            $filter_query = ''.implode(",", $filter).''; 
        } else {
            $filter_query = '';
        }


        $query_complete = '{
            "sort" : [
                    { "year.keyword" : "desc" }
                ],    
            "query": {    
                "bool": {
                  "must": {
                    '.$search_term.'
                  },
                  "filter":[
                    '.$filter_query.'        
                    ]
                  }
            }
        }';


        $query_aggregate = '
            "query": {
                "bool": {
                  "must": {
                    '.$search_term.'
                  },
                  "filter":[
                    '.$filter_query.'
                    ]
                  }
                },
        ';


    } elseif (!empty($get['operator'])) {
        
        unset($get['operator']);
        unset($new_get['operator']);
        
        foreach ($get as $key => $value){
                $key = $key;
                $value_array[] = $value;                
        } 
            $query_part = '{"'.$key.'.keyword":["'.implode('","',$value_array[0]).'"]}';

            $query_complete = '
                {
                "sort" : [
                    { "year.keyword" : "desc" }
                ],    
                "query" : {
                    "bool" : {
                        "filter" : {
                            "terms":
                                 '.$query_part.'
                        }
                    }
                },
                "from": '.$skip.',
                "size": '.$limit.'
                }    
                ';

            $query_aggregate = '
                "query" : {
                    "bool" : {
                        "filter" : {
                            "terms":
                                 '.$query_part.'
                        }
                    }
                },
            ';          
        
        
    } else {
        
        foreach ($get as $key => $value) {

            $conta_value = count($value);
            $get_query1 = [];
            
            if ($conta_value > 1) {
                foreach ($value as $valor){
                    $get_query1[] = '{"term":{"'.$key.'.keyword":"'.$valor.'"}}';
                }                        
            } else {
                 foreach ($value as $valor){
                     $filter[] = '{"term":{"'.$key.'.keyword":"'.$valor.'"}}';
                 }
            }       
        }
    
        $query_part = '"must" : ['.implode(",",$get_query1).']';
        $query_part2 = implode(",",$filter);

        $query_complete = '
                    {
                       "sort" : [
                           { "year.keyword" : "desc" }
                       ],    
                       "query" : {
                          "constant_score" : {
                             "filter" : {
                                "bool" : {
                                  "should" : [
                                    { "bool" : {
                                    '.$query_part.'
                                   }} 
                                  ],
                                  "filter": [
                                    '.$query_part2.'
                                  ]
                               }
                             }
                          }
                       },
                      "from": '.$skip.',
                      "size": '.$limit.'
                    }    
        ';
        
        $query_aggregate = '
                    "query" : {
                      "constant_score" : {
                         "filter" : {
                            "bool" : {
                              "should" : [
                                { "bool" : {
                                '.$query_part.'
                               }} 
                              ],
                              "filter": [
                                '.$query_part2.'
                              ]
                           }
                         }
                      }
                   },
    ';
    }
        
/* Pegar a URL atual */
    
    
if (isset($new_get)){
    
   $novo_get="";
    if (!empty($new_get['search_index'])){
        $novo_get[] = 'search_index='.$new_get['search_index'].'';
        $termo_consulta = $new_get['search_index'];
        unset($new_get['search_index']);
    }  
    
    foreach ($new_get as $key => $value){
        $novo_get[] = ''.$key.'[]='.$value[0].'';        
    } 
    if (!empty($novo_get)){
        $pega_get = implode("&",$novo_get); 
    } else {
        $pega_get = "";
    }
       
    $url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['PHP_SELF'].'?'.$pega_get.'';

} else {
    $url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['PHP_SELF'].'';
}
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');     
    
    return compact('page','get','new_get','query_complete','query_aggregate','url','escaped_url','limit','termo_consulta','data_inicio','data_fim','skip');
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
        foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
            $string_down_array[] = '<a href="result.php?assunto='.$string_down->{'string'}.'">'.$string_down->{'string'}.'</a>';     
        };
        echo 'Ou pesquisar pelo assuntos mais específicos: ';
        print_r(implode(" - ",$string_down_array));

    } else {
        $termo_naocorrigido[] = $termo_limpo;
    }
}

function gera_consulta_citacao($citacao) {

    $type = get_type($citacao["type"]);
    $author_array = array();
    foreach ($citacao["authors"] as $autor_citation){
        $array_authors = explode(',', $autor_citation);
        $author_array[] = '{"family":"'.$array_authors[0].'","given":"'.$array_authors[1].'"}';
    };
    $authors = implode(",",$author_array);

    if (!empty($citacao["ispartof"])) {
        $container = '"container-title": "'.$citacao["ispartof"].'",';
    } else {
        $container = "";
    };
    if (!empty($citacao["doi"])) {
        $doi = '"DOI": "'.$citacao["doi"][0].'",';
    } else {
        $doi = "";
    };

    if (!empty($citacao["url"])) {
        $url = '"URL": "'.$citacao["url"][0].'",';
    } else {
        $url = "";
    };

    if (!empty($citacao["publisher"])) {
        $publisher = '"publisher": "'.$citacao["publisher"].'",';
    } else {
        $publisher = "";
    };

    if (!empty($citacao["publisher_place"])) {
        $publisher_place = '"publisher-place": "'.$citacao["publisher_place"].'",';
    } else {
        $publisher_place = "";
    };

    $volume = "";
    $issue = "";
    $page_ispartof = "";

    if (!empty($citacao["ispartof_data"])) {
        foreach ($citacao["ispartof_data"] as $ispartof_data) {
            if (strpos($ispartof_data, 'v.') !== false) {
                $volume = '"volume": "'.str_replace("v.","",$ispartof_data).'",';
            } elseif (strpos($ispartof_data, 'n.') !== false) {
                $issue = '"issue": "'.str_replace("n.","",$ispartof_data).'",';
            } elseif (strpos($ispartof_data, 'p.') !== false) {
                $page_ispartof = '"page": "'.str_replace("p.","",$ispartof_data).'",';
            }
        }
    }

    $data = json_decode('{
    "title": "'.$citacao["title"].'",
    "type": "'.$type.'",
    '.$container.'
    '.$doi.'
    '.$url.'
    '.$publisher.'
    '.$publisher_place.'
    '.$volume.'
    '.$issue.'
    '.$page_ispartof.'
    "issued": {
    "date-parts": [
    [
    "'.$citacao["year"].'"
    ]
    ]
    },
    "author": [
    '.$authors.'
    ]
    }');
    
    return $data;    
    
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

function metrics_update($server,$_id,$metrics_array){    
    $ch = curl_init();
    $method = "POST";    
    $url = "http://$server/sibi/producao/$_id/_update";
       $query = 
             '{
                "doc":{
                    "metrics" : {
                        '.implode(",",$metrics_array).'
                    },
                    "date":"'.date("Y-m-d").'"
                },                    
                "doc_as_upsert" : true
            }';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 9200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    $result = curl_exec($ch);
    //var_dump($result);
    curl_close($ch); 
}



?>
