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
    
    static function base_inicio() {
        global $type;
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
        $response = elasticsearch::elastic_search($type,null,0,$query);
        foreach ($response["aggregations"]["group_by_state"]["buckets"] as $facets) {
            echo '<li><a href="result.php?search[]=base.keyword:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
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
                echo '<a href="single.php?_id='.$r['_id'].'"><h4 class="uk-comment-title uk-margin-remove">'.$r["_source"]['name'].'';
                if (!empty($r["_source"]['datePublished'])){
                   echo ' ('.$r["_source"]['datePublished'].')';
                }         
                echo '</h4></a>';
            };
            echo '<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-small">';
            if (!empty($r["_source"]['author'])) { 
            foreach ($r["_source"]['author'] as $autores) {
            echo '<li><a href="result.php?search[]=author.person.name.keyword:&quot;'.$autores["person"]["name"].'&quot;">'.$autores["person"]["name"].'</a></li>';
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

class paginaSingle {

    public function counter ($_id,$client) {
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

    public function uploader () {
        global $_GET;
        global $_POST;
        global $_FILES;


        if (!is_dir('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'')){
            mkdir('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'', 0700, true);
        }
        
        $uploaddir = 'upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'/';
        $count_files = count(glob('upload/'.$_GET['_id'][0].'/'.$_GET['_id'][1].'/'.$_GET['_id'][2].'/'.$_GET['_id'][3].'/'.$_GET['_id'][4].'/'.$_GET['_id'][5].'/'.$_GET['_id'][6].'/'.$_GET['_id'][7].'/'.$_GET['_id'].'/*',GLOB_BRACE));
        $rights = '{"rights":"'.$_POST["rights"].'"},';
        
        if (!empty($_POST["embargo_date"])){
            $embargo_date = '{"embargo_date":"'.$_POST["embargo_date"].'"},';
        } else {
            $embargo_date = '{"embargo_date":""},';
        }
        
        if ($_FILES['upload_file']['type'] == 'application/pdf'){
            $uploadfile = $uploaddir . basename($_GET['_id'] . "_" . ($count_files+1) . ".pdf");
        } else {
            $uploadfile = $uploaddir . basename($_GET['_id'] . "_" . ($count_files+1) . ".pptx");
        }    
        
        if ($_FILES['upload_file']['type'] == 'application/pdf'||$_FILES['upload_file']['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
            //echo '<pre>';
            if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
                $query = 
                '
                {
                    "doc":{
                        "sysno":"'.$_GET['_id'].'",
                        "file_info" :[ 
                            {"num_usp":"'.$_SESSION['oauthuserdata']->{'loginUsuario'}.'"},
                            {"name_file":"'.$_FILES['upload_file']['name'].'"},
                            '.$rights.'
                            '.$embargo_date.'
                            {"file_type":"'.$_FILES['upload_file']['type'].'"}
                        ],
                        "date_file":"'.date("Y-m-d").'"
                    },                    
                    "doc_as_upsert" : true
                }
                ';
                            
                $params = [
                    'index' => 'sibi',
                    'type' => 'files',
                    'id' => $uploadfile,
                    'parent' => $_GET['_id'],
                    'body' => $query
                ];
                $response_upload = $client->update($params); 
                
                
                $myfile = fopen("$uploadfile.json", "w") or die("Unable to open file!");
                $txt = $query;
                fwrite($myfile, $txt);
                fclose($myfile);
                
                
                
            } else {
                echo "Possível ataque de upload de arquivo!\n";
            }
        }
        
        //echo 'Aqui está mais informações de debug:';
        //print_r($_FILES);
    //print "</pre>";  

    }

}

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
    static function load_itens_aleph ($sysno) {
        $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
        if ($xml->error == "No associated items"){

        } else {


            echo '<div id="exemplares'.$sysno.'">';
            echo "<table class=\"uk-table uk-table-small uk-text-small uk-table-striped\">
                        <caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>
                        <thead>
                          <tr>
                            <th><small>Biblioteca</small></th>
                            <th><small>Cód. de barras</small></th>
                            <th><small>Status</small></th>
                            <th><small>Núm. de chamada</small></th>";
                            if ($xml->item->{'loan-status'} == "A"){
                            echo "<th><small>Status</small></th>
                            <th><small>Data provável de devolução</small></th>";
                          } else {
                            echo "<th><small>Disponibilidade</small></th>";
                          }
                          echo "</tr>
                        </thead>
                      <tbody>";
              foreach ($xml->item as $item) {
                echo '<tr>';
                echo '<td><small>'.$item->{'sub-library'}.'</small></td>';
                echo '<td><small>'.$item->{'barcode'}.'</small></td>';
                echo '<td><small>'.$item->{'item-status'}.'</small></td>';
                echo '<td><small>'.$item->{'call-no-1'}.'</small></td>';
                if ($item->{'loan-status'} == "A"){
                echo '<td><small>Emprestado</small></td>';
                echo '<td><small>'.$item->{'loan-due-date'}.'</small></td>';
              } else {
                echo '<td><small>Disponível</small></td>';
              }
                echo '</tr>';
              }
              echo "</tbody></table></div>";
              }
              flush();
      }
    


    static function get_fulltext_file($id,$session){
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
    
    
}

class USP {

    /* Consulta o Vocabulário Controlado da USP */
    static function consultar_vcusp($termo) {
        echo '<h4>Vocabulário Controlado do SIBiUSP</h4>';
        $xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');

        if ($xml->{'resume'}->{'cant_result'} != 0) {

            $termo_xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchUp&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            foreach (($termo_xml->{'result'}->{'term'}) as $string_up) {
                $string_up_array[] = '<a href="result.php?search[]=subject.keyword:&quot;'.$string_up->{'string'}.'&quot;">'.$string_up->{'string'}.'</a>';    
            };
            echo 'Você também pode pesquisar pelos termos mais genéricos: ';
            print_r(implode(" -> ",$string_up_array));
            echo '<br/>';
            $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            if (!empty($termo_xml_down->{'result'}->{'term'})){
                foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                    $string_down_array[] = '<a href="result.php?search[]=subject.keyword:&quot;'.$string_down->{'string'}.'&quot;">'.$string_down->{'string'}.'</a>';     
                };
                echo 'Ou pesquisar pelo assuntos mais específicos: ';
                print_r(implode(" - ",$string_down_array));            
            }


        } else {
            $termo_naocorrigido[] = $termo_limpo;
        }
    }      
    
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

class autoridades {
    
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
    
}


class API {
    

    static function get_title_elsevier($issn,$api_elsevier) {
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

    static function get_articlefull_elsevier($doi,$api_elsevier) {
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

    static function get_citations_elsevier($doi,$api_elsevier) {
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

    static function get_oadoi($doi) {
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

    static function metrics_update($client,$_id,$metrics_array){    

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

    static function store_issn_info($client,$issn,$issn_info){    

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
    
    
    
    
}

class exporters {
    static function RIS($cursor) {

        $record = [];
        switch ($cursor["_source"]["type"]) {
        case "ARTIGO DE PERIODICO":
            $record[] = "TY  - JOUR";
            break;
        case "PARTE DE MONOGRAFIA/LIVRO":
            $record[] = "TY  - CHAP";
            break;
        case "TRABALHO DE EVENTO-RESUMO":
            $record[] = "TY  - CPAPER";
            break;
        case "TEXTO NA WEB":
            $record[] = "TY  - ICOMM";
            break;
        }

        $record[] = "TI  - ".$cursor["_source"]['name']."";

        if (!empty($cursor["_source"]['datePublished'])) {
        $record[] = "PY  - ".$cursor["_source"]['datePublished']."";
        }

        foreach ($cursor["_source"]['author'] as $autores) {
        $record[] = "AU  - ".$autores["person"]["name"]."";
        }

        if (!empty($cursor["_source"]['ispartof'])) {
        $record[] = "T2  - ".$cursor["_source"]['ispartof']."";
        }

        if (!empty($cursor["_source"]['issn'][0])) {
        $record[] = "SN  - ".$cursor["_source"]['issn'][0]."";
        }

        if (!empty($cursor["_source"]["doi"])) {
        $record[] = "DO  - ".$cursor["_source"]["doi"][0]."";
        }

        if (!empty($cursor["_source"]["url"])) {
        $record[] = "UR  - ".$cursor["_source"]["url"][0]."";
        }

        if (!empty($cursor["_source"]["publisher-place"])) {
        $record[] = "PP  - ".$cursor["_source"]["publisher-place"]."";
        }

        if (!empty($cursor["_source"]["publisher"]["organization"]["name"])) {
            $record[] = "PB  - ".$cursor["_source"]["publisher"]["organization"]["name"]."";
        }

        if (!empty($cursor["_source"]["ispartof_data"])) {
        foreach ($cursor["_source"]["ispartof_data"] as $ispartof_data) {
            if (strpos($ispartof_data, 'v.') !== false) {
            $record[] = "VL  - ".str_replace("v.","",$ispartof_data)."";
            } elseif (strpos($ispartof_data, 'n.') !== false) {
            $record[] = "IS  - ".str_replace("n.","",$ispartof_data)."";
            } elseif (strpos($ispartof_data, 'p.') !== false) {
            $record[] = "SP  - ".str_replace("p.","",$ispartof_data)."";
            }
        }
        }
        $record[] = "ER  - ";

        $record_blob = implode("\\n", $record);

        return $record_blob;

    }
}


?>
