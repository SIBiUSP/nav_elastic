<?php

include('functions_core/functions_core.php');

/**
 * Classe de funções da página inicial
 */
class paginaInicial {
    
    static function contar_registros () {
        global $type;
        $query_all = '
            {
                "query": {
                    "match_all": {}
                }
            }        
        ';
        $response = elasticsearch::elastic_search($type,null,0,$query_all);
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
        global $type;
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
        $response = elasticsearch::elastic_search($type,null,0,$count_distinct_query);
        return $response["aggregations"]["distinct_authors"]["value"];

    }

    
    static function unidadeUSP_inicio() {
        global $type;
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

        $response = elasticsearch::elastic_search($type,null,0,$query);

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
    
    static function facet_inicio($field) 
    {
        global $type;
        $query = '{
            "aggs": {
                "group_by_state": {
                    "terms": {
                        "field": "'.$field.'.keyword",                    
                        "size" : 10
                    }
                }
            }
        }';
        $response = elasticsearch::elastic_search($type, null, 0, $query);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result.php?filter[]='.$field.':&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'], 0, ',', '.').')</a></li>';
        }   

    }    
    
    static function ultimos_registros() {
        global $type;
        $query = '{
                    "query": {
                        "match_all": {}
                     },
                    "sort" : [
                        {"_uid" : {"order" : "desc"}}
                        ]
                    }';
        $response = elasticsearch::elastic_search($type,null,11,$query);

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
            if (!empty($r["_source"]['name'])){
                echo '<a href="http://dedalus.usp.br/F/?func=direct&doc_number='.$r['_id'].'" target="_blank"><h4 class="uk-comment-title uk-margin-remove">'.$r["_source"]['name'].'';
                if (!empty($r["_source"]['datePublished'])){
                   echo ' ('.$r["_source"]['datePublished'].')';
                }         
                echo '</h4></a>';
            };
            echo '<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-small">';
            if (!empty($r["_source"]['author'])) { 
            foreach ($r["_source"]['author'] as $autores) {
            echo '<li><a href="result.php?search[]=authors.keyword:&quot;'.$autores["person"]["name"].'&quot;">'.$autores["person"]["name"].'</a></li>';
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

?>
