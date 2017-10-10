<?php
/**
 * Arquivo de classes e funções do principais do sistema
 */

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
    public static function elastic_get ($_id,$type,$fields,$alternative_index = "") {
        global $index;
        global $client;
        $params = [];

        if( strlen($alternative_index) > 0 ){
            $params["index"] = $alternative_index;
        }
        else {
            $params["index"] = $index;
        }
                
        $params["type"] = $type;
        $params["id"] = $_id;
        $params["_source"] = $fields;
        
        $response = $client->get($params);        
        return $response;    
    }    

    /**
     * Executa o commando search no Elasticsearch
     * 
     * @param string $type Tipo de documento no índice do Elasticsearch                         
     * @param string[] $fields Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * @param int $size Quantidade de registros nas respostas
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     * 
     */    
    public static function elastic_search ($type,$fields,$size,$body,$alternative_index = "") {
        global $index;
        global $client;
        $params = [];

        if( strlen($alternative_index) > 0 ){
            $params["index"] = $alternative_index;
        }
        else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["_source"] = $fields;
        $params["size"] = $size;
        $params["body"] = $body;
        
        $response = $client->search($params);        
        return $response;
    }
    
    /**
     * Executa o commando update no Elasticsearch
     * 
     * @param string $_id ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch  
     * 
     */     
    public static function elastic_update ($_id,$type,$body,$alternative_index = "") {
        global $index;
        global $client;
        $params = [];

        if( strlen($alternative_index) > 0 ){
            $params["index"] = $alternative_index;
        }
        else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["id"] = $_id;
        $params["body"] = $body;
        
        $response = $client->update($params);        
        return $response;
    }

    /**
     * Executa o commando delete no Elasticsearch
     * 
     * @param string $_id ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch     
     * 
     */     
    public static function elastic_delete ($_id,$type,$alternative_index = "") {
        global $index;
        global $client;
        $params = [];

        if( strlen($alternative_index) > 0 ){
            $params["index"] = $alternative_index;
        }
        else {
            $params["index"] = $index;
        }  
        
        $params["type"] = $type;
        $params["id"] = $_id;
        
        $response = $client->delete($params);        
        return $response;
    }
    
    /**
     * Executa o commando delete_by_query no Elasticsearch
     * 
     * @param string $_id ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch  
     * 
     */     
    public static function elastic_delete_by_query ($_id,$type,$body,$alternative_index = "") {
        global $index;
        global $client;
        $params = [];

        if( strlen($alternative_index) > 0 ){
            $params["index"] = $alternative_index;
        }
        else {
            $params["index"] = $index;
        }    

        $params["type"] = $type;
        $params["id"] = $_id;
        $params["body"] = $body;
        
        $response = $client->deleteByQuery($params);        
        return $response;
    }    
    
    /**
     * Executa o commando update no Elasticsearch e retorna uma resposta em html
     * 
     * @param string $_id ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch  
     * 
     */     
    static function store_record ($_id,$type,$body){
        $response = elasticsearch::elastic_update($_id,$type,$body);    
        echo '<br/>Resultado: '.($response["_id"]).', '.($response["result"]).', '.($response["_shards"]['successful']).'<br/>';   

    }
    
}

class get {
    
    static function analisa_get($get) {
        $query = [];

        if (!empty($get['fields'])) {
            $query["query"]["query_string"]["fields"] = $get['fields'];
        } else {
            $query["query"]["query_string"]["fields"][] = "_all";
        } 

        /* codpes */
        if (!empty($get['codpes'])){        
            $get['search'][] = 'authorUSP.codpes.keyword:'.$get['codpes'].'';
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
        
        if (!empty($get['search'])){
            $search = implode(" ",$get['search']);
            $query["query"]["query_string"]["query"] = $search;
        } else {
            $query["query"]["query_string"]["query"] = "*";
        }
     
        $query["query"]["query_string"]["default_operator"] = "AND";
        $query["query"]["query_string"]["analyzer"] = "portuguese";
        $query["query"]["query_string"]["phrase_slop"] = 10;
        
        return compact('page','query','limit','skip');
    }    
    
}

class users {
    
    static function store_user ($userdata){
	global $index;
	
        $query["doc"]["nomeUsuario"] = $userdata->{'nomeUsuario'};
        $query["doc"]["tipoUsuario"] = $userdata->{'tipoUsuario'};
        $query["doc"]["emailPrincipalUsuario"] = $userdata->{'emailPrincipalUsuario'};
        $query["doc"]["emailAlternativoUsuario"] = $userdata->{'emailAlternativoUsuario'};
        $query["doc"]["emailUspUsuario"] = $userdata->{'emailUspUsuario'};
        $query["doc"]["numeroTelefoneFormatado"] = $userdata->{'numeroTelefoneFormatado'};       
        
        $i = 0;
        foreach ($userdata->{'vinculo'} as $vinculo) {
            $query["doc"]["vinculo"][$i]["tipoVinculo"] = $vinculo->{'tipoVinculo'};
            $query["doc"]["vinculo"][$i]["codigoSetor"] = $vinculo->{'codigoSetor'};
            $query["doc"]["vinculo"][$i]["nomeAbreviadoSetor"] = $vinculo->{'nomeAbreviadoSetor'};
            $query["doc"]["vinculo"][$i]["nomeSetor"] = $vinculo->{'nomeSetor'};
            $query["doc"]["vinculo"][$i]["codigoUnidade"] = $vinculo->{'codigoUnidade'};
            $query["doc"]["vinculo"][$i]["siglaUnidade"] = $vinculo->{'siglaUnidade'};
            $query["doc"]["vinculo"][$i]["nomeUnidade"] = $vinculo->{'nomeUnidade'};
            $i++;
        }
        $query["doc"]["doc_as_upsert"] = true;

        $num_usp = $userdata->{'loginUsuario'};
        $params = [
            'index' => $index,
            'type' => 'users',
            'id' => "$num_usp",
            'body' => $query
        ];
        $response = elasticsearch::elastic_update($num_usp,"users",$query);
    }    
    
}

class facets {
    
    public function facet($field,$size,$field_name,$sort,$sort_type,$get_search) {
        global $type;
        $query = $this->query;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        $query["aggs"]["counts"]["terms"]["missing"] = "Não preenchido";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"][$sort_type] = $sort;
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;
        
        $response = elasticsearch::elastic_search($type,null,0,$query);
	
	$result_count = count($response["aggregations"]["counts"]["buckets"]);
	
		
	if ($result_count == 0) {
		
	} elseif ($result_count <= 5) {
	
		echo '<li class="uk-parent">';    
		echo '<a href="#" style="color:#333">'.$field_name.'</a>';
		echo ' <ul class="uk-nav-sub">';
		foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {            
            if ($facets['key'] == "Não preenchido") {
                if(!empty($_SESSION['oauthuserdata'])){
                    echo '<li>';
                    echo '<div uk-grid>
                        <div class="uk-width-3-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></div>';
                    echo '</div></li>';                   
                }
            } else {
                echo '<li>';
                echo '<div uk-grid>
                    <div class="uk-width-2-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=+'.$field.'.keyword:&quot;'.str_replace('&','%26',$facets['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></div>
                    <div class="uk-width-1-3" style="color:#333">                    
                    <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-'.$field.'.keyword:&quot;'.$facets['key'].'&quot;" title="NÃO" style="color:#0040ff;font-size: 65%">Não</a>
                    ';
                if (isset($get_search)) {
                    echo '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=OR '.$field.'.keyword:&quot;'.$facets['key'].'&quot;" title="OU" style="color:#0040ff;font-size: 65%">Ou</a>';
                } 
                echo '</div></div></li>';                

            }    		

		};
		echo   '</ul></li>';
	
	} else {
		$i = 0;
		echo '<li class="uk-parent">';    
		echo '<a href="#" style="color:#333">'.$field_name.'</a>';
		echo ' <ul class="uk-nav-sub">';
		while ($i <= 5) {
            if ($response["aggregations"]["counts"]["buckets"][$i]['key'] == "Não preenchido") {
                if(!empty($_SESSION['oauthuserdata'])){
                    echo '<li>';
                    echo '<div uk-grid>
                        <div class="uk-width-3-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.$response["aggregations"]["counts"]["buckets"][$i]['key'].' ('.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'],0,',','.').')</a></div>';
                    echo '</div></li>';
                }
            } else {
                echo '<li>';
                echo '<div uk-grid>
                    <div class="uk-width-2-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=+'.$field.'.keyword:&quot;'.str_replace('&','%26',$response["aggregations"]["counts"]["buckets"][$i]['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$response["aggregations"]["counts"]["buckets"][$i]['key'].' ('.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'],0,',','.').')</a></div>
                    <div class="uk-width-1-3" style="color:#333">                    
                    <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-'.$field.'.keyword:&quot;'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'&quot;" title="NÃO" style="color:#0040ff;font-size: 65%">Não</a>
                    ';
                if (isset($get_search)) {
                    echo '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=OR '.$field.'.keyword:&quot;'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'&quot;" title="NÃO" style="color:#0040ff;font-size: 65%">Ou</a>';
                } 
                echo '</div></div></li>';                

            }

			$i++;
		}	

		echo '<a href="#'.str_replace(".","_",$field).'" uk-toggle>mais >></a>';
		echo   '</ul></li>';
		
		
		echo '
		<div id="'.str_replace(".","_",$field).'" uk-modal="center: true">
			<div class="uk-modal-dialog">
				<button class="uk-modal-close-default" type="button" uk-close></button>
				<div class="uk-modal-header">
					<h2 class="uk-modal-title">'.$field_name.'</h2>
				</div>
				<div class="uk-modal-body">
				<ul class="uk-list">
		';
            
		foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
            if ($facets['key'] == "Não preenchido") {
                if(!empty($_SESSION['oauthuserdata'])){
                    echo '<li>';
                    echo '<div uk-grid>
                        <div class="uk-width-3-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></div>';
                    echo '</div></li>';
                }                   

            } else {
                echo '<li>';
                echo '<div uk-grid>
                    <div class="uk-width-2-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=+'.$field.'.keyword:&quot;'.str_replace('&','%26',$facets['key']).'&quot;">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a></div>
                    <div class="uk-width-1-3" style="color:#333">
                    <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-'.$field.'.keyword:&quot;'.$facets['key'].'&quot;">Não</a>
                    ';
                if (isset($get_search)) {
                    echo '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=OR '.$field.'.keyword:&quot;'.$facets['key'].'&quot;">Ou</a>';
                } 
                echo '</div></div></li>';
            }    
		};
		echo '</ul>';
		echo '    
		</div>
		<div class="uk-modal-footer uk-text-right">
			<button class="uk-button uk-button-default uk-modal-close" type="button">Fechar</button>
		</div>
		</div>
		</div>
		';
	
	}		
	
	

	


    }
    
    public function rebuild_facet($field,$size,$nome_do_campo) {
        global $type;
        $query = $this->query;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"]["_count"] = "desc";
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;        
        
        $response = elasticsearch::elastic_search($type,null,0,$query);

        echo '<li">';
        echo '<a href="#">'.$nome_do_campo.'</a>';
        echo ' <ul class="uk-list">';
        foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
            echo '<li class="uk-h6">';        
            echo '<a href="autoridades.php?term='.$facets['key'].'">'.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a>';
            echo '</li>';
        };
        echo   '</ul>
          </li>';

    }

    public function facet_range($field,$size,$nome_do_campo) {
        global $type;
        $query = $this->query;
        $query["aggs"]["ranges"]["range"]["field"] = "$field";
        $query["aggs"]["ranges"]["range"]["ranges"][0]["to"] = 0.5;
        $query["aggs"]["ranges"]["range"]["ranges"][1]["from"] = 0.5;
        $query["aggs"]["ranges"]["range"]["ranges"][1]["to"] = 1;
        $query["aggs"]["ranges"]["range"]["ranges"][2]["from"] = 1;
        $query["aggs"]["ranges"]["range"]["ranges"][2]["to"] = 2;
        $query["aggs"]["ranges"]["range"]["ranges"][3]["from"] = 2;
        $query["aggs"]["ranges"]["range"]["ranges"][3]["to"] = 5;
        $query["aggs"]["ranges"]["range"]["ranges"][4]["from"] = 5;
        $query["aggs"]["ranges"]["range"]["ranges"][3]["to"] = 10;
        $query["aggs"]["ranges"]["range"]["ranges"][4]["from"] = 10;        
        //$query["aggs"]["counts"]["terms"]["size"] = $size;               
        
        $response = elasticsearch::elastic_search($type,null,0,$query);

        $result_count = count($response["aggregations"]["ranges"]["buckets"]);       
            
        if ($result_count > 0) {
            echo '<li class="uk-parent">';    
            echo '<a href="#" style="color:#333">'.$nome_do_campo.'</a>';
            echo ' <ul class="uk-nav-sub">';
            foreach ($response["aggregations"]["ranges"]["buckets"] as $facets) {
                $facets_array = explode("-",$facets['key']);
                echo '<li>
                    <div uk-grid>
                    <div class="uk-width-3-3 uk-text-small" style="color:#333">';
                    echo '<a style="color:#333" href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=+'.$field.':['.$facets_array[0].' TO '.$facets_array[1].']">Intervalo '.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a>';
                    echo '</div>';
                
                echo '</div></li>';
            };
            echo   '</ul></li>';
        }    


    }
}

class citation {
    
    /* Pegar o tipo de material */
    static function get_type($material_type){
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
    
    static function citation_query($citacao) {

        $array_citation = [];
        $array_citation["type"] = citation::get_type($citacao["type"]);
        $array_citation["title"] = $citacao["name"];
        
        if (!empty($citacao["author"])) {
            $i = 0;
            foreach ($citacao["author"] as $authors){
                $array_authors = explode(',', $authors["person"]["name"]);
                $array_citation["author"][$i]["family"] = $array_authors[0];
                if (!empty($array_authors[1])){
                    $array_citation["author"][$i]["given"] = $array_authors[1];
                }                
                $i++;
            }
        }
        
        if (!empty($citacao["isPartOf"]["name"])) {
            $array_citation["container-title"] = $citacao["isPartOf"]["name"];
        }
        if (!empty($citacao["doi"])) {
            $array_citation["DOI"] = $citacao["doi"];
        }        
        if (!empty($citacao["url"][0])) {
            $array_citation["URL"] = $citacao["url"][0];
        }           
        if (!empty($citacao["publisher"]["organization"]["name"])) {
            $array_citation["publisher"] = $citacao["publisher"]["organization"]["name"];
        }
        if (!empty($citacao["publisher"]["organization"]["location"])) {
            $array_citation["publisher-place"] = $citacao["publisher"]["organization"]["location"];
        }
        if (!empty($citacao["datePublished"])) {
            $array_citation["issued"]["date-parts"][0][] = intval($citacao["datePublished"]);
        }

        if (!empty($citacao["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",",$citacao["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $array_citation["volume"] = str_replace("v.","",$periodicos_array_new);
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $array_citation["issue"] = str_replace("n.","",$periodicos_array_new);
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $array_citation["page"] = str_replace("p.","",$periodicos_array_new);
                }

            }
        }         

       
        $json = json_encode($array_citation);
        $data = json_decode($json);
        return $data;    
    }
    
}

class ui {
    
    /* Montar a barra de paginação */
    static function pagination ($page, $total, $limit, $t){

        echo '<div class="uk-child-width-expand@s uk-grid-divider" uk-grid>';
            echo '<div>';
                echo '<ul class="uk-pagination">';
                if ($page == 1) {
                    echo '<li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> '.$t->gettext('Anterior').'</a></li>';
                } else {                    
                    $_GET["page"] = $page-1 ;
                    echo '<li><a href="result.php?'.http_build_query($_GET).'"><span class="uk-margin-small-right" uk-pagination-previous></span> '.$t->gettext('Anterior').'</a></li>';
                }
                echo '</ul>';
            echo '</div>';
            echo '<div>';
                echo '<p class="uk-text-center">'.number_format($total,0,',','.') .'&nbsp;'. $t->gettext('registros').'</p>'; 
            echo '</div>';
            echo '<div>';
                if (isset($_GET["sort"])) {
                    echo '<a href="http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.str_replace('&sort='.$_GET["sort"].'',"",$_SERVER['QUERY_STRING']).'">'.$t->gettext('Ordenar por Data').'</a>';
                } else {
                    echo '<a href="http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&sort=name.keyword">'.$t->gettext('Ordenar por Título').'</a>';
                }
            echo '</div>';
            echo '<div>';
                echo '<ul class="uk-pagination">';
                    if ($total/$limit > $page){
                        $_GET["page"] = $page+1;
                        echo '<li class="uk-margin-auto-left"><a href="result.php?'.http_build_query($_GET).'">'.$t->gettext('Próxima').' <span class="uk-margin-small-left" uk-pagination-next></span></a></li>';
                    } else {
                        echo '<li class="uk-margin-auto-left"><a href="#">'.$t->gettext('Próxima').' <span class="uk-margin-small-left" uk-pagination-next></span></a></li>';
                    }
                echo '</ul>';    
            echo '</div>';
        echo '</div>';      

    }    
}

class metrics {

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

    static function get_aminer($title){
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.aminer.org/api/search/pub?query='.urlencode($title).'',
            //CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $data = json_decode($resp, TRUE);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);
    }
    
    static function get_opencitation_doi($doi) {
        $sparql = new EasyRdf_Sparql_Client('http://opencitations.net/sparql');
        $result = $sparql->query(
            'PREFIX cito: <http://purl.org/spar/cito/>
            PREFIX dcterms: <http://purl.org/dc/terms/>
            PREFIX datacite: <http://purl.org/spar/datacite/>
            PREFIX literal: <http://www.essepuntato.it/2010/06/literalreification/>
            SELECT ?citing ?title WHERE {
              ?id a datacite:Identifier ;
                datacite:usesIdentifierScheme datacite:doi ;
                literal:hasLiteralValue "'.$doi.'" .
              ?br 
                datacite:hasIdentifier ?id ;
                ^cito:cites ?citing .
              ?citing dcterms:title ?title
            }'
        );        
        return $result;
    }
    
    static function get_wikipedia($url){
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://pt.wikipedia.org/w/api.php?action=query&list=exturlusage&format=json&euquery='.$url.'',
            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $data = json_decode($resp, TRUE);
        return $data;
        // Close request to clear up some resources
        curl_close($curl);
    }     

}

?>