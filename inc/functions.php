<?php

/**
 * Classe de interação com o Elasticsearch
 */
class elasticsearch {
    /**
     * Executa o commando get no Elasticsearch
     * 
     * @param string $_id ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch                         
     * @param string[] $fields Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * 
     */
    public static function elastic_get ($_id,$type,$fields) {
        global $index;
        global $client;
        if (!defined('type_constant')) define('type_constant', ''.$type.'');
        //define('fields', ''.$fields.'');
        $params = [];
        $params["index"] = $index;
        $params["type"] = type_constant;
        $params["id"] = $_id;
        $params["_source"] = $fields;
        
        $response = $client->get($params);        
        return $response;    
    }   
}


function query_one_elastic ($_id,$client) {
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'id' => ''.$_id.''
    ];
    $response = $client->get($params);
    return $response;    

}

function counter ($_id,$client) {
    $query = 
    '
    {
        "script" : {
            "inline": "ctx._source.counter += params.count",
            "lang": "painless",
            "params" : {
                "count" : 1
            }
        },
        "upsert" : {
            "counter" : 1
        }
    }
    ';  
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao_metrics',
        'id' => $_id,
        'body' => $query
    ];
    $response = $client->update($params);        
    //print_r($response);
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

function contar_arquivos ($client) {

    $query_all = '
        {
            "query": {
                "match_all": {}
            }
        }        
    ';
    $params = [
        'index' => 'sibi',
        'type' => 'files',
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
                  "field" : "'.$field.'.keyword"
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
    return $response["aggregations"]["distinct_authors"]["value"];
    
}

function store_user ($userdata,$client){
    
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
    
    $num_usp = $userdata->{'loginUsuario'};
    $params = [
        'index' => 'sibi',
        'type' => 'users',
        'id' => "$num_usp",
        'body' => $query
    ];
    $response = $client->update($params);   
 
}

function ultimos_registros($client) {
    
    $query = '{
                "query": {
                    "match_all": {}
                 },
                "sort" : [
                    {"_uid" : {"order" : "desc"}}
                    ]
                }';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',
        'size' => 11,
        'body' => $query
    ];
    $response = $client->search($params); 
    
    foreach ($response["hits"]["hits"] as $r){
        echo '<article class="uk-comment">
        <header class="uk-comment-header uk-grid-medium uk-flex-middle" uk-grid>';    
        if (!empty($r["_source"]['unidadeUSP'])) {
            $file = 'inc/images/logosusp/'.$r["_source"]['unidadeUSP'][0].'.jpg';
        } else {
            $file = "";
        }
        if (file_exists($file)) {
        echo '<div class="uk-width-auto"><img class="uk-comment-avatar" src="'.$file.'" width="80" height="80" alt=""></div>';
        } else {

        };
        echo '<div class="uk-width-expand">';
        if (!empty($r["_source"]['title'])){
        echo '<a href="single.php?_id='.$r['_id'].'"><h4 class="uk-comment-title uk-margin-remove">'.$r["_source"]['title'].' ('.$r["_source"]['year'].')</h4></a>';
        };
        echo '<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-small">';
        if (!empty($r["_source"]['authors'])) { 
        foreach ($r["_source"]['authors'] as $autores) {
        echo '<li><a href="result.php?search[]=authors.keyword:&quot;'.$autores.'&quot;">'.$autores.'</a></li>';
        }
        echo '</ul></div>';     
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
    $programas_pos = array('BIOENG', 'BIOENGENHARIA', 'BIOINFORM', 'BIOINFORMÁTICA', 'BIOTECNOL','BIOTECNOLOGIA','ECOAGROEC','ECOLOGIA APLICA','ECOLOGIA APLICADA','EE/EERP','EESC/IQSC/FMRP','ENERGIA','ENFERM','ENFERMA','ENG DE MATERIAI','ENG DE MATERIAIS','ENGMAT','ENSCIENC','ENSINO CIÊNCIAS','EP/FEA/IEE/IF','ESTHISART','INTER - ENFERMA','IPEN','MAE/MAC/MP/MZ','MODMATFIN','MUSEOLOGIA','NUTHUMANA','NUTRIÇÃO HUMANA','PROCAM','PROLAM','ESTÉTICA HIST.','FCF/FEA/FSP','IB/ICB','HRACF','LASERODON','EP/IB/ICB/IQ/BUTANT /IPT','FO/EE/FSP');
    foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {        
        if (in_array($facets['key'],$programas_pos)) {        
          $programas[] =  '<li><a href="result.php?search[]=unidadeUSPtrabalhos:&quot;'.strtoupper($facets['key']).'&quot;">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        } else { 
            echo '<li><a href="result.php?search[]=unidadeUSPtrabalhos:&quot;'.strtoupper($facets['key']).'&quot;">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }
        
       if ($count == 12)
            {  
                 echo '<div id="unidades" class="uk-list uk-list-striped" hidden>';
            }
        $count++;
    }
    
    if (!empty($programas)) {
        echo '<li><b>Programas de Pós-Graduação Interunidades</b></li>';
        echo implode("",$programas);
    }
    
    if ($count > 7) {
        echo '</div>';
        echo '<button uk-toggle="target: #unidades">Ver todas as unidades</button>';
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
        echo '<li><a href="result.php?search[]=base.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
    }   
    
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

/* Comparar registros */
function compararRegistros ($client,$query_type,$query_year,$query_title,$query_doi,$query_authors) {

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
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',   
        'body' => $query
    ];
    
    $response = $client->search($params);   
        
    if ($response["hits"]["total"] > 0) {
    
        foreach ($response['hits']['hits'] as $results) {
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

function compararRegistrosLattes ($client,$query_type,$query_year,$query_title,$query_doi,$query_authors,$codpes) {

    $query = '
    {
        "min_score": 20,
        "query":{
            "bool": {
                "should": [
                    {
                        "multi_match" : {
                            "query":      "'.$codpes.'",
                            "type":       "cross_fields",
                            "fields":     [ "codpes" ],
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
                            "query":      "'.$query_year.'",
                            "type":       "best_fields",
                            "fields":     [ "year" ],
                            "minimum_should_match": "75%" 
                        }
                    }
                ],
                "minimum_should_match" : 2               
            }
        }
    }
    ';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',   
        'body' => $query
    ];
    
    $response = $client->search($params);     
        
    if ($response["hits"]["total"] > 0) {
    
    foreach ($response['hits']['hits'] as $results) {
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
                  <td>'.$results["_source"]["title"].'</td>';
                    if (!empty($results["_source"]["doi"])) {
                        echo '<td>'.$results["_source"]["doi"][0].'</td>';
                    } else {
                        echo '<td></td>';
                    }       
        
                  echo '<td>'. implode("|",$results["_source"]["authors"]).'</td>
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

function compararRegistrosScopus ($client,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

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

    $params = [
        'index' => 'sibi',
        'type' => 'producao',   
        'body' => $query
    ];
    
    $response = $client->search($params);     
        
    if ($response["hits"]["total"] > 0) {
    
    foreach ($response['hits']['hits'] as $results) {
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

function compararCSVScopus ($client,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

    $query = '
    {
        "min_score": 20,
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
                    },
                    {
                        "multi_match" : {
                            "query":      "'.$query_year.'",
                            "type":       "cross_fields",
                            "fields":     [ "year" ],
                            "minimum_should_match": "75%" 
                         }
                    } 
                ],
                "minimum_should_match" : 2                
            }
        }
    }
    ';
    
    $params = [
        'index' => 'sibi',
        'type' => 'producao',   
        'body' => $query
    ];
    
    $response = $client->search($params); 
        
    if ($response["hits"]["total"] > 0) {
    
        foreach ($response['hits']['hits'] as $results) {
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

function compararRegistrosWos ($client,$query_type,$query_year,$query_title,$query_authors,$query_DOI) {

    $query = '
    {
        "min_score": 20,
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
                    },
                    {
                        "multi_match" : {
                            "query":      "'.$query_year.'",
                            "type":       "cross_fields",
                            "fields":     [ "year" ],
                            "minimum_should_match": "75%" 
                         }
                    } 
                ],
                "minimum_should_match" : 2                
            }
        }
    }
    ';

    $params = [
        'index' => 'sibi',
        'type' => 'producao',   
        'body' => $query
    ];
    
    $response = $client->search($params); 
        
    if ($response["hits"]["total"] > 0) {
    
        foreach ($response['hits']['hits'] as $results) {
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
    
    $search_fields = "";
    if (!empty($get['fields'])) {
        $search_fields = implode('","',$get['fields']);  
    } else {            
        $search_fields = "_all";
    }    
    
    if (!empty($get['search'])){
        $get['search'] = str_replace('"','\"',$get['search']);
    }
    

    /* Pagination */
    if (isset($get['page'])) {
        $page = $get['page'];
        unset($get['page']);
    } else {
        $page = 1;
    }
    
    /* Pagination variables */

    $limit = 20;
    $skip = ($page - 1) * $limit;
    $next = ($page + 1);
    $prev = ($page - 1);
    $sort = array('year' => -1);       
    
    if (!empty($get['codpes'])){        
        $get['search'][] = 'codpes:'.$get['codpes'].'';
    }
    
    if (!empty($get['assunto'])){        
        $get['search'][] = 'subject:\"'.$get['assunto'].'\"';
    }    
    
    if (!empty($get['search'])){
        $query = implode(" ", $get['search']); 
    } else {
        $query = "*";
    }
    
    $search_term = '
        "query_string" : {
            "fields" : ["'.$search_fields.'"],
            "query" : "'.$query.'",
            "default_operator": "AND",
            "analyzer":"portuguese",
            "phrase_slop":10
        }                
    ';    
    
    $query_complete = '{
        "sort" : [
                { "year.keyword" : "desc" }
            ],    
        "query": {
        '.$search_term.'
        }
    }';

    $query_aggregate = '
        "query": {
            '.$search_term.'
        },
    ';
 
    return compact('page','get','new_get','query_complete','query_aggregate','url','escaped_url','limit','termo_consulta','data_inicio','data_fim','skip');

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

function card_unidade ($sigla,$nome_unidade) {
    $card = '
    <div class="uk-text-center">
        <a href="result.php?search[]=unidadeUSPtrabalhos:'.$sigla.'">
        <div class="uk-inline-clip uk-transition-toggle">
            <img src="inc/images/fotosusp/'.$sigla.'.jpg" alt="">
            <div class="uk-transition-fade uk-position-cover uk-position-small uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle">
                <p class="uk-h6 uk-margin-remove">'.$nome_unidade.'</p>
            </div>
        </div>
        <p class="uk-margin-small-top">'.$sigla.'</p>
        </a>
    </div>
    ';
    return $card;
}


?>
