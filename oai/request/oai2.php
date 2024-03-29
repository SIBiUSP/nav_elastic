<?php
require '../../inc/config.php';
require 'oai2server.php';
require 'functions_oai.php';
/**
 * Identifier settings. It needs to have proper values to reflect the settings of the data provider.
 * Is MUST be declared in this order
 *
 * - $identifyResponse['repositoryName'] : compulsory. A human readable name for the repository;
 * - $identifyResponse['baseURL'] : compulsory. The base URL of the repository;
 * - $identifyResponse['protocolVersion'] : compulsory. The version of the OAI-PMH supported by the repository;
 * - $identifyResponse['earliestDatestamp'] : compulsory. A UTCdatetime that is the guaranteed lower limit of all datestamps recording changes, modifications, or deletions in the repository. A repository must not use datestamps lower than the one specified by the content of the earliestDatestamp element. earliestDatestamp must be expressed at the finest granularity supported by the repository.
 * - $identifyResponse['deletedRecord'] : the manner in which the repository supports the notion of deleted records. Legitimate values are no ; transient ; persistent with meanings defined in the section on deletion.
 * - $identifyResponse['granularity'] : the finest harvesting granularity supported by the repository. The legitimate values are YYYY-MM-DD and YYYY-MM-DDThh:mm:ssZ with meanings as defined in ISO8601.
 *
 */
$identifyResponse = array();
$identifyResponse["repositoryName"] = 'Repositório da Produção USP';
$identifyResponse["baseURL"] = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['SCRIPT_NAME'].'';
$identifyResponse["protocolVersion"] = '2.0';
$identifyResponse['adminEmail'] = 'atendimento@aguia.usp.br';
$identifyResponse["earliestDatestamp"] = '2016-01-01T12:00:00Z';
$identifyResponse["deletedRecord"] = 'no'; // How your repository handles deletions
                                           // no:             The repository does not maintain status about deletions.
                                           //                It MUST NOT reveal a deleted status.
                                           // persistent:    The repository persistently keeps track about deletions
                                           //                with no time limit. It MUST consistently reveal the status
                                           //                of a deleted record over time.
                                           // transient:   The repository does not guarantee that a list of deletions is
                                           //                maintained. It MAY reveal a deleted status for records.
$identifyResponse["granularity"] = 'YYYY-MM-DDThh:mm:ssZ';

/* unit tests ;) */
if (!isset($args)) {
    $args = $_GET;
}
if (!isset($uri)) {
    $uri = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['SCRIPT_NAME'].'';
}

$oai2 = new OAI2Server ($uri, $args, $identifyResponse,
    array(
        'ListMetadataFormats' =>
        function ($identifier = '') {
            if (!empty($identifier) && $identifier != 'a.b.c') {
                throw new OAI2Exception('idDoesNotExist');
            }
            return
                array( 'oai_dc' => array('metadataPrefix'=>'oai_dc',
                                        'schema'=>'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                                        'metadataNamespace'=>'http://www.openarchives.org/OAI/2.0/oai_dc/',
                                        'record_prefix'=>'dc',
                                        'record_namespace' => 'http://purl.org/dc/elements/1.1/'));
        },

        'ListSets' =>
        function ($resumptionToken = '') {
            return
                array (
                    array('setSpec'=>'PI', 'setName'=>'Produção Intelectual'),
                    array('setSpec'=>'TD', 'setName'=>'Teses e Dissertações'),
                    array('setSpec'=>'OA', 'setName'=>'Open Access'),
                    array('setSpec'=>'ECA', 'setName'=>'Escola de Comunicações e Artes'),
                );
        },

        'ListRecords' =>
        function ($metadataPrefix, $from = '', $until = '', $set = '', $scroll_id_token = '', $count = false, $scroll_id_get_token = false) {
            global $client;
            global $index;
            global $type;
            global $url_base;

            if ($metadataPrefix != 'oai_dc') {
                throw new OAI2Exception('noRecordsMatch');
            }

           if (!empty($set)) {
               if ($set == "PI") {
                   $query["query"]["bool"]["filter"]["term"]["base.keyword"] = "Produção científica";
               } elseif ($set == "TD") {
                   $query["query"]["bool"]["filter"]["term"]["base.keyword"] = "Teses e dissertações";
               } elseif ($set == "OA") {
                   $query["query"]["bool"]["should"]["exists"]["field"] = "files.database.file_name";
                   $query["query"]["bool"]["should"]["exists"]["field"] = "USP.unpaywall.best_oa_location.url_for_pdf";
               } else {
                   $query["query"]["bool"]["filter"]["term"]["unidadeUSP.keyword"] = "$set";
               }
            } else {
                $query["query"]["bool"]["must"]["query_string"]["query"] = "*";
            }

            if (!empty($from)||!empty($until)) {
                $filter[]= '{ "range" : { "datestamp" : { "gte" : "'.$from.'", "lt" :  "'.$until.'" } } }';
            }

            if (!empty($filter)) {
                $filter_query = ''.implode(",", $filter).'';
            } else {
                $filter_query = "";
            }

            $query["sort"]["_uid"]["order"] = 'desc';

            $params = [];
            $params["index"] = $index;
            $params["type"] = $type;
            $params["size"] = 50;
            $params["scroll"] = "10m";
            $params["body"] = $query;

            if (empty($scroll_id_token)) {
                $cursor = $client->search($params);
            } else {

                $cursor = $client->scroll(
                    [
                    "scroll" => "10m",
                    "scroll_id" => $scroll_id_token
                    ]
                );
            }

            $response_array = [];
            $response_array["count"] = $cursor["hits"]["total"];
            $response_array["scroll_id_new"] = $cursor['_scroll_id'];

            $records = array();
            $now = date('Y-m-d-H:s');
            $i = 0;
            foreach ($cursor["hits"]["hits"] as $hit) {

		$fields = getFieldsDC($hit);
                $records[$i]["identifier"] = $hit['_id'];
                $records[$i]["datestamp"] = $now;
                if (!empty($set)) {
                    $records[$i]["set"] = "$set";
                } else {
                    $records[$i]["set"] = 'all';
                }
                $records[$i]["metadata"]["container_name"] = 'oai_dc:dc';
                $records[$i]["metadata"]["container_attributes"]["xmlns:oai_dc"] = "http://www.openarchives.org/OAI/2.0/oai_dc/";
                $records[$i]["metadata"]["container_attributes"]["xmlns:dc"] = "http://purl.org/dc/elements/1.1/";
                $records[$i]["metadata"]["container_attributes"]["xmlns:xsi"] = "http://www.w3.org/2001/XMLSchema-instance";
                $records[$i]["metadata"]["container_attributes"]["xsi:schemaLocation"] = 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd';
                if (!empty($fields)) {
                    $records[$i]["metadata"]["fields"] = $fields;
                } else {
                    $records[$i]["metadata"]["fields"]['dc:title'] = "";
                    $records[$i]["metadata"]["fields"]['dc:type'] = "";
                    $records[$i]["metadata"]["fields"]['dc:language'] = "";
                    $records[$i]["metadata"]["fields"]['dc:creator'] = "";
                }
                $i++;
                unset($fields);
            }
            $response_array["records"] = $records;
            return $response_array;

        },

        'GetRecord' =>
        function ($identifier, $metadataPrefix) {
            global $client;
            global $index;
            global $type;

            if ($metadataPrefix != 'oai_dc') {
                throw new OAI2Exception('noRecordsMatch');
            }

            $params = [
                'index' => $index,
                'type' => $type,
                'id' => ''.$identifier.''
            ];
            $record = $client->get($params);
            $fields = getFieldsDC($record);
            if ($record["found"] === false) {
                throw new OAI2Exception('idDoesNotExist');
            }
            $now = date('Y-m-d-H:s');
            return array('identifier' => $record['_id'],
                         'datestamp' => $now,
                         'set' => 'all',
                         'metadata' => array(
                             'container_name' => 'oai_dc:dc',
                             'container_attributes' => array(
                                  'xmlns:oai_dc' => "http://www.openarchives.org/OAI/2.0/oai_dc/",
                                  'xmlns:dc' => "http://purl.org/dc/elements/1.1/",
                                  'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
                                  'xsi:schemaLocation' =>
                                  'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd'
                              ),
                              'fields' => $fields
                          ));
        },
    )
);

$response = $oai2->response();
if (isset($return)) {
    return $response;
} else {
    $response->formatOutput = true;
    $response->preserveWhiteSpace = false;
    header('Content-Type: text/xml');
    echo $response->saveXML();
}
