<?php

include('functions_core/functions_core.php');

/**
 * Classe de funções da página inicial
 */
class paginaInicial {
    
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
            echo '<div class="uk-width-auto"><img class="uk-comment-avatar" src="'.$file.'" width="60" height="60" alt=""></div>';
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

    public static function uploader () {
        global $_GET;
        global $_POST;
        global $_FILES;
        global $client;


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
                            
                // $params = [
                //     'index' => 'sibi',
                //     'type' => 'files',
                //     'id' => $uploadfile,
                //     'parent' => $_GET['_id'],
                //     'body' => $query
                // ];
                // $response_upload = $client->update($params); 
                
                
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

    public static function metadataGoogleScholar($record) {
        echo '<meta name="citation_title" content="'.$record["name"].'">';
        if (!empty($record['author'])) {
            foreach ($record['author'] as $autores) {
                echo '<meta name="citation_author" content="'.$autores["person"]["name"].'">';
            }
        } 
        echo '
        <meta name="citation_publication_date" content="'.$record['datePublished'].'">';
        if (!empty($record["isPartOf"])) {
            echo '<meta name="citation_journal_title" content="'.$record["isPartOf"]["name"].'">';
        }

        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",",$record["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    echo '
                    <meta name="citation_volume" content="'.trim(str_replace("v.","",$periodicos_array_new)).'">';
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    echo '
                    <meta name="citation_issue" content="'.trim(str_replace("n.","",$periodicos_array_new)).'">';
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $pages_array = explode("-",str_replace("p.","",$periodicos_array_new));
                    echo '<meta name="citation_firstpage" content="'.trim($pages_array[0]).'">';
                    if (!empty($pages_array[1])) {
                        echo '<meta name="citation_lastpage" content="'.trim($pages_array[1]).'">'; 
                    }                    
                }

            }
        } 

        $files_upload = glob('upload/'.$_GET['_id'].'/*.{pdf,pptx}', GLOB_BRACE);    
        $links_upload = "";
        if (!empty($files_upload)){       
            foreach($files_upload as $file) {        
                echo '<meta name="citation_pdf_url" content="http://'.$_SERVER['SERVER_NAME'].'/'.$file.'">
            ';
            }
        }
           

    }

    public static function jsonLD ($record) {

        foreach ($record['author'] as $autores) {
            $autor_json[] = '"'.$autores["person"]["name"].'"';
        }

        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",",$record["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $volume = trim(str_replace("v.","",$periodicos_array_new));
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $numero = trim(str_replace("n.","",$periodicos_array_new));
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $pages_array = explode("-",str_replace("p.","",$periodicos_array_new));
                    $first_page = trim($pages_array[0]);
                    if (!empty($pages_array[1])){
                        $end_page = trim($pages_array[1]);
                    } else {
                        $end_page = "N/D";
                    }

                }

            }
        }
        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            if (!empty($record["publisher"]["organization"]["name"])) { 
                $publisher = '"publisher": "'.$record["publisher"]["organization"]["name"].'",';
            } else {
                $publisher = "";
            }             
        }

        if (!empty($record['description'])) { 
            $description = '"description": "'.$record['description'][0].'",';         
        } else {
            $description = "";
        }       
        
        echo '<script type="application/ld+json">';
        echo '
            {
            "@context":"http://schema.org",
            "@graph": [
              {
                "@id": "http://bdpi.usp.br",
                "@type": "Library",
                "name": "Base de Produção Intelectual da USP"
              },
              ';
            
        
            switch ($record["type"]) {
                case "ARTIGO DE PERIODICO":
                if (empty($record["isPartOf"]["issn"][0])){
                    $record["isPartOf"]["issn"][0] = "";
                }
                if (empty($numero)){
                    $numero = "";
                }
                if (empty($volume)){
                    $volume = "";
                }                
                if (empty($end_page)){
                    $end_page = "";
                }
                if (empty($first_page)){
                    $first_page = "";
                }                                                 
                    
                    echo '

            {
                "@id": "#periodical", 
                "@type": [
                    "Periodical"
                ], 
                "name": "'.$record["isPartOf"]["name"].'", 
                "issn": [
                    "'.$record["isPartOf"]["issn"][0].'"
                ],  
                '.$publisher.'
            },
            {
                "@id": "#volume", 
                "@type": "PublicationVolume", 
                "volumeNumber": "'.$volume.'", 
                "isPartOf": "#periodical"
            },     
            {
                "@id": "#issue", 
                "@type": "PublicationIssue", 
                "issueNumber": "'.$numero.'", 
                "datePublished": "'.$record['datePublished'].'", 
                "isPartOf": "#volume"
            }, 
            {
                "@type": "ScholarlyArticle", 
                "isPartOf": "#issue",
                '.$description.'                 
                ';
                if (!empty($record['doi'])) {            
                    echo '"sameAs": "http://dx.doi.org/'.$record['doi'].'",';
                }
                echo '
                "about": [
                    "Works", 
                    "Catalog"
                ], 
                "pageEnd": "'.$end_page.'", 
                "pageStart": "'.$first_page.'", 
                "name": "'.$record["name"].'", 
                "author": ['.implode(",",$autor_json).']
            }
                            
                            ';                   
                            
                            break;
                        case "PARTE DE MONOGRAFIA/LIVRO":
                            
                            break;
                        case "TRABALHO DE EVENTO-RESUMO":
                            
                            break;
                        case "TEXTO NA WEB":
                            
                            break;
                        }
                
                    echo '

                    ]
                    }
            </script>';

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

        if ($field == "datePublished" ) {
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
                                   <input type="hidden" name="delete_file" value="'.$file.'">
                                   <button type="submit" uk-close></button>
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
                $string_up_array[] = '<a href="result.php?search[]=about.keyword:&quot;'.$string_up->{'string'}.'&quot;">'.$string_up->{'string'}.'</a>';    
            };
            echo 'Você também pode pesquisar pelos termos mais genéricos: ';
            print_r(implode(" -> ",$string_up_array));
            echo '<br/>';
            $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            if (!empty($termo_xml_down->{'result'}->{'term'})){
                foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                    $string_down_array[] = '<a href="result.php?search[]=about.keyword:&quot;'.$string_down->{'string'}.'&quot;">'.$string_down->{'string'}.'</a>';     
                };
                echo 'Ou pesquisar pelo assuntos mais específicos: ';
                print_r(implode(" - ",$string_down_array));            
            }


        } else {
            $termo_naocorrigido[] = $termo;
        }
    }      
    
}

/* Function to generate Tables */
function generateDataTable($consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {
    global $client;
    
    //if (!empty($sort)){
    //    $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';  
    //}
    
    $query["size"] = 0;
    $query = $consulta;    
    $query["aggregations"]["counts"]["terms"]["field"] = $campo.'.keyword';
    $query["aggregations"]["counts"]["terms"]["missing"] = "N/D";
    $query["aggregations"]["counts"]["terms"]["size"] = $tamanho;  
    


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
function generateCSV($consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho) {
    global $client;
	
    //if (!empty($sort)){
    //    $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';  
    //}

    $query["size"] = 0;
    $query = $consulta;    
    $query["aggregations"]["counts"]["terms"]["field"] = $campo.'.keyword';
    $query["aggregations"]["counts"]["terms"]["missing"] = "N/D";
    $query["aggregations"]["counts"]["terms"]["size"] = $tamanho; 
    
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

        if (!empty($cursor["_source"]["isPartOf"]["name"])) {
        $record[] = "T2  - ".$cursor["_source"]["isPartOf"]["name"]."";
        }

        if (!empty($cursor["_source"]['isPartOf']['issn'])) {
        $record[] = "SN  - ".$cursor["_source"]['isPartOf']['issn'][0]."";
        }

        if (!empty($cursor["_source"]["doi"])) {
        $record[] = "DO  - ".$cursor["_source"]["doi"]."";
        }

        if (!empty($cursor["_source"]["url"])) {
        $record[] = "UR  - ".$cursor["_source"]["url"][0]."";
        }

        if (!empty($cursor["_source"]["publisher"]["organization"]["location"])) {
        $record[] = "PP  - ".$cursor["_source"]["publisher"]["organization"]["location"]."";
        }

        if (!empty($cursor["_source"]["publisher"]["organization"]["name"])) {
            $record[] = "PB  - ".$cursor["_source"]["publisher"]["organization"]["name"]."";
        }

        if (!empty($cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",",$cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $record[] = "VL  - ".trim(str_replace("v.","",$periodicos_array_new))."";
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $record[] = "IS  - ".str_replace("n.","",trim(str_replace("n.","",$periodicos_array_new)))."";
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $record[] = "SP  - ".str_replace("p.","",trim(str_replace("p.","",$periodicos_array_new)))."";
                }

            }
        } 
    
        $record[] = "ER  - ";

        $record_blob = implode("\\n", $record);

        return $record_blob;

    }
}


?>
