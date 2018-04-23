<?php
/**
 * PHP version 7
 * File: Functions
 *
 * @category Functions
 * @package  Functions
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */

if (file_exists('functions_core/functions_core.php')) {
    include 'functions_core/functions_core.php';
} else {
    include '../functions_core/functions_core.php';
}

/**
 * Página Inicial
 *
 * @category Class
 * @package  Homepage
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic
 */
class Homepage
{
    
    static function unidadeUSP_inicio()
    {
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

        $response = elasticsearch::elastic_search($type, null, 0, $query);

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
    
    static function baseInicio()
    {
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
            echo '<li><a href="result.php?filter[]=base:&quot;'.$facets['key'].'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
        }   

    }    
    
    /** 
     * Function last records
     * 
     * @return array Last records
     */    
    static function ultimosRegistros()
    {
        global $type;
        $query = '{
                    "query": {
                        "match_all": {}
                     },
                    "sort" : [
                        {"_uid" : {"order" : "desc"}}
                        ]
                    }';
        $response = elasticsearch::elastic_search($type, null, 11, $query);

        foreach ($response["hits"]["hits"] as $r) {
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
            if (!empty($r["_source"]['name'])) {
                echo '<a href="item/'.$r['_id'].'"><h4 class="uk-comment-title uk-margin-remove">'.$r["_source"]['name'].'';
                if (!empty($r["_source"]['datePublished'])) {
                    echo ' ('.$r["_source"]['datePublished'].')';
                }         
                echo '</h4></a>';
            };
            echo '<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-small">';
            if (!empty($r["_source"]['author'])) { 
                foreach ($r["_source"]['author'] as $autores) {
                    echo '<li><a href="result.php?search[]=author.person.name:&quot;'.$autores["person"]["name"].'&quot;">'.$autores["person"]["name"].'</a></li>';
                }
                echo '</ul></div>';     
            };
            echo '</header>';
            echo '</article>';
        }

    }
    
    static function card_unidade($sigla,$nome_unidade)
    {
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

/**
 * Página Single
 *
 * @category Class
 * @package  PageSingle
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class PageSingle
{

    public static function counter($_id,$client)
    {

        global $index;

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
        
        $params = [];
        $params['index'] = $index;
        $params['type'] = 'metrics';
        $params['id'] = $_id;
        $params['body'] = $query;

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

    public static function metadataGoogleScholar($record)
    {
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
        if (!empty($files_upload)) {       
            foreach ($files_upload as $file) {        
                echo '<meta name="citation_pdf_url" content="http://'.$_SERVER['SERVER_NAME'].'/'.$file.'">
            ';
            }
        }
           

    }

    public static function jsonLD($record)
    {
        foreach ($record['author'] as $autores) {
            $autor_json[] = '"'.$autores["person"]["name"].'"';
        }

        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",", $record["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $volume = trim(str_replace("v.", "", $periodicos_array_new));
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $numero = trim(str_replace("n.", "", $periodicos_array_new));
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $pages_array = explode("-", str_replace("p.", "", $periodicos_array_new));
                    $first_page = trim($pages_array[0]);
                    if (!empty($pages_array[1])) {
                        $end_page = trim($pages_array[1]);
                    } else {
                        $end_page = "N/D";
                    }

                }

            }
        }
        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            if (!empty($record["publisher"]["organization"]["name"])) { 
                $publisher = '"publisher": "'.$record["publisher"]["organization"]["name"].'"';
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
                "name": "Base de Produção Intelectual da USP",
                "priceRange": "0",
                "address":"Rua da Praça do Relógio, 109 - Bloco L  Térreo - Cidade Universitária, São Paulo, SP",
                "image":"http://bdpi.usp.br/images/logo_sibi.jpg",
                "telephone":"011 2648-0948"
              },
              ';
            
        
        switch ($record["type"]) {
        case "ARTIGO DE PERIODICO":
            if (empty($record["isPartOf"]["issn"][0])) {
                $record["isPartOf"]["issn"][0] = "";
            }
            if (empty($numero)) {
                $numero = "";
            }
            if (empty($volume)) {
                $volume = "";
            }                
            if (empty($end_page)) {
                $end_page = "";
            }
            if (empty($first_page)) {
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
                "datePublished": "'.$record['datePublished'].'", 
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
                "image":"http://bdpi.usp.br/images/logo_sibi.jpg",
                "pageEnd": "'.$end_page.'", 
                "pageStart": "'.$first_page.'", 
                "headline": "'.$record["name"].'", 
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

/**
 * Processa resultados
 *
 * @category Class
 * @package  Results
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class Results
{
    
    /** 
     * Function to generate Graph Bar 
     * 
     * @param array  $query User query
     * @param string $field Field to aggregate
     */
    static function generateDataGraphBar($query, $field, $sort, $sort_orientation, $facet_display_name, $size) {
        global $index;
        global $type;
        global $client;
        
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"][$sort] = $sort_orientation;
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;
        
        $params = [
            'index' => $index,
            'type' => $type,
            'size'=> 0, 
            'body' => $query
        ]; 

        $facet = $client->search($params);  

        $data_array= array();
        foreach ($facet['aggregations']['counts']['buckets'] as $facets) {
            array_push($data_array, '{"name":"'.$facets['key'].'","value":'.$facets['doc_count'].'}');
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
    static function load_itens_aleph($sysno) 
    {
        $xml = simplexml_load_file('http://dedalus.usp.br/X?op=item-data&base=USP01&doc_number='.$sysno.'');
        if ($xml->error == "No associated items") {

        } else {
            echo '<div id="exemplares'.$sysno.'">';
            echo "<table class=\"uk-table uk-table-small uk-text-small uk-table-striped\">";
            echo "<caption>Exemplares físicos disponíveis nas Bibliotecas da USP</caption>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><small>Biblioteca</small></th>";
            echo "<th><small>Cód. de barras</small></th>";
            echo "<th><small>Status</small></th>";
            echo "<th><small>Núm. de chamada</small></th>";
            if ($xml->item->{'loan-status'} == "A") {
                echo "<th><small>Status</small></th>
                <th><small>Data provável de devolução</small></th>";
            } else {
                echo "<th><small>Disponibilidade</small></th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($xml->item as $item) {
                $bib_fisica = explode("-", $item->{'sub-library'});
                echo '<tr>';
                echo '<td><small><a target="_blank" href="http://www.sibi.usp.br/bibliotecas/fisicas/?char='. (string)$bib_fisica[0] .'">'.$item->{'sub-library'}.'</a></small></td>';
                echo '<td><small>'.$item->{'barcode'}.'</small></td>';
                echo '<td><small>'.$item->{'item-status'}.'</small></td>';
                echo '<td><small>'.$item->{'call-no-1'}.'</small></td>';
                if ($item->{'loan-status'} == "A") {
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
    


    static function get_fulltext_file($id,$session) 
    {
        global $url_base;
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
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$url_base.'/pdf.php?_id='.$id.'&file='.$file.'" target="_blank"><img src="'.$url_base.'/inc/images/pdf.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
                } else {
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$url_base.'/pdf.php?_id='.$id.'&file='.$file.'" target="_blank"><img src="'.$url_base.'/inc/images/pptx.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
                }
            }
        }
        return $links_upload;
    }
    
    
}

/**
 * Classe USP
 *
 * @category Class
 * @package  USP
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class USP
{

    /* Consulta o Vocabulário Controlado da USP */
    static function consultar_vcusp($termo) {
        echo '<h4>Vocabulário Controlado do SIBiUSP</h4>';
        $xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');

        if ($xml->{'resume'}->{'cant_result'} != 0) {

            $termo_xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchUp&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            foreach (($termo_xml->{'result'}->{'term'}) as $string_up) {
                $string_up_array[] = '<a href="result.php?search[]=about:&quot;'.$string_up->{'string'}.'&quot;">'.$string_up->{'string'}.'</a>';    
            };
            echo 'Você também pode pesquisar pelos termos mais genéricos: ';
            print_r(implode(" -> ",$string_up_array));
            echo '<br/>';
            $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            if (!empty($termo_xml_down->{'result'}->{'term'})){
                foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                    $string_down_array[] = '<a href="result.php?search[]=about:&quot;'.$string_down->{'string'}.'&quot;">'.$string_down->{'string'}.'</a>';     
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
function generateDataTable($consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho)
{
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
function generateCSV($consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho)
{
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
        array_push($data_array, ''.$facets["key"].'\\t'.$facets["doc_count"].'');
    };
    $comma_separated = implode("\\n", $data_array);
    return $comma_separated;

}

/**
 * Autoridades
 *
 * @category Class
 * @package  Autoridades
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class Autoridades
{
    
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

/**
 * APIs
 *
 * @category Class
 * @package  APIs
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class API
{
    

    static function get_title_elsevier($issn,$api_elsevier)
    {
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
        $data = json_decode($resp, true);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);    
    }

    static function get_citations_elsevier($doi,$api_elsevier)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.elsevier.com/content/abstract/citation-count?doi='.$doi.'&apiKey='.$api_elsevier.'&httpAccept=application%2Fjson',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $data = json_decode($resp, true);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);    
    } 

    static function metrics_update($_id,$metrics_array)
    {    
        global $client;    
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

    static function store_issn_info($client,$issn,$issn_info)
    {    

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

    /*
    * Obtem dados do Google Scholar  *
    */
    static function google_scholar_py($record)
    {

        if (!empty($record["doi"])) {
            $command_string = 'google_scholar_py/scholar.py -c 1 --all "'.$record["doi"].'" --cookie-file=cookies.txt --csv';
            $command = escapeshellcmd($command_string);
            $output = shell_exec($command);
        } else {
            $array_name = explode(',', $record["author"][0]["person"]["name"]);
            $command_string = 'google_scholar_py/scholar.py -c 1 -p "'.htmlentities($record["name"]).'" -a "'.htmlentities($array_name[0]).'" --cookie-file=cookies.txt --csv';	
            $command = escapeshellcmd($command_string);
            $output = shell_exec($command);
        }
              
        if (!empty($output)) {
            $output_array = explode("|", $output);
            $g_output = [];
            $g_output["title"] = $output_array[0];
            $g_output["url"] = $output_array[1];
            $g_output["year"] = (int)$output_array[2];
            $g_output["num_citations"] = (int)$output_array[3];
            $g_output["num_versions"] = (int)$output_array[4];
            $g_output["cluster_id"] = $output_array[5];
            $g_output["url_pdf"] = $output_array[6];
            $g_output["url_citations"] = $output_array[7];
            $g_output["url_versions"] = $output_array[8];
            $g_output["url_citation"] = $output_array[9];
            $g_output["excerpt"] = $output_array[10];
            return $g_output;
            unset($g_output);
        }
    }
    
    static function get_microsoft_academic($title)
    {
        global $api_microsoft;
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => array('Ocp-Apim-Subscription-Key:'.$api_microsoft.''), 
            //CURLOPT_URL => 'https://westus.api.cognitive.microsoft.com/academic/v1.0/evaluate?expr=Composite(AA%2EAuN%3D%3D%27tiago%20murakami%27)&model=latest&count=10&offset=0&attributes=Id,Y,Ti,CC,AA.AuN,AA.AuId,AA.AfN,AA.AfId,L,F.FN,F.FId,J.JN,J.JId,C.CN,C.CId,RId,W,D,ECC,E',
            CURLOPT_URL => 'https://westus.api.cognitive.microsoft.com/academic/v1.0/evaluate?expr=Ti=\''.$title.'\'&model=latest&count=1&offset=0&attributes=Id,Y,Ti,CC,AA.AuN,AA.AuId,AA.AfN,AA.AfId,L,F.FN,F.FId,J.JN,J.JId,C.CN,C.CId,RId,W,D,ECC,E',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $data = json_decode($resp, true);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);    
    } 
    
    static function get_opencitation_title($title)
    {
        $sparql = new EasyRdf_Sparql_Client('http://opencitations.net/sparql');
        $result = $sparql->query(
            'PREFIX cito: <http://purl.org/spar/cito/>
            PREFIX dcterms: <http://purl.org/dc/terms/>
            PREFIX datacite: <http://purl.org/spar/datacite/>
            PREFIX literal: <http://www.essepuntato.it/2010/06/literalreification/>
            SELECT ?citing ?title WHERE {
              ?br 
                dcterms:title "'.$title.'" ;
                ^cito:cites ?citing .
              ?citing dcterms:title ?title
            }'
        );        
        return $result;
    }
    
    static function dimensionsAPI($doi)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://metrics-api.dimensions.ai/doi/'.$doi.'',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A'
        )
        );
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $data = json_decode($resp, true);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);    
    }     

    
}

/**
 * Exporters
 *
 * @category Class
 * @package  Exporters
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class Exporters
{

    static function RIS($cursor) 
    {

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
            $periodicos_array = explode(",", $cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $record[] = "VL  - ".trim(str_replace("v.", "", $periodicos_array_new))."";
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $record[] = "IS  - ".str_replace("n.", "", trim(str_replace("n.","",$periodicos_array_new)))."";
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $record[] = "SP  - ".str_replace("p.", "", trim(str_replace("p.","",$periodicos_array_new)))."";
                }

            }
        } 
    
        $record[] = "ER  - ";
        $record[] = "";
        $record[] = "";

        $record_blob = implode("\\n", $record);

        return $record_blob;

    }
}

/**
 * Record
 *
 * @category Class
 * @package  Record
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic 
 */
class Record
{

    public function __construct($record, $showMetrics)
    {   
        $this->id = $record["_id"];
        $this->name = $record["_source"]["name"];
        $this->base = $record["_source"]["base"][0];
        $this->type = ucfirst(strtolower($record["_source"]["type"]));
        $this->datePublished = $record["_source"]["datePublished"];
        if (isset($record["_source"]["dateCreated"])) {
            $this->dateCreated = $record["_source"]["dateCreated"];
        }        
        $this->languageArray = $record["_source"]["language"];
        $this->countryArray = $record["_source"]["country"];
        $this->authorArray = $record["_source"]["author"];
        if (isset($record["_source"]["description"])) {
            $this->descriptionArray = $record["_source"]["description"];
        }
        if (isset($record["_source"]["numberOfPages"])) {
            $this->numberOfPages = $record["_source"]["numberOfPages"];
        }
        if (isset($record["_source"]["publisher"])) {
            $this->publisherArray = $record["_source"]["publisher"];
        }
        if (isset($record["_source"]["isPartOf"])) {
            $this->isPartOfArray = $record["_source"]["isPartOf"];
        }
        if (isset($record["_source"]["about"])) {
            $this->aboutArray = $record["_source"]["about"];
        }
        if (isset($record["_source"]["USP"]["about_BDTD"])) {
            $this->aboutBDTDArray = $record["_source"]["USP"]["about_BDTD"];
        } else {
            $this->aboutBDTDArray = 0;
        } 
        if (isset($record["_source"]['funder'])) {
            $this->funderArray = $record["_source"]['funder'];
        } else {
            $this->funderArray = 0;
        }
        $this->USPArray = $record["_source"]["USP"];
        $this->authorUSPArray = $record["_source"]["authorUSP"];
        $this->unidadeUSPArray = $record["_source"]["unidadeUSP"];
        if (isset($record["_source"]["isbn"])) {
            $this->isbn = $record["_source"]["isbn"];
        }
        if (isset($record["_source"]["url"])) {       
            $this->url = $record["_source"]["url"];
        }
        if (isset($record["_source"]["doi"])) {
            $this->doi = $record["_source"]["doi"];
        } 
        if (isset($record["_source"]["issn"])) {
            $this->issnArray = $record["_source"]["issn"];
        } else {
            $this->issnArray[] = "Não informado";
        } 
        $this->completeRecord = $record;
        $this->showMetrics = $showMetrics;
    }

    public function simpleRecordMetadata($t)
    {
        echo '<li>';
        echo '<div class="uk-grid-divider uk-padding-small" uk-grid>';
        echo '<div class="uk-width-1-5@m">';
            echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['SCRIPT_NAME'].''.$_SERVER['QUERY_STRING'].'&filter[]=type:&quot;'.$this->type.'&quot;">'.$this->type.'</a></p>';
            echo '<p>Unidades USP: ';
            if (!empty($this->unidadeUSPArray)) {
                $unique =  array_unique($this->unidadeUSPArray);
                foreach ($unique as $unidadeUSP) {
                    echo '<a href="result.php?filter[]=unidadeUSP:&quot;'.$unidadeUSP.'&quot;">'.$unidadeUSP.' </a>';
                }
            }
            echo '</p>';
            if (!empty($this->isbn)) {
                $cover_link = 'http://covers.openlibrary.org/b/isbn/'.$this->isbn.'-M.jpg';
                echo  '<p><img src="'.$cover_link.'"></p>';
            } 
        echo '</div>';
        echo '<div class="uk-width-4-5@m">';
            echo '<article class="uk-article">';
            echo '<p class="uk-text-lead uk-margin-remove" style="font-size:115%"><a class="uk-link-reset" href="item/'.$this->id.'">'.$this->name.' ('.$this->datePublished.')</a></p>';
            
            /* Authors */
            echo '<p class="uk-article-meta uk-margin-remove">'.$t->gettext('Autores').': '; 
            foreach ($this->authorArray as $authors) {
                if (!empty($authors["person"]["potentialAction"])) {
                    $authors_array[]='<a href="result.php?search[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' ('.$authors["person"]["potentialAction"].')</a>';
                } else {
                    $authors_array[]='<a href="result.php?search[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a>';
                }
            } 
            $array_aut = implode("; ", $authors_array);
            print_r($array_aut);
            echo '</p>';

            /* IsPartOf */    
            if (!empty($this->isPartOfArray["name"])) {
                echo '<p class="uk-text-small uk-margin-remove">In: <a href="result.php?filter[]=isPartOf.name:&quot;'.$this->isPartOfArray["name"].'&quot;">'.$this->isPartOfArray["name"].'</a></p>';
            } 
            
            /* Subjects */
            echo '<p class="uk-text-small uk-margin-remove">'.$t->gettext('Assuntos').': ';
            foreach ($this->aboutArray as $subject) {
                echo '<a href="result.php?filter[]=about:&quot;'.$subject.'&quot;">'.$subject.'</a> ';
            }
        
        if (!empty($this->url)||!empty($this->doi)) {
            $this->onlineAccess($t);
        }
        if ($this->showMetrics == true) {
            if (!empty($this->doi)) {
                $this->metrics($t, $this->doi, $this->completeRecord);
            }
        }             
                        
        $this->citation($t, $this->completeRecord);


            
        echo '</article>';
                    
        echo '</div>';
        echo '</div>';

        echo '</li>';
        flush();
        ob_flush();

    }

    public function completeRecordMetadata($t,$url_base)
    {
        echo '<article class="uk-article">';
        echo '<p class="uk-article-meta">';    
        echo '<a href="<?php echo $url_base ?>/result.php?search[]=type:&quot;'.$this->type.'&quot;">'.$this->type.'</a>';
        echo '</p>';
        echo '<h1 class="uk-article-title uk-margin-remove-top" style="font-size:150%"><a class="uk-link-reset" href="">'.$this->name.' ('.$this->datePublished.')</a></h1>';
        echo '<div class="uk-flex" uk-grid>';
        /* Authors */
        echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Autores').':';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        foreach ($this->authorArray as $authors) {
            if (!empty($authors["person"]["affiliation"]["name"])) {
                echo '<li><a href="'.$url_base.'/result.php?search[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' - '.$authors["person"]["affiliation"]["name"].'</a></li>';
            } elseif (!empty($authors["person"]["potentialAction"])) {
                echo '<li><a href="'.$url_base.'/result.php?search[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' - '.$authors["person"]["potentialAction"].'</a></li>';
            } else {
                echo '<li><a href="'.$url_base.'/result.php?search[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a></li>';
            }                                
        }
        echo '</ul></p></div>';         
        /* USP Authors */
        echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Autores USP').':';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        foreach ($this->authorUSPArray as $autoresUSP) {
        echo '<li><a href="'.$url_base.'/result.php?search[]=authorUSP.name:&quot;'.$autoresUSP["name"].'&quot;">'.$autoresUSP["name"].' - '.$autoresUSP["unidadeUSP"].' </a></li>';
        }
        echo '</ul></p></div>';
        /* USP Units */
        echo '<div> <p class="uk-text-small uk-margin-remove">'.$t->gettext('Unidades USP').':';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        foreach ($this->unidadeUSPArray as $unidadeUSP) {
            echo '<li><a href="'.$url_base.'/result.php?search[]=unidadeUSP:&quot;'.$unidadeUSP.'&quot;">'.$unidadeUSP.'</a></li>';
        }
        echo '</ul></p></div>';        
        /* Subject */
        echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Assuntos').':';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        foreach ($this->aboutArray as $subject) {
            echo '<li><a href="'.$url_base.'/result.php?search[]=about:&quot;'.$subject.'&quot;">'.$subject.'</a></li>';
        }
        echo '</ul></p></div>';
        
        /* BDTD Subject */
        if ($this->aboutBDTDArray > 0){
            echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Assuntos provenientes das teses').':';
            echo '<ul class="uk-list uk-list-striped uk-text-small">';
            foreach ($this->aboutBDTDArray as $subject_BDTD) {
                echo '<li><a href="'.$url_base.'/result.php?search[]=USP.about_BDTD:&quot;'.$subject_BDTD.'&quot;">'.$subject_BDTD.'</a></li>';
            }
            echo '</ul></p></div>';
        }

        /* Funder */
        if ($this->funderArray > 0){
            echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Agências de fomento').': ';
            echo '<ul class="uk-list uk-list-striped uk-text-small">';
            foreach ($this->funderArray as $funder) {                
                echo '<li><a href="'.$url_base.'/result.php?search[]=funder:&quot;'.$funder.'&quot;">'.$funder.'</a></li>';
            }
            echo '</ul></p></div>';
        }

        /* Language */
        echo '<div><p class="uk-text-small uk-margin-remove">'.$t->gettext('Idioma').': ';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        foreach ($this->languageArray as $language) {
            echo '<li><a href="'.$url_base.'/result.php?search[]=language:&quot;'.$language.'&quot;">'.$language.'</a></li>';
        }
        echo '</ul></p></div>';

        echo '</div><hr>';

        /* Abstract */
        if (!empty($this->descriptionArray)) {
            echo '<p class="uk-text-small uk-margin-remove">'.$t->gettext('Resumo').': ';
                foreach ($this->descriptionArray as $resumo) {
                    echo $resumo;
                }
            echo '</p>';
        }

        echo '<hr><div class="uk-column-1-2">';

        /* Imprint */
        if (!empty($this->publisherArray)) {
            echo '<p class="uk-text-small uk-margin-remove">'.$t->gettext('Imprenta').':';
            echo '<ul class="uk-list uk-list-striped uk-article-meta">';
                if (!empty($this->publisherArray["organization"]["name"])) {
                    echo '<li>'.$t->gettext('Editora').': <a href="'.$url_base.'/result.php?search[]=publisher.organization.name:&quot;'.$this->publisherArray["organization"]["name"].'&quot;">'.$this->publisherArray["organization"]["name"].'</a></li>';
                }
                if (!empty($this->publisherArray["organization"]["location"])) {
                    echo '<li>'.$t->gettext('Local').': <a href="'.$url_base.'/result.php?search[]=publisher.organization.location:&quot;'.$this->publisherArray["organization"]["location"].'&quot;">'.$this->publisherArray["organization"]["location"].'</a></li>';
                }
                if (!empty($this->datePublished)) {
                    echo '<li>'.$t->gettext('Data de publicação').': <a href="'.$url_base.'/result.php?search[]=datePublished:&quot;'.$this->datePublished.'&quot;">'.$this->datePublished.'</a></li>';
                }
            echo '</ul></p>';            
        }
        
        /* Data da defesa */
        if (!empty($this->dateCreated)) {
            echo '<p class="uk-text-small uk-margin-remove">Data da defesa: '.$this->dateCreated.'</a></p>';
        }
        
        /* Phisical description */
        if (!empty($this->numberOfPages)) {
            echo '<p class="uk-text-small uk-margin-remove">Descrição física: '.$this->numberOfPages.'</a></p>';
        }              

        /* ISBN */
        if (!empty($this->isbn)) {
            echo '<p class="uk-text-small uk-margin-remove">ISBN: '.$this->isbn.'</a></p>';
        }

        /* DOI */
        if (!empty($this->doi)) {
            echo '<p class="uk-text-small uk-margin-remove">DOI: <a href="https://dx.doi.org/'.$this->doi.'">'.$this->doi.'</a></p>';
        }

        /* Source */
        if (!empty($this->isPartOfArray)) {
            echo '<p class="uk-text-small uk-margin-remove">';
                echo '<p class="uk-text-small uk-margin-remove">'.$t->gettext('Fonte').':<ul class="uk-list uk-list-striped uk-article-meta">';
                if (!empty($this->isPartOfArray["name"])) {
                        echo '<li>Título do periódico: <a href="'.$url_base.'/result.php?search[]=isPartOf.name:&quot;'.$this->isPartOfArray["name"].'&quot;">'.$this->isPartOfArray["name"].'</a></li>';
                }    
                if (!empty($this->isPartOfArray['issn'][0])) {
                    echo '<li>ISSN: <a href="'.$url_base.'/result.php?search[]=issn:&quot;'.$this->isPartOfArray['issn'][0].'&quot;">'.$this->isPartOfArray['issn'][0].'</a></li>';
                }                                    
                if (!empty($this->isPartOfArray["USP"]["dados_do_periodico"])) {
                    echo '<li>Volume/Número/Paginação/Ano: '.$this->isPartOfArray["USP"]["dados_do_periodico"].'</li>';
                }
                echo '</ul></p>';
        }        
        
        echo '</div>';

        if (!empty($this->url)||!empty($this->doi)) {
            $this->onlineAccess($t);
        }        
     
        flush();
        ob_flush();
    }

    public function onlineAccess($t)
    {
        
        echo '<div class="uk-alert-primary" uk-alert>';
            echo '<p class="uk-text-small">'.$t->gettext('Acesso online ao documento').'</p>';
            if (!empty($this->url)) {
                foreach ($this->url as $url) {
                    echo '<a class="uk-button uk-button-primary uk-button-small" href="'.$url.'" target="_blank">'.$t->gettext('Acesso online à fonte').'</a>';
                }
            }
            if (!empty($this->doi)) {
                echo '<a class="uk-button uk-button-primary uk-button-small" href="http://dx.doi.org/'.$this->doi.'" target="_blank">DOI</a>';
            }

            $sfx_array[] = 'rft.atitle='.$this->name.'';
            $sfx_array[] = 'rft.year='.$this->datePublished.'';
            if (!empty($this->isPartOfArray["name"])) {
                $sfx_array[] = 'rft.jtitle='.$this->isPartOfArray["name"].'';
            }
            if (!empty($this->doi)) {
                $sfx_array[] = 'rft_id=info:doi/'.$this->doi.'';
            }
            if (!empty($this->issnArray[0])) {
                $sfx_array[] = 'rft.issn='.$this->issnArray[0].'';
            }
            if (!empty($r["_source"]['ispartof_data'][0])) {
                $sfx_array[] = 'rft.volume='.trim(str_replace("v.","",$r["_source"]['ispartof_data'][0])).'';
            }                                             
            echo ' <a class="uk-text-small" href="http://143.107.154.66:3410/sfxlcl41?'.implode("&", $sfx_array).'" target="_blank">'.$t->gettext('ou pesquise este registro no').'<img src="http://143.107.154.66:3410/sfxlcl41/sfx.gif"></a>'; 
        echo '</div>';

    }      
    
    public function holdings($id)
    {
        if ($dedalus == true) {
            Results::load_itens_aleph($id);
        } 
    }    

    public function metrics($t, $doi, $completeRecord)
    {

        if ($doi != "Não informado") {
            echo '<div class="uk-alert-warning" uk-alert>';
                echo '<p>'.$t->gettext('Métricas').'</p>';
                echo '<div uk-grid>';
                    echo '<div data-badge-popover="right" data-badge-type="1" data-doi="'.$doi.'" data-hide-no-mentions="true" class="altmetric-embed"></div>';
                    echo '<div><a href="https://plu.mx/plum/a/?doi='.$doi.'" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true"></a></div>';
                    if ($doi != "Não informado") {
                        echo '<div><object data="http://api.elsevier.com/content/abstract/citation-count?doi='.$doi.'&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=image/jpeg"></object></div>';
                    }
                    echo '<div><span class="__dimensions_badge_embed__" data-doi="'.$doi.'" data-hide-zero-citations="true" data-style="small_rectangle"></span></div>';
                    if (!empty($completeRecord["_source"]["USP"]["opencitation"]["num_citations"])) {
                        echo '<div>Citações no OpenCitations: '.$completeRecord["_source"]["USP"]["opencitation"]["num_citations"].'</div>';
                    }
                    if (isset($completeRecord["_source"]["USP"]["aminer"]["num_citation"])) {
                        echo '<div>Citações no AMiner: '.$completeRecord["_source"]["USP"]["aminer"]["num_citation"].'</div>';
                    }
                echo '</div>';
            echo '</div>';
        } else {
            if (isset($r["_source"]["USP"]["aminer"]["num_citation"])) {
                if ($r["_source"]["USP"]["aminer"]["num_citation"] > 0) {
                    echo '<div class="uk-alert-warning" uk-alert>';
                        echo '<p>'.$t->gettext('Métricas').':</p>';
                        echo '<div uk-grid>'; 
                        echo '<div>Citações no AMiner: <?php echo $r["_source"]["USP"]["aminer"]["num_citation"]; ?></div>';
                        echo '</div>';
                    echo '</div>';
                }
            }
        }
    }

    public function citation($t, $record)
    {
        /* Citeproc-PHP*/
        require_once 'inc/citeproc-php/CiteProc.php';
        $csl_abnt = file_get_contents('inc/citeproc-php/style/abnt.csl');
        $csl_apa = file_get_contents('inc/citeproc-php/style/apa.csl');
        $csl_nlm = file_get_contents('inc/citeproc-php/style/nlm.csl');
        $csl_vancouver = file_get_contents('inc/citeproc-php/style/vancouver.csl');
        $lang = "br";
        $citeproc_abnt = new citeproc($csl_abnt,$lang);
        $citeproc_apa = new citeproc($csl_apa,$lang);
        $citeproc_nlm = new citeproc($csl_nlm,$lang);
        $citeproc_vancouver = new citeproc($csl_nlm,$lang);
        $mode = "reference";        
        echo '<div class="uk-grid-small uk-child-width-auto" uk-grid>';
            echo '<div><a class="uk-button uk-button-text" href="item/'.$record['_id'].'">'.$t->gettext('Ver registro completo').'</a></div>';
            echo '<div><a class="uk-button uk-button-text" href="#" uk-toggle="target: #citacao'.$record['_id'].'; animation: uk-animation-fade">'.$t->gettext('Como citar').'</a> </div>';
        echo '</div>';        
        echo '<div id="citacao'.$record['_id'].'" hidden="hidden">';
            echo '<li class="uk-h6 uk-margin-top">';
                echo '<div class="uk-alert-danger" uk-alert>A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>';
                echo '<ul>';
                    echo '<li class="uk-margin-top">';
                    echo '<p><strong>ABNT</strong></p>';
                    $data = citation::citation_query($record["_source"]);
                    print_r($citeproc_abnt->render($data, $mode));
                    echo '</li>';
                    echo '<li class="uk-margin-top">';
                    echo '<p><strong>APA</strong></p>';
                    $data = citation::citation_query($record["_source"]);
                    print_r($citeproc_apa->render($data, $mode));
                    echo '</li>';
                    echo '<li class="uk-margin-top">';
                    echo '<p><strong>NLM</strong></p>';
                    $data = citation::citation_query($record["_source"]);
                    print_r($citeproc_nlm->render($data, $mode));
                    echo '</li>';
                    echo '<li class="uk-margin-top">';
                    echo '<p><strong>Vancouver</strong></p>';
                    $data = citation::citation_query($record["_source"]);
                    print_r($citeproc_vancouver->render($data, $mode));
                    echo '</li>';                                                
                echo '</ul>';                                              
            echo '</li>';
        echo '</div>';
    }
}

class DSpaceREST {
    static function loginREST()
    {
    
        global $dspaceRest;
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL,"$dspaceRest/rest/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    http_build_query(array('email' => 'dgti@dt.sibi.usp.br','password' => '123456'))
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec($ch);
        $output_parsed = explode(" ",$server_output);
        
        return $output_parsed[3];
        
        curl_close ($ch);  
    
    } 
    
    static function logoutREST($cookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $cookies"));
        curl_setopt($ch, CURLOPT_URL,"$dspaceRest/rest/logout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        //print_r($server_output);  
        curl_close ($ch);
    } 
    
    static function searchItem($cookies,$sysno)
    {
        global $dspaceRest;
        $data_string = "{\"key\":\"usp.sysno\", \"value\":\"$sysno\"}";  
        $ch = curl_init();          
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/find-by-metadata-field");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: $cookies",                                                                          
            'Content-Type: application/json'      
            )                                                                       
        );  
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return $result[0]["uuid"];
        curl_close($ch);
    }
    
    static function getBitstreamDSpace($cookies,$itemID)
    {
        global $dspaceRest;
        $ch = curl_init();          
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$itemID/bitstreams");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: $cookies",                                                                          
            'Content-Type: application/json'      
            )                                                                       
        );  
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return $result;
        curl_close($ch);
    } 
}



?>
