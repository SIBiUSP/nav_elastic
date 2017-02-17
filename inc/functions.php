<?php

include('functions_core.php');

/**
 * Classe de funções da página inicial
 */
class paginaInicial {
    
    static function contar_registros () {
        $query_all = '
            {
                "query": {
                    "match_all": {}
                }
            }        
        ';
        $response = elasticsearch::elastic_search("producao",null,0,$query_all);
        return $response['hits']['total'];
        print_r($response);

    }
    
    static function contar_arquivos () {

        $query_all = '
            {
                "query": {
                    "match_all": {}
                }
            }        
        ';
        $response = elasticsearch::elastic_search("files",null,0,$query_all);
        return $response['hits']['total'];
        print_r($response);

    } 

    static function contar_unicos ($field) {

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
        $response = elasticsearch::elastic_search("producao",null,0,$count_distinct_query);
        return $response["aggregations"]["distinct_authors"]["value"];

    }

    
    static function unidadeUSP_inicio() {

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

        $response = elasticsearch::elastic_search("producao",null,0,$query);

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
    
    static function base_inicio() {

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
        $response = elasticsearch::elastic_search("producao",null,0,$query);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result.php?search[]=base.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }   

    }    
    
    static function ultimos_registros() {

        $query = '{
                    "query": {
                        "match_all": {}
                     },
                    "sort" : [
                        {"_uid" : {"order" : "desc"}}
                        ]
                    }';
        $response = elasticsearch::elastic_search("producao",null,11,$query);

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
    
    static function card_unidade ($sigla,$nome_unidade) {
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

?>
