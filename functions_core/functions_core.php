<?php
/**
 * Arquivo de classes e funções do principais do sistema
 */

/**
 * Classe de interação com o Elasticsearch
 */
class elasticsearch
{

    /**
     * Executa o commando get no Elasticsearch
     *
     * @param string   $_id               ID do documento.
     * @param string   $type              Tipo de documento no índice do Elasticsearch
     * @param string[] $fields            Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * @param string   $alternative_index Caso use indice alternativo
     *
     */
    public static function elastic_get($_id, $type, $fields, $alternative_index = "")
    {
        global $index;
	global $client;

        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["id"] = $_id;
	$params["_source"] = $fields;
        $response = $client->get($params);
        return __htmlspecialchars($response);
    }

    /**
     * Executa o commando search no Elasticsearch
     *
     * @param string   $type   Tipo de documento no índice do Elasticsearch
     * @param string[] $fields Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * @param int      $size   Quantidade de registros nas respostas
     * @param resource $body   Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    public static function elastic_search($type, $fields, $size, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0 ) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["_source"] = $fields;
        $params["size"] = $size;
	$params["body"] = $body;

        $response = $client->search($params);
        return __htmlspecialchars($response);
    }

    /**
     * Executa o commando update no Elasticsearch
     *
     * @param string   $_id  ID do documento
     * @param string   $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    public static function elastic_update($_id, $type, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["id"] = $_id;
        $params["body"] = $body;

        $response = $client->update($params);
        return $response;
    }

    /**
     *Executa o comando index no Elasticsearch
     */
    public static function elastic_index($_id, $type, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];
        if (strlen($alternative_index) > 0)
            $params["index"] = $alternative_index;
        else
            $params["index"] = $index;
        $params["type"] = $type;
        $params["id"] = $_id;
        $params["body"] = $body["doc"];
        if ($params["index"]=="bdta" && !$params["body"]["USP"]["indicado_por_orgao"]){
            echo("\nIgnoring registry ").$params["id"]." because it was not indicated\n";
            return;
        }
	self::elastic_clean_record($_id, $type);
        $response = $client->index($params);
        ElasticPatch::syncElastic($_id);
	$DSpaceCookies = DSpaceREST::loginREST();
	$uuid = DSpaceREST::searchItemDSpace($_id, $DSpaceCookies);
	DSpaceREST::logoutREST($DSpaceCookies);
	if (!empty($uuid)){
	    echo("Found in dspace. UUID: " . $uuid . "\n");
            DSpaceREST::refreshMetadata($_id);
	    //$cursor = elasticsearch::elastic_get($_id, $type, null);
            //$metadata = DSpaceREST::buildDC($cursor, $_id);
	    //var_dump($metadata);
	}
        return $response;
    }

    /**
     * Executa o commando delete no Elasticsearch
     *
     * @param string $_id  ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch
     *
     */
    public static function elastic_delete($_id, $type, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["type"] = $type;
        $params["id"] = $_id;
        $params["client"]["ignore"] = 404;

        $response = $client->delete($params);
        return $response;
    }

    /**
     * Limpar um registro em todas as possíveis bases
     *
     * @param string $_id  ID do documento
     * @param string $type Tipo de documento no índice do Elasticsearch
     *
     */
    public static function elastic_clean_record($_id, $type)
    {
        global $client;
    	$indexes = array('acorde', 'bdpi', 'bdta', 'bdta_homologacao', 'ebooks', 'opac');
        $params = [];
        $params["type"] = $type;
        $params["id"] = $_id;
        $params["client"]["ignore"] = 404;
	echo("Cleaning record id " . $_id . " from all indexes\n");
        	foreach ($indexes as $index) 
    		{
			$params["index"] = $index;
	    		$client->delete($params);
		}
        return true;
    }

    /**
     * Executa o commando delete_by_query no Elasticsearch
     *
     * @param string   $_id  ID do documento
     * @param string   $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    public static function elastic_delete_by_query($_id, $type, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
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
     * @param string   $_id  ID do documento
     * @param string   $type Tipo de documento no índice do Elasticsearch
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    static function store_record($_id, $type, $body)
    {
        $response = elasticsearch::elastic_update($_id, $type, $body);
        echo '<br/>Resultado: '.($response["_id"]).', '.($response["result"]).', '.($response["_shards"]['successful']).'<br/>';

    }

}

class get
{

    static function analisa_get($get)
    {
	$query = [];

        if (!empty($get['fields'])) {
            $query["query"]["bool"]["must"]["query_string"]["fields"] = $get['fields'];
        } else {
            $query["query"]["bool"]["must"]["query_string"]["default_field"] = "*";
        }
        /* codpes */
        if (!empty($get['codpes'])) {
            $get['search'][] = 'authorUSP.codpes:'.$get['codpes'].'';
        }

	/* Pagination */
        if (isset($get['page'])) {
		if($get['page'] < 1){
			$get['page'] = 1;
		}
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

        $i_filter = 0;
	if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter) {
		    $filter_array = explode(":", $filter);
		if(!empty($filter_array[1])){
			$filter_array_term = str_replace('"', "", (string)$filter_array[1]);
                    $query["query"]["bool"]["filter"][$i_filter]["term"][(string)$filter_array[0].".keyword"] = $filter_array_term;
		}
                $i_filter++;
            }
        }

        if (!empty($get['notFilter'])) {
            $i_notFilter = 0;
            foreach ($get['notFilter'] as $notFilter) {
	        $notFilterArray = explode(":", $notFilter);
		if(!empty($notFilterArray[1])){
		    $notFilterArrayTerm = str_replace('"', "", (string)$notFilterArray[1]);
                    $query["query"]["bool"]["must_not"][$i_notFilter]["term"][(string)$notFilterArray[0].".keyword"] = $notFilterArrayTerm;
		}
                $i_notFilter++;
            }
        }

	if (!empty($get['search'])) {
            foreach ($get['search'] as $getSearch) {
                if (strpos($getSearch, 'base.keyword') !== false) {
                    $query["query"]["bool"]["filter"][$i_filter]["term"]["base.keyword"] = "Produção científica";
                    $i_filter++;
                } else {
                    $getSearchArray[] = $getSearch;
                }
	    }
	    $getSearchResult = implode(" ", $getSearchArray);
	    $query["query"]["bool"]["must"]["query_string"]["query"] = str_replace(" ", " AND ", $getSearchResult);
        } 

        if (!empty($get['range'])) {
            $query["query"]["bool"]["must"]["query_string"]["query"] = $get['range'][0];
        }         
        
        if (empty($query["query"]["bool"]["must"]["query_string"]["query"])) {
            $query["query"]["bool"]["must"]["query_string"]["query"] = "*";
        }

        $query["query"]["bool"]["must"]["query_string"]["default_operator"] = "AND";
        $query["query"]["bool"]["must"]["query_string"]["analyzer"] = "portuguese";
	$query["query"]["bool"]["must"]["query_string"]["phrase_slop"] = 10;

	
	if (is_bdta() && !is_staffUser()){
		$query["query"]["bool"]["must"]["query_string"]["query"] = filtro_bdta_indicado($query["query"]["bool"]["must"]["query_string"]["query"]);
	}
	
        return compact('page', 'query', 'limit', 'skip');
    }

}

class Users
{
    static function store_user($userdata)
    {
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
        $response = elasticsearch::elastic_update($num_usp, "users", $query);
    }

}

class Facets
{
    public function facet($field,$size,$field_name,$sort,$sort_type,$get_search, $classHtmlElement = "capitalize")
    {
        global $type;
        $query = $this->query;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        $query["aggs"]["counts"]["terms"]["missing"] = "Não preenchido";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"][$sort_type] = $sort;
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;

        $response = elasticsearch::elastic_search($type, null, 0, $query);

	$result_count = count($response["aggregations"]["counts"]["buckets"]);

        if (($result_count > 0) && ($result_count <= 5)) {
	    echo '<li class="uk-parent">';
            echo '<a href="#" style="color:#123e72;">'.$field_name.'</a>';
            echo ' <ul class="uk-nav-sub">';
            foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
		if ($facets['key'] == "Não preenchido") {
                    echo '';
                    /*if (!empty($_SESSION['oauthuserdata'])) {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-3-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.'" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.mb_strtolower($facets['key'], "UTF-8").' ('.number_format($facets['doc_count'], 0, ',', '.').')</a></div>';
                        echo '</div></li>';
		    }*/
                } else {
                    if (!empty($_SESSION['oauthuserdata'])) {
                        echo '<li>';
                        echo '<div uk-grid>
                        <div class="uk-width-2-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.'" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;"  title="E" style="font-size: 90%">'.mb_strtolower($facets['key'], "UTF-8").' (~'.number_format($facets['doc_count'],0,',','.').')</a></div>
                        <div class="uk-width-1-3" style="color:#333">
                        <a class="link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;" title="Remover do resultado" ><span uk-icon="icon: minus-circle; ratio: 1"></span></a>
                        ';
                        echo '</div></div></li>';                       

                    } else {
			    echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-2-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.' link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;"  title="E" style="font-size: 90%">'.mb_strtolower($facets['key'], "UTF-8").' (~'.number_format($facets['doc_count'],0,',','.').')</a></div>
                            <div class="uk-width-1-3" style="color:#333">
                            <a class="link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;" title="Remover do resultado"><span uk-icon="icon: minus-circle; ratio: 1"></span></a>
                            ';
                        echo '</div></div></li>';
                    }
                }

            }
            echo '</ul></li>';

        } elseif($result_count > 5) {
            $i = 0;
            echo '<li class="uk-parent">';
            echo '<a href="#"  style="color:#123e72;">'.$field_name.'</a>';
            echo ' <ul class="uk-nav-sub">';
            while ($i < 5) {
		if ($response["aggregations"]["counts"]["buckets"][$i]['key'] == "Não preenchido") {
		    echo '';
                    /*if (!empty($_SESSION['oauthuserdata'])) {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-3-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.'" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.mb_strtolower($response["aggregations"]["counts"]["buckets"][$i]['key'], "UTF-8").' ('.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'],0,',','.').')</a></div>';
                        echo '</div></li>';
		    }*/
                } else {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-2-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.' link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $response["aggregations"]["counts"]["buckets"][$i]['key']).'&quot;"  title="E" style="font-size: 90%">'.mb_strtolower($response["aggregations"]["counts"]["buckets"][$i]['key'], "UTF-8").' ('.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'],0,',','.').')</a></div>
                            <div class="uk-width-1-3" style="color:#333">
                            <a class="link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'&quot;" title="Remover do resultado"><span uk-icon="icon: minus-circle; ratio: 1"></span></a>
                            ';
                        echo '</div></div></li>';                                                
                }

                $i++;
            }

            echo '<a id="link" href="#'.str_replace(".", "_", $field).'" uk-toggle>mais >></a>';
            echo   '</ul></li>';


            echo '
            <div id="'.str_replace(".", "_", $field).'" uk-modal="center: true">
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
	                echo '';
                    /*if (!empty($_SESSION['oauthuserdata'])) {
                        echo '<li>';
			echo '<div uk-grid>
                             <div class="uk-width-3-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.'" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.mb_strtolower($facets['key'], "UTF-8").' ('.number_format($facets['doc_count'], 0, ',', '.').')</a></div>';
                        echo '</div></li>';
		}*/

                } else {
                    if ($facets['key'] == "Não preenchido") {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-2-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.' link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;">'.mb_strtolower($facets['key'], "UTF-8").'</a></div>
                            <div class="uk-width-1-3" style="color:#333">
                            <a class="link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;" title="Remover do resultado"><span uk-icon="icon: minus-circle; ratio: 1"></span></a>
                            ';
                        echo '</div></div></li>';
                    } else {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-2-3 uk-text-small" style="color:#333"><a class="'.$classHtmlElement.' link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;">'.mb_strtolower($facets['key'], "UTF-8").' ('.number_format($facets['doc_count'], 0, ',', '.').')</a></div>
                            <div class="uk-width-1-3" style="color:#333">
                            <a class="link" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;"><span uk-icon="icon: minus-circle; ratio: 1"></span></a>
                            ';
                        echo '</div></div></li>';
                    }

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

    public function rebuild_facet($field,$size,$nome_do_campo)
    {
        global $type;
        $query = $this->query;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"]["_count"] = "desc";
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;

        $response = elasticsearch::elastic_search($type, null, 0, $query);

        echo '<li class="uk-parent">';
        echo '<a href="#" style="color:#333">'.$nome_do_campo.'</a>';
        echo ' <ul class="uk-nav-sub">';
        foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
            $termCleaned = str_replace("&", "*", $facets['key']);
            echo '<li">';
            echo "<div uk-grid>";
            echo '<div class="uk-width-2-3 uk-text-small" style="color:#333">';
            echo '<a href="admin/autoridades.php?term=&quot;'.$termCleaned.'&quot;" style="color:#0040ff;font-size: 90%">'.$termCleaned.' ('.number_format($facets['doc_count'], 0, ',', '.').')</a>';
            echo '</div>';
            echo '</li>';
        };
        echo   '</ul>
          </li>';

    }

    public function facet_range($field,$size,$nome_do_campo,$type_of_number = "")
    {
        global $type;
        $query = $this->query;
        if ($type_of_number == "INT") {
            $query["aggs"]["ranges"]["range"]["field"] = "$field";
            $query["aggs"]["ranges"]["range"]["ranges"][0]["to"] = 1;
            $query["aggs"]["ranges"]["range"]["ranges"][1]["from"] = 1;
            $query["aggs"]["ranges"]["range"]["ranges"][1]["to"] = 2;
            $query["aggs"]["ranges"]["range"]["ranges"][2]["from"] = 2;
            $query["aggs"]["ranges"]["range"]["ranges"][2]["to"] = 5;
            $query["aggs"]["ranges"]["range"]["ranges"][3]["from"] = 5;
            $query["aggs"]["ranges"]["range"]["ranges"][3]["to"] = 10;
            $query["aggs"]["ranges"]["range"]["ranges"][4]["from"] = 10;
            $query["aggs"]["ranges"]["range"]["ranges"][3]["to"] = 20;
            $query["aggs"]["ranges"]["range"]["ranges"][4]["from"] = 20;
        } else {
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
        }

        //$query["aggs"]["counts"]["terms"]["size"] = $size;

        $response = elasticsearch::elastic_search($type, null, 0, $query);

        $result_count = count($response["aggregations"]["ranges"]["buckets"]);

        if ($result_count > 0) {
            echo '<li class="uk-parent">';
            echo '<a href="#" style="color:#333">'.$nome_do_campo.'</a>';
            echo ' <ul class="uk-nav-sub">';
            foreach ($response["aggregations"]["ranges"]["buckets"] as $facets) {
                $facets_array = explode("-", $facets['key']);
                echo '<li>
                    <div uk-grid>
                    <div class="uk-width-3-3 uk-text-small" style="color:#333">';
                    echo '<a style="color:#333" href="//'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]='.$field.':['.$facets_array[0].' TO '.$facets_array[1].']">Intervalo '.$facets['key'].' ('.number_format($facets['doc_count'],0,',','.').')</a>';
                    echo '</div>';

                echo '</div></li>';
            };
            echo   '</ul></li>';
        }


    }
}

class citation
{
    /* Pegar o tipo de material */
    static function get_type($material_type)
    {
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
        case "TRABALHO DE CONCLUSAO DE CURSO - TCC":
            return "thesis";
	break;
        }
    }
    
    static function citation_query($citacao)
    {
	include_once 'unidades.php';
        $array_citation = [];
	$array_citation["type"] = citation::get_type($citacao["type"]);
	$array_citation["title"] = $citacao["name"];

	if(!empty($citacao["inSupportOf"])){
		$array_citation["genre"] = $citacao["inSupportOf"];
	}

        if (!empty($citacao["author"])) {
            $i = 0;
	    foreach ($citacao["author"] as $authors) {
		if(isset($authors['person']['potentialAction']))
			continue;
                $array_authors = explode(',', $authors["person"]["name"]);
                $array_citation["author"][$i]["family"] = $array_authors[0];
                if (!empty($array_authors[1])) {
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
        if ($citacao["base"][0] == "Teses e dissertações") {
            $citacao["publisher"]["organization"]["name"] = "Universidade de São Paulo";
        }

        if (!empty($citacao["publisher"]["organization"]["name"])) {
            $array_citation["publisher"] = $citacao["publisher"]["organization"]["name"];
	} elseif (!empty($citacao["unidadeUSP"])){
	    $array_citation["publisher"] = $unidades[$citacao["unidadeUSP"][0]]." &#8211; Universidade de São Paulo";
	    }
        if (!empty($citacao["publisher"]["organization"]["location"])) {
            $array_citation["publisher-place"] = $citacao["publisher"]["organization"]["location"];
        }
        if (!empty($citacao["datePublished"])) {
            $array_citation["issued"]["date-parts"][0][] = intval($citacao["datePublished"]);
        }

        if (!empty($citacao["isPartOf"]["USP"]["dados_do_periodico"])) {
            $periodicos_array = explode(",", $citacao["isPartOf"]["USP"]["dados_do_periodico"]);
            foreach ($periodicos_array as $periodicos_array_new) {
                if (strpos($periodicos_array_new, 'v.') !== false) {
                    $array_citation["volume"] = str_replace("v.", "", $periodicos_array_new);
                } elseif (strpos($periodicos_array_new, 'n.') !== false) {
                    $array_citation["issue"] = str_replace("n.", "", $periodicos_array_new);
                } elseif (strpos($periodicos_array_new, 'p.') !== false) {
                    $array_citation["page"] = str_replace("p.", "", $periodicos_array_new);
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
    static function pagination($page, $total, $limit, $t)
    {
	$total_pages = ceil($total/$limit);
	if ($total_pages >= $page) {
		$mod_result = $total % $limit;
		$start_limit = $page * $limit - $limit + 1;
		if($mod_result > 0 && $page == $total_pages){
			$end_limit = $page * $limit - $limit + $mod_result;
		} else {
			$end_limit = $page * $limit;
		}
	}
        //echo '<div class="uk-child-width-expand@s uk-grid-divider" uk-grid>';
        echo '<div class="uk-grid-divider uk-child-width-expand@s menu" uk-grid>';
        /*echo '<div>';
        echo '<ul class="uk-pagination uk-flex-center">';
        if ($page == 1) {
            echo '<li><a href="#"><span class="uk-margin-small-right" uk-pagination-previous></span> '.$t->gettext('Anterior').'</a></li>';
        } else {
            $_GET["page"] = $page-1 ;
            echo '<li><a href="http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'"><span class="uk-margin-small-right" uk-pagination-previous></span> '.$t->gettext('Anterior').'</a></li>';
        }
        echo '</ul>';
	echo '</div>';*/
	echo '<div class="uk-width-auto@m">';
        if (isset($_GET["sort"])) {
            echo '<a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.str_replace('&sort='.$_GET["sort"].'', "", $_SERVER['QUERY_STRING']).'">'.$t->gettext('Ordenar por Data').'</a>';
        } else {
            echo '<a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&sort=name.keyword">'.$t->gettext('Ordenar por Título').'</a>';
        }
	echo '</div>';
	
	/* Adiciona paginador numérico com 5 páginas antes e 5 páginas depois da página atual */
	echo '<div class="uk-width-expand@m">';
	echo '<ul class="uk-pagination uk-flex-center">';
	if ($total_pages > 1 && $page <= $total_pages) {
		$back_lvl = 0;
		if($page > 1 && $page < 4){
			$back_lvl = $page - 1;
		} else if ($page > $back_lvl){
			$back_lvl = 3;
		}
		if($page > 1){
			$_GET["page"] = $page-1 ;
			echo '<li><a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'"><span class="uk-margin-small-right" uk-pagination-previous></span></a></li>';
		}
		if($page > 4){
			$_GET["page"] = 1 ;
			echo '<li><a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'">1</a></li>';
			echo '<li class="uk-disabled"><span>...</span></li>';
		}
		for($i=$back_lvl; $i > 0; $i--){
			if(($page - $i) > 0){
				$_GET["page"] =  $page - $i;
			} else {
				break;
			}
			echo '<li><a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'">'.$t->gettext(number_format($page - $i, 0, ',', '.')).'</a></li>';
        	}
		echo '<li class="uk-active">'.$t->gettext($page).'</li>';
		$next_lvl = 3;
		if (($total_pages - $page) >= 3 ){
			$next_lvl = 3;
		} else if(($total_pages - $page) < 3 && ($total_pages - $page) >= 1){
			$next_lvl = $total_pages - $page;
		} else {
			$next_lvl = 0;
		}
		for($i=1; $i <= $next_lvl; $i++){
			$_GET["page"] =  $page + $i;
			 echo '<li><a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'">'.$t->gettext(number_format($page + $i, 0, ',', '.')).'</a></li>';
		}
		if($next_lvl > 0){
			$_GET["page"] = $page+1;
			echo '<li><a href="//'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'"><span class="uk-margin-small-left" uk-pagination-next></span></a></li>';
		}
	} else {
		echo '<li class="uk-active">'.$t->gettext($page).'</li>';
	}
	echo '</ul>';
	echo '</div>';
	/* Fim do paginador numérico */
	echo '<div class="uk-width-auto@m">';
	echo '<p class="uk-text-center">'. number_format($start_limit, 0, ',', '.') . " - " . number_format($end_limit, 0, ',', '.') . ' (' . number_format($total, 0, ',', '.') .'&nbsp;'. $t->gettext('registros').')</p>';
	echo '</div>';

        /*echo '<div>';
        echo '<ul class="uk-pagination uk-flex-center">';
	if ($total/$limit > $page) {
            $_GET["page"] = $page+1;
            echo '<li class="uk-margin-auto-left"><a href="http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'">'.$t->gettext('Próxima').' <span class="uk-margin-small-left" uk-pagination-next></span></a></li>';
        } else {
            echo '<li class="uk-margin-auto-left"><a href="#">'.$t->gettext('Próxima').' <span class="uk-margin-small-left" uk-pagination-next></span></a></li>';
        }
        echo '</ul>';
	echo '</div>';*/
        echo '</div>';

    }
}

class metrics
{

    static function get_oadoi($doi)
    {
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
        $data = json_decode($resp, true);
        // Close request to clear up some resources
        curl_close($curl);
        return $data;
    }

    static function get_aminer($title)
    {
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
        $data = json_decode($resp, true);
        // Close request to clear up some resources
        curl_close($curl);
        return $data;
    }

    static function get_opencitation_doi($doi)
    {
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

    static function get_wikipedia($url)
    {
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
        $data = json_decode($resp, true);
        // Close request to clear up some resources
        curl_close($curl);
        return $data;
    }

}

class authorities {
    static function tematres($term, $tematres_url)
    {
        // Clean term
        $term = preg_replace("/\s+/", " ", $term);
        $clean_term = str_replace(array("\r\n", "\n", "\r"), "", $term);
        $clean_term = preg_replace('/^\s+|\s+$/', '', $clean_term);
        $clean_term = str_replace("\t\n\r\0\x0B\xc2\xa0", " ", $clean_term);
        $clean_term = trim($clean_term, " \t\n\r\0\x0B\xc2\xa0");
        $clean_term = rawurlencode($clean_term);
        $clean_term_p = $term;
        $clean_term = str_replace("%C2%A0", "%20", $clean_term);
        $clean_term = str_replace("&", "e", $clean_term);

        // Query tematres
        $ch = curl_init();
        $method = "GET";
        $url = ''.$tematres_url.'?task=fetch&arg='.$clean_term.'&output=json';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $result_get_id_tematres = curl_exec($ch);
        $resultado_get_id_tematres = json_decode($result_get_id_tematres, true);
        curl_close($ch);

        // Get correct term
        if ($resultado_get_id_tematres["resume"]["cant_result"] != 0) {
            foreach ($resultado_get_id_tematres["result"] as $key => $val) {
                $term_key = $key;
            }
            $ch = curl_init();
            $method = "GET";
            $url = ''.$tematres_url.'?task=fetchTerm&arg='.$term_key.'&output=json';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            $result_term = curl_exec($ch);
            $resultado_term = json_decode($result_term, true);
            $found_term = $resultado_term["result"]["term"]["string"];
            $term_not_found = "";
            curl_close($ch);

            $ch_country = curl_init();
            $method = "GET";
            $url_country = ''.$tematres_url.'?task=fetchUp&arg='.$term_key.'&output=json';
            curl_setopt($ch_country, CURLOPT_URL, $url_country);
            curl_setopt($ch_country, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch_country, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            $result_country = curl_exec($ch_country);
            $resultado_country = json_decode($result_country, true);
            foreach ($resultado_country["result"] as $country_list) {
                if ($country_list["order"] == 1) {
                    $country = $country_list["string"];
                }
            }
            curl_close($ch_country);

        } else {
            $term_not_found = $clean_term_p;
            $found_term = "";
            $country = "ND";
        }
        return compact('found_term', 'term_not_found', 'country');
    }
}

/**
 * ElasticPatch
 *
 *@category Class
 *@package ElasticPatch
 */
class ElasticPatch
{
    static function syncElastic($sysno)
    {
	global $pythonBdpiApi;
	$url = "$pythonBdpiApi/item/$sysno/";
	$headers = array('Content-Type: application/json');
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec($curl);
	curl_close($curl);
    }

    static function doEmbargo($objectID, $policyID, $releaseDate)
    {
        global $pythonBdpiApi;
	$url = "$pythonBdpiApi/embargo/$objectID/";
	$headers = array('Content-Type: application/json');
	$data = json_encode(array("dspace_object"=>$objectID,"policy_id"=>$policyID,"release_date"=>$releaseDate));
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$response = curl_exec($curl);
	curl_close($curl);
    }

    static function doPublic($objectID, $policyID)
    {
        global $pythonBdpiApi;
        $url = "$pythonBdpiApi/public/$objectID/";
        $headers = array('Content-Type: application/json');
        $data = json_encode(array("dspace_object"=>$objectID,"policy_id"=>$policyID));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        curl_close($curl);
    }

    static function doPrivate($objectID, $policyID)
    {
        global $pythonBdpiApi;
        $url = "$pythonBdpiApi/private/$objectID/";
        $headers = array('Content-Type: application/json');
        $data = json_encode(array("dspace_object"=>$objectID,"policy_id"=>$policyID));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        curl_close($curl);
    }

    static function accountability($objectID, $numusp, $accountabilityType)
    {
	global $pythonBdpiApi;
	$url = "$pythonBdpiApi/bitstream/$objectID/$accountabilityType/";
	$headers = array('Content-Type: application/json');
	$data = json_encode(array("dspace_object"=>$objectID,"numusp"=>$numusp));
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$response = curl_exec($curl);
	curl_close($curl);
    }

    static function cleanMetadata($sysno)
    {
        global $pythonBdpiApi;
        $url = "$pythonBdpiApi/item/$sysno/";
        $headers = array('Content-Type: application/json');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        curl_close($curl);
    }

    static function uploader($objectID)
    {
	self::accountability($objectID, $_SESSION['oauthuserdata']->{'loginUsuario'}, "uploader");
    }

    static function publisher($objectID)
    {
	self::accountability($objectID, $_SESSION['oauthuserdata']->{'loginUsuario'}, "publisher");
    }

    static function deleter($objectID)
    {
	self::accountability($objectID, $_SESSION['oauthuserdata']->{'loginUsuario'}, "deleter");
    }

    static function privater($objectID)
    {
	self::accountability($objectID, $_SESSION['oauthuserdata']->{'loginUsuario'}, "privater");
    }
}

/**
 * DSpaceREST
 *
 * @category Class
 * @package  DSpaceREST
 * @author   Tiago Rodrigo Marçal Murakami <tiago.murakami@dt.sibi.usp.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://github.com/sibiusp/nav_elastic
 */
class DSpaceREST
{
    static function loginREST()
    {

        global $dspaceRest;
        global $dspaceEmail;
        global $dspacePassword;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array('email' => $dspaceEmail,'password' => $dspacePassword))
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $output_parsed = explode(" ", $server_output);

        curl_close($ch);
        return $output_parsed[3];


    }

    static function logoutREST($DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $DSpaceCookies"));
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/logout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
    }

    static function searchItemDSpace($sysno, $DSpaceCookies = null)
    {
        global $dspaceRest;
        $data_string = "{\"key\":\"usp.sysno\", \"value\":\"$sysno\"}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/find-by-metadata-field");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        if (!empty($result)) {
            return $result[0]["uuid"];
        } else {
            return "";
        }
    }

    static function getBitstreamDSpace($itemID, $DSpaceCookies = NULL)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$itemID/bitstreams");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function getBitstreamPolicyDSpace($bitstreamID, $DSpaceCookies = null)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/bitstreams/$bitstreamID/policy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function deleteBitstreamPolicyDSpace($bitstreamID, $policyID, $DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/bitstreams/$bitstreamID/policy/$policyID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: $DSpaceCookies",
            'Content-Type: application/json'
            )
        );
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function addBitstreamPolicyDSpace($bitstreamID, $policyAction, $groupId, $resourceType, $rpType, $DSpaceCookies)
    {
        global $dspaceRest;
        $policyArray["action"] =  $policyAction;
        $policyArray["epersonId"] =  "";
        $policyArray["groupId"] =  $groupId;
        $policyArray["resourceId"] =  $bitstreamID;
        $policyArray["resourceType"] =  $resourceType;
        $policyArray["rpDescription"] =  "";
        $policyArray["rpName"] =  "";
        $policyArray["rpType"] =  $rpType;
        $policyArray["startDate"] =  "";
        $policyArray["endDate"] =  "";
        $data_string = json_encode($policyArray);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/bitstreams/$bitstreamID/policy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function getBitstreamRestrictedDSpace($bitstreamID, $DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/bitstreams/$bitstreamID/retrieve/64171-196117-1-PB.pdf");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function createItemDSpace($dataString,$collection,$DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/collections/$collection/items");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: $DSpaceCookies",
            'Content-Type: application/json'
            )
        );
        $output = curl_exec($ch);
        curl_close($ch);

    }

    static function deleteItemDSpace($uuid, $DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$uuid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
	$result = json_decode($output, true);
        curl_close($ch);
    }

    static function addBitstreamDSpace($uuid, $file, $userBitstream, $DSpaceCookies)
    {
        global $dspaceRest;
        $filename = rawurlencode($file["file"]["name"]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$uuid/bitstreams?name=$filename&description=$userBitstream");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file["file"]["tmp_name"]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: $DSpaceCookies",
            'Content-Type: text/plain',
            'Accept: application/json'
            )
        );
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function deleteBitstreamDSpace($bitstreamId, $DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/bitstreams/$bitstreamId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        return $result;
    }

    static function getMetadata($uuid, $DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$uuid/metadata");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        if (!empty($result)) {
            return $result;
        } else {
            return "";
        }
        curl_close($ch);
    }

    static function updateMetadata($uuid, $DSpaceCookies, $metadata)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/items/$uuid/metadata");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $metadata);
        if (!empty($DSpaceCookies)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Cookie: $DSpaceCookies",
                'Content-Type: application/json'
                )
            );
        }
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return $result;
        curl_close($ch);
    }

    static function refreshMetadata($sysno)
    {
        $return = false;
        $cursor = NULL;
	try{
            try{
                $cursor = elasticsearch::elastic_get($sysno, 'producao', null);
            }catch(Exception $e){
                $cursor = elasticsearch::elastic_get($sysno, 'producao', null, $alternative_index='bdta');
            }
        }catch(Exception $e){
            echo('Error while trying refresh dspace metadata, registry not found in elastic search');
        }
        if (empty($cursor))
            return $return;
        $document = DSpaceREST::buildDC($cursor, $sysno);
        $newMetadata = json_decode($document, true)['metadata'];
        $DSpaceCookies = DSpaceREST::loginREST();
        $uuid = DSpaceREST::searchItemDSpace($sysno, $DSpaceCookies);
	if ($uuid){
            ElasticPatch::cleanMetadata($sysno);
            DSpaceREST::updateMetadata($uuid, $DSpaceCookies, json_encode($newMetadata));
	    $return = true;
        }
        DSpaceREST::logoutREST($DSpaceCookies);
        return $return;
    }

    static function buildDC($cursor,$sysno)
    {
        $arrayDC["type"] = "item";

        /* Title */
        $title["key"] = "dc.title";
        $title["language"] = "pt_BR";
        $title["value"] = $cursor["_source"]["name"];
        $arrayDC["metadata"][] = $title;
        $title = [];

        /* Sysno */
        $sysnoArray["key"] = "usp.sysno";
        $sysnoArray["language"] = "pt_BR";
        $sysnoArray["value"] = $sysno;
        $arrayDC["metadata"][] = $sysnoArray;
        $sysnoArray = [];

        // /* Abstract */
        // if (!empty($marc["record"]["940"]["a"])){
        //     $abstractArray["key"] = "dc.description.abstract";
        //     $abstractArray["language"] = "pt_BR";
        //     $abstractArray["value"] = $marc["record"]["940"]["a"][0];
        //     $arrayDC["metadata"][] = $abstractArray;
        //     $abstractArray = [];
        // } elseif (!empty($marc["record"]["520"]["a"])){
        //     $abstractArray["key"] = "dc.description.abstract";
        //     $abstractArray["language"] = "pt_BR";
        //     $abstractArray["value"] = $marc["record"]["520"]["a"][0];
        //     $arrayDC["metadata"][] = $abstractArray;
        //     $abstractArray = [];
        // }


        /* DateIssued */
        $dateIssuedArray["key"] = "dc.date.issued";
        $dateIssuedArray["language"] = "pt_BR";
        $dateIssuedArray["value"] = $cursor["_source"]["datePublished"];
        $arrayDC["metadata"][] = $dateIssuedArray;
        $dateIssuedArray = [];

        /* DOI */
        if (!empty($cursor["_source"]["doi"])) {
            $DOIArray["key"] = "dc.identifier";
            $DOIArray["language"] = "pt_BR";
            $DOIArray["value"] = $cursor["_source"]["doi"];
            $arrayDC["metadata"][] = $DOIArray;
            $DOIArray = [];
        }

        /* IsPartOf */
        if (!empty($cursor["_source"]["isPartOf"])) {
            $IsPartOfArray["key"] = "dc.relation.ispartof";
            $IsPartOfArray["language"] = "pt_BR";
            $IsPartOfArray["value"] = $cursor["_source"]["isPartOf"]["name"];
            $arrayDC["metadata"][] = $IsPartOfArray;
            $IsPartOfArray = [];
        }

        /* Authors */
        foreach ($cursor["_source"]["author"] as $author) {
            $authorArray["key"] = "dc.contributor.author";
            $authorArray["language"] = "pt_BR";
            $authorArray["value"] = $author["person"]["name"];
            $arrayDC["metadata"][] = $authorArray;
            $authorArray = [];
        }


        /* Unidade USP */
        if (isset($cursor["_source"]["authorUSP"])) {
            foreach ($cursor["_source"]["authorUSP"] as $unidadeUSP) {
                $unidadeUSPArray["key"] = "usp.unidadeUSP";
                $unidadeUSPArray["language"] = "pt_BR";
                $unidadeUSPArray["value"] = $unidadeUSP["unidadeUSP"];
                $arrayDC["metadata"][] = $unidadeUSPArray;
                $unidadeUSPArray = [];

                $authorUSPArray["key"] = "usp.authorUSP.name";
                $authorUSPArray["language"] = "pt_BR";
                $authorUSPArray["value"] = $unidadeUSP["name"];
                $arrayDC["metadata"][] = $authorUSPArray;
                $authorUSPArray = [];
            }
        }

        /* Subject */
        foreach ($cursor["_source"]["about"] as $subject) {
            $subjectArray["key"] = "dc.subject.other";
            $subjectArray["language"] = "pt_BR";
            $subjectArray["value"] = $subject;
            $arrayDC["metadata"][] = $subjectArray;
            $subjectArray = [];
        }

        /* USP Type */
        $USPTypeArray["key"] = "usp.type";
        $USPTypeArray["language"] = "pt_BR";
        $USPTypeArray["value"] = $cursor["_source"]["type"];
        $arrayDC["metadata"][] = $USPTypeArray;
        $USPTypeArray = [];

        $jsonDC = json_encode($arrayDC);
        return $jsonDC;

    }

    static function testREST($DSpaceCookies)
    {
        global $dspaceRest;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $DSpaceCookies"));
        curl_setopt($ch, CURLOPT_URL, "$dspaceRest/rest/status");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        print_r($server_output);
        curl_close($ch);

    }
}

?>
