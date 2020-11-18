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
    include_once 'functions_core/functions_core.php';
} elseif (file_exists('../functions_core/functions_core.php')) {
    include_once '../functions_core/functions_core.php';
} else {
    include_once '../../functions_core/functions_core.php';
}

function checkDSpaceAPI($url_api){
    $curl = curl_init($url_api);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if($http_status == 200)
	   return true;
    error_log("A API de sincronização não está respondendo");
    return false;
}

function getAlertMessage($message, $type){
    return '<div class="uk-alert-'. $type .'" uk-alert>
              <a class="uk-alert-close" uk-close></a>
              <p>No momento, '. $message . '. Tente mais tarde ou contate o administrador do sistema.</p>
            </div>';
}

function input_sanitize($input, $email=false){
    $input = trim($input);
    $input = stripslashes($input);
    if($email){
        $input = filter_var($input, FILTER_SANITIZE_EMAIL);
    } else {
        $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_SANITIZE_SPECIAL_CHARS);    
    }
    return $input;
}

function search_sanitize($search){
	if(is_array($search)){
		foreach($search as $key => $value){
			$data[$key] = str_replace(":", "", $value);
		}
	} else {
		$data = str_replace(":", "", $value);
	}
	$data = preg_replace('/\s+/', ' ',$data);
	$data = preg_replace('/s+\/s+/', ' ', $data);
	$data = preg_replace('/\//', ' ', $data);
	return $data;
}

function __htmlspecialchars($data) {
    if (is_array($data)) {
        foreach ( $data as $key => $value ) {
            $data[$key] = __htmlspecialchars($value);
        }
    } else if (is_object($data)) {
        $values = get_class_vars(get_class($data));
        foreach ( $values as $key => $value ) {
            $data->{$key} = __htmlspecialchars($value);
        }
    } else {
        $data = htmlspecialchars($data);
    }
    return $data;
}


function get_staffUsers(){
	$staffUsers = array();
	$arquivo = fopen($_SERVER["DOCUMENT_ROOT"].'/inc/staff.txt','r');
	if ($arquivo == false) die('O arquivo não existe.');
	while(true) {
		$linha = fgets($arquivo);
		if ($linha==null) break;
		$usertemp = explode(";", $linha);
		$staffUsers[] = $usertemp[0];
	}
	fclose($arquivo);
	return $staffUsers;
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
              $programas[] =  '<li><a href="result.php?filter[]=unidadeUSPtrabalhos:&quot;'.strtoupper($facets['key']).'&quot;">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
            } else {
                echo '<li><a href="result.php?filter[]=unidadeUSPtrabalhos:&quot;'.strtoupper($facets['key']).'&quot;">'.strtoupper($facets['key']).' ('.number_format($facets['doc_count'],0,',','.').')</a></li>';
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

    static function totalProducao($t){
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
        $total = round($response["hits"]["total"]);
        /*if ($total >= 0 && $total < 1000000) {
            return $t->gettext("Total da produção") . ": &cong; " . round($total/1000) . " " . $t->gettext("mil");
        } else if ($total >= 1000000 && $total <= 1949999){
            return $t->gettext("Total da produção") . ": &cong; " . number_format($total/1000000,1,',','.') . " " . $t->gettext("milhão");
        } else {
            return $t->gettext("Total da produção") . ": &cong; " . number_format($total/1000000,1,',','.') . " " . $t->gettext("milhões");
        }*/
        
        if(isset($_GET["locale"]) && $_GET["locale"] == "en_US"){
            $total = number_format($total, 0, '.', ',');
        } else {
            $total = number_format($total, 0, ',', '.');
        }
        return $t->gettext("Total da produção") . ": " . $total;
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
        $response = elasticsearch::elastic_search($type, null, 10, $query);

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
		    if (!empty($autores["person"]["orcid"])) {
                        $orcidLink = ' <a href="'.$autores["person"]["orcid"].'"><img src="https://orcid.org/sites/default/files/images/orcid_16x16.png"></a>';
                    } else {
                        $orcidLink = '';
                    }
                    echo '<li><a href="result.php?filter[]=author.person.name:&quot;'.$autores["person"]["name"].'&quot;">'.$autores["person"]["name"].'</a>'.$orcidLink.'</li>';
                    unset($orcidLink);
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
            <a href="result.php?filter[]=unidadeUSPtrabalhos:'.$sigla.'">
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

        if (!empty($_POST["embargo_date"])) {
            $embargo_date = '{"embargo_date":"'.$_POST["embargo_date"].'"},';
        } else {
            $embargo_date = '{"embargo_date":""},';
        }

        if ($_FILES['upload_file']['type'] == 'application/pdf') {
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
        echo "\n";
        if (!empty($record['author'] && is_array($record['author']))) {
            foreach ($record['author'] as $autores) {
                echo '    <meta name="citation_author" content="'.$autores["person"]["name"].'">';
                echo "\n";
            }
	} elseif(!empty($autores["person"]["name"])){
	    echo '    <meta name="citation_author" content="'.$autores["person"]["name"].'">';
	    echo "\n";
	}
	if(isset($record['datePublished'])){
            echo '    <meta name="citation_publication_date" content="'.$record['datePublished'].'">';
            echo "\n";
	}
        if (!empty($record["isPartOf"]["name"])) {
            echo '    <meta name="citation_journal_title" content="'.$record["isPartOf"]["name"].'">';
            echo "\n";
        }

        if (!empty($record["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",", $record["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    echo '    <meta name="citation_volume" content="'.trim(str_replace("v.", "", $periodicos_array_new)).'">';
                    echo "\n";
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    echo '    <meta name="citation_issue" content="'.trim(str_replace("n.", "", $periodicos_array_new)).'">';
                    echo "\n";
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $pages_array = explode("-", str_replace("p.", "", $periodicos_array_new));
                    echo '    <meta name="citation_firstpage" content="'.trim($pages_array[0]).'">';
                    echo "\n";
                    if (!empty($pages_array[1])) {
                        echo '    <meta name="citation_lastpage" content="'.trim($pages_array[1]).'">';
                        echo "\n";
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

        $publisher = $record["publisher"]["organization"]["name"] ?? "";
        $isPartofName = $record["isPartOf"]["name"] ?? "";
        $description = $record['description'][0] ?? "";
        $volume = $volume ?? "";
        $numero = $numero ?? "";
        $first_page = $first_page ?? "";
        $end_page = $end_page ?? "";

        $jsonSource['@context'] = 'http://schema.org';
        $jsonSource['@graph'][0]['@id'] = '#periodical';
        $jsonSource['@graph'][0]['@type'] = 'Periodical';
	$jsonSource['@graph'][0]['name'] = $isPartofName;
	if(isset($record["isPartOf"]["issn"][0])){
            $jsonSource['@graph'][0]['issn'][0] = $record["isPartOf"]["issn"][0];
	}
        $jsonSource['@graph'][0]['publisher'] = $publisher;
        $jsonSource['@graph'][1]['@id'] = '#volume';
        $jsonSource['@graph'][1]['@type'] = 'PublicationVolume';
        $jsonSource['@graph'][1]['volumeNumber'] = $volume;
        $jsonSource['@graph'][1]['isPartOf'] = '#periodical';
        $jsonSource['@graph'][2]['@id'] = '#issue';
        $jsonSource['@graph'][2]['@type'] = 'PublicationIssue';
        $jsonSource['@graph'][2]['issueNumber'] = $numero;
	$jsonSource['@graph'][2]['isPartOf'] = '#volume';
	if(isset($record['datePublished'])){
            $jsonSource['@graph'][2]['datePublished'] = $record['datePublished'];
	    $jsonSource['@graph'][3]['datePublished'] = $record['datePublished'];
	}
        $jsonSource['@graph'][3]['@type'] = 'ScholarlyArticle';
        $jsonSource['@graph'][3]['isPartOf'] = '#issue';
	$jsonSource['@graph'][3]['description'] = $description;
	if(isset($record['doi'])){
            $jsonSource['@graph'][3]['sameAs'] = 'https://doi.org/'.$record['doi'].'';
	}
        $jsonSource['@graph'][3]['about'][] = 'Works';
        $jsonSource['@graph'][3]['about'][] = 'Catalog';
        $jsonSource['@graph'][3]['image'] = '//bdpi.usp.br/images/logo_sibi.jpg';
        $jsonSource['@graph'][3]['pageEnd'] = $end_page;
        $jsonSource['@graph'][3]['pageStart'] = $first_page;
        $jsonSource['@graph'][3]['headline'] = $record['name'];
        foreach ($record['author'] as $autores) {
            $jsonSource['@graph'][3]['author'][] = $autores["person"]["name"];
        }
        $json = json_encode($jsonSource, JSON_PRETTY_PRINT);

        echo '<script type="application/ld+json">';

        print_r($json);
        
        echo '</script>';

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
                echo '<td><small><a href="//www.sibi.usp.br/bibliotecas/fisicas/?char='. (string)$bib_fisica[0] .'" target="_blank" rel="noopener noreferrer">'.$item->{'sub-library'}.'</a></small></td>';
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
        if (!empty($files_upload)) {
            foreach ($files_upload as $file) {
                $delete = "";
                if (!empty($session)) {
                    $delete = '<form method="POST" action="single.php?_id='.$id.'">
                                   <input type="hidden" name="delete_file" value="'.$file.'">
                                   <button type="submit" uk-close></button>
                               </form>';
                }

                if (strpos($file, '.pdf') !== false ) {
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$url_base.'/pdf.php?_id='.$id.'&file='.$file.'" target="_blank" rel="noopener noreferrer"><img src="'.$url_base.'/inc/images/pdf.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
                } else {
                    $links_upload[] = '<div class="uk-width-1-4@m"><div class="uk-panel"><a onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);" href="'.$url_base.'/pdf.php?_id='.$id.'&file='.$file.'" target="_blank" rel="noopener noreferrer"><img src="'.$url_base.'/inc/images/pptx.png"  height="70" width="70"></img></a>'.$delete.'</div></div>';
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
    static function consultar_vcusp($termo)
    {
        echo '<h4>Vocabulário Controlado do SIBiUSP</h4>';
	//$xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');
	
        $externalxml = file_get_contents('http://vocab.sibi.usp.br/pt-br/services.php?task=fetch&arg='.$termo.'');
        $xml = simplexml_load_string($externalxml);

        if (is_object($xml) && $xml->{'resume'}->{'cant_result'} != 0) {

            $termo_xml = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchUp&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            foreach (($termo_xml->{'result'}->{'term'}) as $string_up) {
                $string_up_array[] = '<a href="result.php?filter[]=about:&quot;'.$string_up->{'string'}.'&quot;">'.$string_up->{'string'}.'</a>';
            };
            echo 'Você também pode pesquisar pelos termos mais genéricos: ';
            print_r(implode(" -> ", $string_up_array));
            echo '<br/>';
            $termo_xml_down = simplexml_load_file('http://vocab.sibi.usp.br/pt-br/services.php?task=fetchDown&arg='.$xml->{'result'}->{'term'}->{'term_id'}[0].'');
            if (!empty($termo_xml_down->{'result'}->{'term'})) {
                foreach (($termo_xml_down->{'result'}->{'term'}) as $string_down) {
                    $string_down_array[] = '<a href="result.php?filter[]=about:&quot;'.$string_down->{'string'}.'&quot;">'.$string_down->{'string'}.'</a>';
                };
                echo 'Ou pesquisar pelo assuntos mais específicos: ';
                print_r(implode(" - ", $string_down_array));
            }


        } else {
            $termo_naocorrigido[] = $termo;
        }
    }

    static function aGlue($invalcheck)
    {
        global $aglue;
        
        if (array_key_exists($invalcheck, $aglue)  ) {
            return $aglue[$invalcheck];
        } else {
            return $invalcheck;
        }        

    }

    static function isOfThisUnit($ausrund, $aund) 
    {
        global $aglue;

        foreach ($aglue as $k => $v) {
            if (in_array($k, $aund)) {
                $aund[] = $v;
            }
        }

        foreach ($ausrund as $kusund => $vusund) {
            return in_array(USP::aGlue(strtoupper($vusund["siglaUnidade"])), $aund);
        }
        return false;
    }

    

}


/* Function to generate Tables */
function generateDataTable($consulta, $campo, $sort, $sort_orientation, $facet_display_name, $tamanho)
{
    global $client;
    global $type;
    global $index;

    //if (!empty($sort)){
    //    $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';
    //}

    $query["size"] = 0;
    $query = $consulta;
    $query["aggregations"]["counts"]["terms"]["field"] = $campo.'.keyword';
    $query["aggregations"]["counts"]["terms"]["missing"] = "N/D";
    $query["aggregations"]["counts"]["terms"]["size"] = $tamanho;



    $params = [
        'index' => $index,
        'type' => $type,
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
    global $index;
    global $type;
    //if (!empty($sort)){
    //    $sort_query = '"order" : { "'.$sort.'" : "'.$sort_orientation.'" },';
    //}

    $query["size"] = 0;
    $query = $consulta;
    $query["aggregations"]["counts"]["terms"]["field"] = $campo.'.keyword';
    $query["aggregations"]["counts"]["terms"]["missing"] = "N/D";
    $query["aggregations"]["counts"]["terms"]["size"] = $tamanho;

    $params = [
        'index' => $index,
        'type' => $type,
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

        if (!empty($cursor["_source"]["releasedEvent"])) {
            $record[] = "T2  - ".$cursor["_source"]["releasedEvent"]."";
            if (!empty($cursor["_source"]["isPartOf"]["name"])) {
                $record[] = "J2  - ".$cursor["_source"]["isPartOf"]["name"]."";
            }
        } else {
            if (!empty($cursor["_source"]["isPartOf"]["name"])) {
                $record[] = "T2  - ".$cursor["_source"]["isPartOf"]["name"]."";
            }
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
                    $record[] = "IS  - ".str_replace("n.", "", trim(str_replace("n.", "", $periodicos_array_new)))."";
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $record[] = "SP  - ".str_replace("p.", "", trim(str_replace("p.", "", $periodicos_array_new)))."";
                }

            }
        }

        $record[] = "ER  - ";
        $record[] = "";
        $record[] = "";

        $record_blob = implode("\\n", $record);

        return $record_blob;

    }

    static function bibtex($cursor)
    {

        $record = [];

        if (!empty($cursor["_source"]['name'])) {
            $recordContent[] = 'title   = {'.$cursor["_source"]['name'].'}';
        }

        if (!empty($cursor["_source"]['author'])) {
            $authorsArray = [];
            foreach ($cursor["_source"]['author'] as $author) {
                $authorsArray[] = $author["person"]["name"];
            }
            $recordContent[] = 'author = {'.implode(" and ", $authorsArray).'}';
        }

        if (!empty($cursor["_source"]['datePublished'])) {
            $recordContent[] = 'year = {'.$cursor["_source"]['datePublished'].'}';
        }

        if (!empty($cursor["_source"]['doi'])) {
            $recordContent[] = 'doi = {'.$cursor["_source"]['doi'].'}';
        }

        if (!empty($cursor["_source"]['publisher']['organization']['name'])) {
            $recordContent[] = 'publisher = {'.$cursor["_source"]['publisher']['organization']['name'].'}';
        }

        if (!empty($cursor["_source"]["releasedEvent"])) {
            $recordContent[] = 'booktitle   = {'.$cursor["_source"]["releasedEvent"].'}';
        } else {
            if (!empty($cursor["_source"]["isPartOf"]["name"])) {
                $recordContent[] = 'journal   = {'.$cursor["_source"]["isPartOf"]["name"].'}';
            }
        }


        $sha256 = hash('sha256', ''.implode("", $recordContent).'');

        switch ($cursor["_source"]["type"]) {
        case "ARTIGO DE PERIODICO":
            $record[] = '@article{article'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        case "MONOGRAFIA/LIVRO":
            $record[] = '@book{book'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        case "PARTE DE MONOGRAFIA/LIVRO":
            $record[] = '@inbook{inbook'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        case "TRABALHO DE EVENTO":
            $record[] = '@inproceedings{inproceedings'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        case "TRABALHO DE EVENTO-RESUMO":
            $record[] = '@inproceedings{inproceedings'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        case "TESE":
            $record[] = '@mastersthesis{mastersthesis'.substr($sha256, 0, 8).',';
            $recordContent[] = 'school = {Universidade de São Paulo}';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
            break;
        default:
            $record[] = '@misc{misc'.substr($sha256, 0, 8).',';
            $record[] = implode(",\\n", $recordContent);
            $record[] = '}';
        }


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
        if (isset($record["_source"]["datePublished"])) {
            $this->datePublished = $record["_source"]["datePublished"];
        }
        if (isset($record["_source"]["dateCreated"])) {
            $this->dateCreated = $record["_source"]["dateCreated"];
        }
        $this->languageArray = $record["_source"]["language"];
        if (isset($record["_source"]["country"])) {
            $this->countryArray = $record["_source"]["country"];
	}
	if(isset($record["_source"]["author"])){
	    $this->authorArray = $record["_source"]["author"];
	}
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
        if (isset($record["_source"]["releasedEvent"])) {
            $this->releasedEvent = $record["_source"]["releasedEvent"];
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
        if (isset($record["_source"]["USP"]["crossref"]["message"]["funder"])) {
            $this->funderCrossrefArray = $record["_source"]["USP"]["crossref"]["message"]["funder"];
        } else {
            $this->funderCrossrefArray = 0;
        }
        if (isset($record["_source"]["USP"]["crossref"]["message"])) {
            $this->crossrefArray = $record["_source"]["USP"]["crossref"]["message"];
        } else {
            $this->crossrefArray = 0;
        }
        if (isset($record["_source"]["USP"])) {
            $this->USPArray = $record["_source"]["USP"];
        }
        if (isset($record["_source"]["authorUSP"])) {
            $this->authorUSPArray = $record["_source"]["authorUSP"];
        }
        if (isset($record["_source"]["unidadeUSP"])) {
            $this->unidadeUSPArray = $record["_source"]["unidadeUSP"];
        }
        if (isset($record["_source"]["USP"]["programa_pos_sigla"])) {
            $this->programa_pos_sigla = $record["_source"]["USP"]["programa_pos_sigla"];
        }        
        if (isset($record["_source"]["isbn"])) {
            $this->isbn = $record["_source"]["isbn"];
        }
        if (isset($record["_source"]["url"])) {
            $this->url = $record["_source"]["url"];
        }
        if (isset($record["_source"]["doi"])) {
            $this->doi = $record["_source"]["doi"];
        }
        if (isset($record["_source"]["USP"]["titleSearchCrossrefDOI"])) {
            $this->searchDOICrossRef = $record["_source"]["USP"]["titleSearchCrossrefDOI"];
        }
        if (isset($record["_source"]["award"])) {
            $this->awardArray = $record["_source"]["award"];
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

        echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?filter[]=type:&quot;'.mb_strtoupper($this->type, "UTF-8").'&quot;'.(empty($_SERVER['QUERY_STRING'])?'':'&'.$_SERVER['QUERY_STRING']).'">'.$this->type.'</a></p>';

        if (!empty($this->isbn)) {
            $cover_link = 'https://covers.openlibrary.org/b/isbn/'.$this->isbn.'-M.jpg';
            echo  '<p><img src="'.$cover_link.'"></p>';
        }
        echo '</div>';
        echo '<div class="uk-width-4-5@m">';
        echo '<article class="uk-article">';
        echo '<p class="uk-text-lead uk-margin-remove title-link"><a class="uk-link-reset" href="item/'.$this->id.'">'.$this->name.' ('.$this->datePublished.')</a></p>';

        /* Authors */
        echo '<p class="uk-article-meta uk-margin-remove"  style="color: #666">';
        foreach ($this->authorArray as $authors) {
	    if (!empty($authors["person"]["orcid"])) {
                $orcidLink = ' <a href="'.$authors["person"]["orcid"].'"><img src="https://orcid.org/sites/default/files/images/orcid_16x16.png"></a>';
            } else {
                $orcidLink = '';
            }
            if (!empty($authors["person"]["potentialAction"])) {
                $authors_array[]='<a class="link" href="result.php?filter[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' ('.$authors["person"]["potentialAction"].')</a>'.$orcidLink.'';
            } else {
                $authors_array[]='<a class="link" href="result.php?filter[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a>'.$orcidLink.'';
            }
            unset($orcidLink);
	}
	if(!empty($authors_array)){
	    $array_aut = implode("; ", $authors_array);
	}
	if (!empty($array_aut)){
		print_r($array_aut);
	}
        echo '</p>';

        /* IsPartOf */
        if (!empty($this->isPartOfArray["name"])) {
            echo '<p class="uk-text-small uk-margin-remove">'. $t->gettext('Fonte') .': <a href="result.php?filter[]=isPartOf.name:&quot;'.$this->isPartOfArray["name"].'&quot;">'.$this->isPartOfArray["name"].'</a>. ';
        }

        /*  releasedEvent */
        if (!empty($this->releasedEvent)) {
            echo $t->gettext('Nome do evento').': <a href="result.php?filter[]=releasedEvent:&quot;'.$this->releasedEvent.'&quot;">'.$this->releasedEvent.'</a>. ';
        }

        /* Units*/

        if (!empty($this->unidadeUSPArray)) {
            $unique =  array_unique($this->unidadeUSPArray);
            if (count($unique) > 1) {
                echo 'Unidades: ';
            } else {
                echo 'Unidade: ';
            }
            $i = 0;
            foreach ($unique as $unidadeUSP) {
                if($i != 0){
                    echo ', ';
                }
                echo '<a href="result.php?filter[]=unidadeUSP:&quot;'.$unidadeUSP.'&quot;">'.$unidadeUSP.'</a>';
                $i++;
            }
            echo '</p>';
        }

        /* Subjects */
        if (!empty($this->aboutArray)) {
            echo '<p class="uk-text-small uk-margin-remove">'.$t->gettext(sizeof($this->aboutArray) > 1 ? 'Assuntos' : 'Assunto').': ';
            $i = 0;
            foreach ($this->aboutArray as $subject) {
                $subjectItem = "";
                if ($i != 0){
                    $subjectItem .= ", ";
                }
		$subjectItem .= '<a href="result.php?filter[]=about:&quot;'.$subject.'&quot;">'.
			strtoupper($subject).'</a>';
                echo $subjectItem;
                $i++;
            }
            unset($i);
        }
	
        echo '<div class="" style="margin-top: 0.7em; margin-bottom: 0.7em;">';
        
	// Alterado para também executar caso haja link no dspace 
	if(isset($this->completeRecord["_source"]["files"]["database"])){
	    $dspaceFileLink = $this->completeRecord["_source"]["files"]["database"];
	}	
	if (!empty($this->url)||!empty($this->doi)||isset($dspaceFileLink)) {
		$this->onlineAccess($t);
	}

	
        
        /* Implementar AJAX */
        //if ($this->showMetrics == true) {
        //    if (!empty($this->doi)) {
        //        $this->metrics($t, $this->doi, $this->completeRecord);
        //    }
	//}
	$this->citation($t, $this->completeRecord);
        echo '</div>';

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
        echo '<a href="'.$url_base.'/result.php?filter[]=type:&quot;'.mb_strtoupper($this->type, "UTF-8").'&quot;">'.$this->type.'</a>';
        echo '</p>';
        //echo '<h1 class="uk-article-title uk-margin-remove-top uk-link-reset" style="font-size:150%"> ('.$this->datePublished.')</h1>';
        echo '<h1 class="uk-article-title uk-margin-remove-top uk-link-reset" style="font-size:150%">'.$this->name.' ('.$this->datePublished.')</h1>';
        echo '<ul class="uk-list uk-list-striped uk-text-small">';
        /* Authors */
        foreach ($this->authorArray as $authors) {
	    if (!empty($authors["person"]["orcid"])) {
                $orcidLink = ' <a class="link" href="'.$authors["person"]["orcid"].'"><img src="https://orcid.org/sites/default/files/images/orcid_16x16.png"></a>';
            } else {
                $orcidLink = '';
            }
            if (!empty($authors["person"]["affiliation"]["name"])) {
                $authorsList[] =  '<li><a class="link" href="'.$url_base.'/result.php?filter[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' - <span class="uk-text-muted">'.$authors["person"]["affiliation"]["name"].'</span></a>'.$orcidLink.'</li>';
            } elseif (!empty($authors["person"]["potentialAction"])) {
                $authorsList[] = '<li><a class="link" href="'.$url_base.'/result.php?filter[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].' <span class="uk-text-muted">('.$authors["person"]["potentialAction"].')</span></a>'.$orcidLink.'</li>';
            } else {
                $authorsList[] = '<li><a class="link" href="'.$url_base.'/result.php?filter[]=author.person.name:&quot;'.$authors["person"]["name"].'&quot;">'.$authors["person"]["name"].'</a>'.$orcidLink.'</li>';
            }
            unset($orcidLink);
        }
        echo '<li>'.$t->gettext(sizeof($authorsList) > 1 ? 'Autores' : 'Autor').': <ul>'.implode("", $authorsList).'</ul></li>';
        /* USP Authors */
        if (!empty($this->authorUSPArray)) {
            foreach ($this->authorUSPArray as $autoresUSP) {
                $authorsUSPList[] = '<a href="'.$url_base.'/result.php?filter[]=authorUSP.name:&quot;'.$autoresUSP["name"].'&quot;">'.$autoresUSP["name"].' - '.$autoresUSP["unidadeUSP"].' </a>';
            }
            echo '<li>'.$t->gettext(sizeof($authorsUSPList) > 1 ? 'Autores USP' : 'Autor USP').': '.implode("; ", $authorsUSPList).'</li>';
        }
        /* USP Units */
	if (!empty($this->unidadeUSPArray)) {
	    
            $i = 0;
            foreach ($this->unidadeUSPArray as $unidadeUSP) {

		$unidadeUSPList[] = '<a href="'.$url_base.'/result.php?filter[]=unidadeUSP:&quot;'.$unidadeUSP.'&quot;">'.$unidadeUSP.'</a>';
	    }
	    $unidadeUSPList = array_unique($unidadeUSPList);
            echo '<li>'.$t->gettext(sizeof($unidadeUSPList) > 1 ? 'Unidades' : 'Unidade').': '.implode("; ", $unidadeUSPList).'</li>';
        }

        /* Programa Sigla Pós */
        if (!empty($this->programa_pos_sigla)) {
            $programa_pos_sigla = $this->programa_pos_sigla;
            $programa_pos_siglaList[] = '<a href="'.$url_base.'/result.php?filter[]=programa_pos_sigla:&quot;'.$programa_pos_sigla.'&quot;">'.$programa_pos_sigla.'</a>';
            echo '<li>'.$t->gettext('Sigla do Departamento').': '.implode("; ", $programa_pos_siglaList).'</li>';
        }        

        /* DOI */
        if (!empty($this->doi)) {
            echo '<li>DOI: <a href="https://doi.org/'.$this->doi.'" target="_blank" rel="noopener noreferrer">'.$this->doi.'</a></li>';
        }

        /* DOI */
        if (isset($_SESSION['oauthuserdata'])) {
            if (!empty($this->searchDOICrossRef)) {
                echo '<div class="uk-alert-danger" uk-alert><li>DOI com base em busca na CrossRef: <a href="https://doi.org/'.$this->searchDOICrossRef.'" target="_blank" rel="noopener noreferrer">'.$this->searchDOICrossRef.'</a></li></div>';
            }
        }



        /* Subject */
        if (isset($this->aboutArray)) {
            foreach ($this->aboutArray as $subject) {

                $subjectList[] = '<a href="'.$url_base.'/result.php?filter[]=about:&quot;'.$subject.'&quot;">'.$subject.'</a>';
            }
            echo '<li>'.$t->gettext(sizeof($subjectList) > 1 ? 'Assuntos' : 'Assunto').': '.implode("; ", $subjectList).'</li>';
        }

        /* BDTD Subject */
        if ($this->aboutBDTDArray > 0) {
            foreach ($this->aboutBDTDArray as $subject_BDTD) {
                $subjectBDTDList[] = '<a href="'.$url_base.'/result.php?filter[]=USP.about_BDTD:&quot;'.$subject_BDTD.'&quot;">'.$subject_BDTD.'</a>';
            }
            echo '<li>'.$t->gettext('Palavras-chave do autor').': '.implode("; ", $subjectBDTDList).'</li>';
        }

        /* Funder */
        if ($this->funderArray > 0) {
            echo '<li>'.$t->gettext('Agências de fomento').': ';
            echo '<ul class="uk-list uk-text-small">';
            foreach ($this->funderArray as $funder) {
                echo '<li><a href="'.$url_base.'/result.php?filter[]=funder.name:&quot;'.$funder["name"].'&quot;">'.$funder["name"].'</a>';
                if (!empty($funder["projectNumber"]) && $funder["name"] == "Fundação de Amparo à Pesquisa do Estado de São Paulo (FAPESP)") {
                    foreach ($funder["projectNumber"] as $projectNumber) {
                        $projectNumber = str_replace(" ", "", $projectNumber);
			preg_match("/\d\d\/\d{5}-\d/", $projectNumber, $projectNumberMatchArray);
			if(isset($projectNumberMatchArray[0])){
			    echo '<br/>Processo FAPESP: <a href="http://bv.fapesp.br/pt/processo/'.$projectNumberMatchArray[0].'" target="_blank" rel="noopener noreferrer">'.$projectNumber.'</a>';
			}
                    }
                }
                echo '</li>';
            }
            echo '</ul></li>';
        }

        /* Funder - Crossref */
        if (isset($_SESSION['oauthuserdata'])) {
            if ($this->funderCrossrefArray > 0) {
                echo '<div class="uk-alert-danger" uk-alert><p class="uk-text-small uk-margin-remove">'.$t->gettext('Agências de fomento coletadas na CrossRef').': ';
                echo '<ul class="uk-list uk-text-small">';
                foreach ($this->funderCrossrefArray as $funder) {
                    echo '<li>';
                    echo 'Agência de fomento: '.$funder["name"].'</a><br/>';
                    if (isset($funder["award"])) {
                        foreach ($funder["award"] as $projectNumber) {
                            echo 'Projeto: '.$projectNumber.'</a><br/>';
                        }
                    }
                    echo "ALEPHSEQ - 536:<br/>";
                    if (isset($funder["award"])) {
                        foreach ($funder["award"] as $projectNumber) {
                            $projectNumberArray[] = '$$f'.$projectNumber.'';
			}
			if(!empty($projectNumberArray)){
			    echo '<p><b>$$a'.$funder["name"].''.implode("", $projectNumberArray).'</b></p>';
			}
                        $projectNumberArray = [];

                    } else {

                        echo '<p><b>$$a'.$funder["name"].'</b></p>';
                    }
                    echo '</li>';
                }
                echo '</ul></p></div>';
            }
        }

        /* Language */
        foreach ($this->languageArray as $language) {
            $languageList[] = '<a href="'.$url_base.'/result.php?filter[]=language:&quot;'.$language.'&quot;">'.$language.'</a>';
        }
        echo '<li>'.$t->gettext(sizeof($languageList) > 1 ? 'Idiomas' : 'Idioma').': '.implode("; ", $languageList).'</li>';

        /* Abstract */
        if (!empty($this->descriptionArray)) {
            echo '<li class="uk-text-justify">'.$t->gettext('Resumo').': ';
            foreach ($this->descriptionArray as $resumo) {
                echo $resumo;
            }
            echo '</li>';
        }

        /* Imprint */
        if (!empty($this->publisherArray)) {
            echo '<li>'.$t->gettext('Imprenta').':';
            echo '<ul>';
                if (!empty($this->publisherArray["organization"]["name"])) {
                    echo '<li>'.$t->gettext('Editora').': <a href="'.$url_base.'/result.php?filter[]=publisher.organization.name:&quot;'.$this->publisherArray["organization"]["name"].'&quot;">'.$this->publisherArray["organization"]["name"].'</a></li>';
                }
                if (!empty($this->publisherArray["organization"]["location"])) {
                    echo '<li>'.$t->gettext('Local').': <a href="'.$url_base.'/result.php?filter[]=publisher.organization.location:&quot;'.$this->publisherArray["organization"]["location"].'&quot;">'.$this->publisherArray["organization"]["location"].'</a></li>';
                }
                if (!empty($this->datePublished)) {
                    echo '<li>'.$t->gettext('Data de publicação').': <a href="'.$url_base.'/result.php?filter[]=datePublished:&quot;'.$this->datePublished.'&quot;">'.$this->datePublished.'</a></li>';
                }
            echo '</ul></li>';
        }
        if (isset($_SESSION['oauthuserdata'])) {
            if ($this->crossrefArray > 0) {
                echo '<li class="uk-alert-danger">'.$t->gettext('Informações sobre o periódico coletadas na CrossRef').': ';
                echo '<ul class="uk-list uk-text-small">';
                if (!empty($this->crossrefArray["container-title"])) {
                    echo '<li>Título do periódico: '.$this->crossrefArray["container-title"][0].'</li>';
                }
                if (!empty($this->crossrefArray["issn-type"])) {
                    echo '<li>ISSN:<br/>';
                    foreach ($this->crossrefArray["issn-type"] as $crossrefISSN) {
                        echo ''.$crossrefISSN["type"].': '.$crossrefISSN["value"].'<br/>';                    }
                    echo '</li>';
                }
                if (!empty($this->crossrefArray["volume"])) {
                    echo '<li>Volume: '.$this->crossrefArray["volume"].'</li>';
                }
                if (!empty($this->crossrefArray["journal-issue"]["issue"])) {
                    echo '<li>Fascículo: '.$this->crossrefArray["journal-issue"]["issue"][0].'</li>';
                }
                if (!empty($this->crossrefArray["journal-issue"]["published-print"]["date-parts"])) {
                    echo '<li>Ano de publicação: '.$this->crossrefArray["journal-issue"]["published-print"]["date-parts"][0][0].'</li>';
                }
                if (!empty($this->crossrefArray["page"])) {
                    echo '<li>Paginação: '.$this->crossrefArray["page"].'</li>';
                }
                if (!empty($this->crossrefArray["publisher"])) {
                    echo '<li>Editora: '.$this->crossrefArray["publisher"].'</li>';
                }
                echo '</ul></li>';
            }
        }

        /* Data da defesa */
        if (!empty($this->dateCreated)) {
            echo '<li>Data da defesa: '.$this->dateCreated.'</a></li>';
        }

        /* Phisical description */
        if (!empty($this->numberOfPages)) {
            echo '<li>Descrição física: '.$this->numberOfPages.'</a></li>';
        }

        /* Award */
        if (isset($this->awardArray)) {
            foreach ($this->awardArray as $award) {
                $awardList[] = '<a href="'.$url_base.'/result.php?filter[]=award:&quot;'.$award.'&quot;">'.$award.'</a>';
            }
            echo '<li>'.$t->gettext('Premiações recebidas').': '.implode("; ", $awardList).'</li>';
        }

        /* ISBN */
        if (!empty($this->isbn)) {
            echo '<li>ISBN: '.$this->isbn.'</a></li>';
        }

        /* Source */
        if (!empty($this->isPartOfArray)) {
            echo '<li>'.$t->gettext('Fonte').':<ul>';
            if (!empty($this->isPartOfArray["name"])) {
                    echo '<li>Título do periódico: <a href="'.$url_base.'/result.php?filter[]=isPartOf.name:&quot;'.$this->isPartOfArray["name"].'&quot;">'.$this->isPartOfArray["name"].'</a></li>';
            }
            if (!empty($this->isPartOfArray['issn'][0])) {
                echo '<li>ISSN: <a href="'.$url_base.'/result.php?filter[]=issn:&quot;'.$this->isPartOfArray['issn'][0].'&quot;">'.$this->isPartOfArray['issn'][0].'</a></li>';
            }
            if (!empty($this->isPartOfArray["USP"]["dados_do_periodico"])) {
                echo '<li>Volume/Número/Paginação/Ano: '.$this->isPartOfArray["USP"]["dados_do_periodico"].'</li>';
            }
            echo '</ul></li>';
        }

        /*  releasedEvent */
        if (!empty($this->releasedEvent)) {
            echo '<li>'.$t->gettext('Nome do evento').': <a href="result.php?filter[]=releasedEvent:&quot;'.$this->releasedEvent.'&quot;">'.$this->releasedEvent.'</a></li>';
        }

	if (!empty($this->url)||!empty($this->doi)) {
	    $this->onlineAccess($t);
        }

    }

    public function onlineAccess($t)
    {
	
	$firstFile = null;
	if(isset($this->completeRecord["_source"]["files"]["database"])){
            $files = $this->completeRecord["_source"]["files"]["database"];
	    foreach ($files as $file) {
                if($file["status"] == "public"){
                    if($file["file_type"] == "publishedVersion" &&  ($firstFile["status"] != "public" || ($firstFile["status"] == "public" && $firstFile["file_type"] != "publishedVersion"))){
                        $file["icon"] = "inc/images/pdf_publicado.svg";
                        $file["iconAlt"] = $t->gettext("Versão Publicada");
                        $firstFile = $file;
                        break;
		    } else if($file["file_type"] == "acceptedVersion" && ($firstFile["status"] != "public" || ($firstFile["status"] == "public" && $firstFile["file_type"] == "submittedVersion")  )){
                        $file["icon"] = "inc/images/pdf_aceito.svg";
                        $file["iconAlt"] = $t->gettext("Versão Aceita");
                        $firstFile = $file;
			
		    } else if($firstFile["status"] != "public"){
                        $file["icon"] = "inc/images/pdf_submetido.svg";
                        $file["iconAlt"] = $t->gettext("Versão Submetida");
                        $firstFile = $file;
                    }
                }
                else if ($file["status"] == "embargoed" && ($firstFile == null || $firstFile["status"] == "private")) {
                    $file["icon"] = "inc/images/pdf_embargado.svg";
                    if ($_SESSION['localeToUse'] == 'pt_BR'){
                        $file["release_date"] =  date('d/m/Y', strtotime($file["release_date"]));
                    }
                    $file["iconAlt"] = $t->gettext("Disponível em ") . $file["release_date"];
                    $firstFile = $file;
                } else if($firstFile == null && $file["status"] == "private") {
                    $file["icon"] = "inc/images/pdf_privado.svg";
                    $file["iconAlt"] = $t->gettext("Privado");
                    $firstFile = $file;
                }
            
	    }
	}

	    //$fileLink = $this->completeRecord["_source"]["USP"]["fullTextFiles"][0]["link"];
        if (!empty($firstFile["file_link"])){
        //$firstFile = substr($fileLink, 5);
            //echo '<a id="button" class="uk-button uk-button-default uk-button-small" href="'.$fileLink.'" target="_blank" rel="noopener noreferrer" style="margin-right: 1em;">PDF</a>';
            if($firstFile["status"] == "public" ){
                echo '<a id="button" href="'. $firstFile["file_link"] . '" target="_blank" rel="noopener noreferrer" style="margin-right: 1em;" uk-tooltip="'. $firstFile["iconAlt"] .'"><img class="result-icons" src="../'. $firstFile["icon"] . '" alt="'. $firstFile["iconAlt"] .'"></a>';
            } else {
                echo '<img class="result-icons" src="../'. $firstFile["icon"] . '" style="margin-right: 1em;" alt="'. $firstFile["iconAlt"] .'" uk-tooltip="'. $firstFile["iconAlt"].'">';
            }
        }

        if (!empty($this->url)) {
            foreach ($this->url as $url) {
                echo '<a id="button" href="'.$url.'" target="_blank" rel="noopener noreferrer" style="margin-right: 1em;" uk-tooltip="'.$t->gettext('Acesso à fonte').'"><img class="result-icons" src="../inc/images/acesso_fonte.svg" alt="'.$t->gettext('Acesso à fonte').'"></a>';
            }
        }
	if (!empty($this->doi)) {
            echo '<a id="button" href="https://doi.org/'.$this->doi.'" target="_blank" rel="noopener noreferrer" style="margin-right: 1em;"><img class="result-icons" src="../inc/images/doi.svg"  alt="DOI"></a>';
        }

        /*$sfx_array[] = 'rft.atitle='.$this->name.'';
        $sfx_array[] = 'rft.year='.$this->datePublished.'';
        if (!empty($this->isPartOfArray["name"])) {
            $sfx_array[] = 'rft.jtitle='.$this->isPartOfArray["name"].'';
        }
        if (!empty($this->doi)) {
            $sfx_array[] = 'rft_id=info:doi/'.$this->doi.'';
        }
        if (!empty($this->issnArray[0]) && ($this->issnArray[0] != "Não informado")) {
            $sfx_array[] = 'rft.issn='.$this->issnArray[0].'';
        }
        if (!empty($r["_source"]['ispartof_data'][0])) {
            $sfx_array[] = 'rft.volume='.trim(str_replace("v.", "", $r["_source"]['ispartof_data'][0])).'';
        }
        echo ' <a class="uk-text-small" href="//www.sibi.usp.br/sfxlcl41?'.implode("&", $sfx_array).'" target="_blank" rel="noopener noreferrer">'.$t->gettext('ou pesquise este registro no').'<img src="https://www.sibi.usp.br/sfxlcl41/sfx.gif"></a>';*/

        

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
            echo '<div><a href="https://plu.mx/plum/a/?doi='.$doi.'" class="plumx-plum-print-popup" data-hide-when-empty="true" data-badge="true" target="_blank" rel="noopener noreferrer"></a></div>';
            if ($doi != "Não informado") {
                echo '<div><object data="https://api.elsevier.com/content/abstract/citation-count?doi='.$doi.'&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object></div>';
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
        include_once 'inc/citeproc-php/CiteProc.php';
        $csl_abnt = file_get_contents('inc/citeproc-php/style/abnt.csl');
        $csl_apa = file_get_contents('inc/citeproc-php/style/apa.csl');
        $csl_nlm = file_get_contents('inc/citeproc-php/style/nlm.csl');
        $csl_vancouver = file_get_contents('inc/citeproc-php/style/vancouver.csl');
        $lang = "br";
        $citeproc_abnt = new citeproc($csl_abnt, $lang);
        $citeproc_apa = new citeproc($csl_apa, $lang);
        $citeproc_nlm = new citeproc($csl_nlm, $lang);
        $citeproc_vancouver = new citeproc($csl_nlm, $lang);
        $mode = "reference";

	echo '<a class="uk-link-text link" href="#" uk-toggle="target: #citacao'.$record['_id'].'; animation: uk-animation-fade" uk-tooltip="'.$t->gettext('Como citar').'" uk-toggle><img class="result-icons"  src="inc/images/citacao.svg" alt="'.$t->gettext('Como citar').'"></a>';

        //echo '<div class="uk-grid-small uk-child-width-auto" uk-grid>';
            /*echo '<div><a class="uk-button uk-button-text" href="item/'.$record['_id'].'">'.$t->gettext('Ver registro completo').'</a></div>';*/
            
        //echo '</div>';
        echo '<div id="citacao'.$record['_id'].'" class="uk-flex-top" uk-modal>';
        echo '<div class="uk-modal-dialog uk-margin-auto-vertical">';
        echo '<button class="uk-modal-close-default" type="button" uk-close></button>';
            echo '<div class="uk-h6 uk-margin-top">';
                echo '<div class="uk-alert-danger" uk-alert>A citação é gerada automaticamente e pode não estar totalmente de acordo com as normas</div>';
                echo '<ul class="citation">';
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
            echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

class FolioREST
{
    static function loginREST()
    {
        $ch = curl_init();
        $headers = array();
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/authn/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"username\":\"diku_admin\",\"password\":\"admin\"}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $output_parsed = explode(" ", $server_output);
        return $output_parsed[10];
        curl_close($ch);
    }
    static function getContributorNameTypes($cookies)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        //$headers[] = "Content-type: application/json";
        //$headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/contributor-name-types");
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "active==\"true\"");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function getInstancesContext($cookies)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        //$headers[] = "Content-type: application/json";
        //$headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/inventory/instances/context");
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function getIdentifierTypes($cookies, $query = null)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/identifier-types?limit=50$query");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }

    static function queryInstances($cookies, $offset = 0, $limit = 0, $query = null)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Accept: application/json";
        if (!empty($query)) {
            curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/inventory/instances?offset=$offset&limit=$limit&query=$query");
        } else {
            curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/inventory/instances?offset=$offset&limit=$limit");        
        }
        
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $resultArray = json_decode($server_output, true);
        return $resultArray;
        curl_close($ch);
    }

    static function queryInstancesHRID($cookies, $query)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/inventory/instances?query=$query");
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $resultArray = json_decode($server_output, true);
        return $resultArray;
        curl_close($ch);
    }
    static function queryHoldingsHRID($cookies, $query)
    {
        $ch = curl_init();
        $headers = array($cookies);
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "http://172.31.1.52:9130/holdings-storage/holdings?query=$query");
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $resultArray = json_decode($server_output, true);
        return $resultArray;
        curl_close($ch);
    }
    static function addRecordREST($cookies,$json) {
        $ch = curl_init();
        $headers = array();
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, 'http://172.31.1.52:9130/inventory/instances');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function deleteRecordsREST($cookies,$id) {
        $ch = curl_init();
        $headers = array();
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, 'http://172.31.1.52:9130/inventory/instances/'.$id.'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function deleteAllRecordsREST($cookies) {
        $ch = curl_init();
        $headers = array();
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, 'http://172.31.1.52:9130/inventory/instances');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function deleteItensREST($cookies) {
        $ch = curl_init();
        $headers = array();
        $headers[] = "X-Okapi-Tenant: diku";
        $headers[] = 'X-Okapi-Token: '.$cookies.'';
        $headers[] = "Content-type: application/json";
        $headers[] = "Accept: text/plain";
        curl_setopt($ch, CURLOPT_URL, 'http://172.31.1.52:9130/holdings-storage/holdings');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);
    }
    static function logoutREST($folioCookies)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $folioCookies"));
        curl_setopt($ch, CURLOPT_URL, "$folioRest/rest/logout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
    }
    static function folioCodexToElasticSchemaOrg($instance) 
    {
        #print_r($instance);
        $body["doc"]["base"][] = "Livros";
        $body["doc"]["type"] = "Livro";
        $body["doc"]["folioID"] = $instance["id"];
        $body["doc"]["sysno"] = $instance["hrid"];
        #$body["doc"]["isbn"] = "";
        #$body["doc"]["doi"] = "";
        $body["doc"]["language"][] = "";
        #$body["doc"]["country"][] = "";
        #$body["doc"]["alternateName"]="";
        $body["doc"]["name"] = $instance["title"];

        foreach (($instance["contributors"]) as $contributor) {
            $author["person"]["name"] = $contributor["name"];
            $body["doc"]["author"][] = $author;
            unset($contributor);
            unset($author);
        }      


        $body["doc"]["publisher"]["organization"]["name"] = $instance["publication"][0]["publisher"];
        $body["doc"]["publisher"]["organization"]["location"] = $instance["publication"][0]["place"];
        $body["doc"]["USP"]["notes"] = $instance["notes"];
        #$body["doc"]["description"][] = "";
        if (isset($instance["funder"])) {
            $body["doc"]["funder"][]["name"] = "";
            $body["doc"]["funder"][]["projectNumber"][] = "";
        }
        $body["doc"]["about"] = $instance["subjects"];
        #$body["doc"]["isPartOf"]["name"] = "";
        #$body["doc"]["url"][] = "";
        $body["doc"]["datePublished"] = $instance["publication"][0]["dateOfPublication"];
        $body["doc_as_upsert"] = true;
        #echo "<br/><br/>";
        #print_r($body);
        #echo "<br/><br/>";

        return $body;
        
    }    
}

?>
