<?php

// Set directory to ROOT
chdir('../');         
require 'inc/config.php'; 
require 'inc/functions.php';            
require 'inc/meta-header.php';

/* Consulta n registros ainda não corrigidos */
if (empty($_GET)) {
    //$body["query"]["bool"]["must"]["query_string"]["query"] = "+_exists_:USP.crossref.message.reference";
    $body["query"]["bool"]["must"]["query_string"]["query"] = "+_exists_:USP.crossref.message.reference -_exists_:USP.crossref.checkReferences";
} 

if (isset($_GET["sort"])) {        
    $body["sort"][$_GET["sort"]]["unmapped_type"] = "long";
    $body["sort"][$_GET["sort"]]["missing"] = "_last";
    $body["sort"][$_GET["sort"]]["order"] = "desc";
    $body["sort"][$_GET["sort"]]["mode"] = "max";
} else {
    $body['sort']['_uid']['order'] = "desc";
}

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["_source"] = ["_id","USP.crossref.message.reference"];
$params["size"] = 100;        
$params["body"] = $body;   

$response = $client->search($params);
            
echo 'Total de registros faltantes: '.$response['hits']['total'].'<br/><br/>';

foreach ($response["hits"]["hits"] as $r) {
    
    $i = 0;
    foreach ($r["_source"]["USP"]["crossref"]["message"]["reference"] as $ref) {
        if (isset($ref["unstructured"])) {
            $xmlGrobid = processReferenceGrobid($ref["unstructured"]);
            if (!empty($xmlGrobid)) {
                if (isset($xmlGrobid->{'analytic'})) {
                    if (isset($xmlGrobid->{'analytic'}->{'title'})) {
                        $ref["original"]["title"] = $ref["title"];
                        $ref["title"] = (string)$xmlGrobid->{'analytic'}->{'title'};
                    }
                    if (isset($xmlGrobid->{'analytic'}->{'idno'})) {
                        if ((string)$xmlGrobid->{'analytic'}->{'idno'} !== $ref["DOI"]) {
                            $ref["original"]["DOI"] = $ref["DOI"];
                            $ref["DOI"] = (string)$xmlGrobid->{'analytic'}->{'idno'};
                        } 
                    }                      
                    if (isset($xmlGrobid->{'monogr'}->{'title'})) {
                        if ((string)$xmlGrobid->{'monogr'}->{'title'} !== $ref["journal-title"]) {
                            $ref["original"]["journal-title"] = $ref["journal-title"];
                            $ref["journal-title"] = (string)$xmlGrobid->{'monogr'}->{'title'};
                        }               

                    }                    
                } elseif (isset($xmlGrobid->{'monogr'})) {
                    if (isset($xmlGrobid->{'monogr'}->{'title'})) {
                        if ((string)$xmlGrobid->{'monogr'}->{'title'} !== $ref["journal-title"]) {
                            $ref["original"]["journal-title"] = $ref["journal-title"];
                            $ref["journal-title"] = (string)$xmlGrobid->{'monogr'}->{'title'};
                        } 
                    }                  
                }
                if (!isset($ref["year"]) AND isset($xmlGrobid->{'monogr'}->{'imprint'}->{'date'}->attributes()->{'when'})) {
                    $ref["year"] = (string)$xmlGrobid->{'monogr'}->{'imprint'}->{'date'}->attributes()->{'when'}[0];
                }
                $body_upsert["doc"]["USP"]["crossref"]["message"][$i]["reference"]["original"] = $ref;
            }            
        }
        if (isset($ref["journal-title"])) {
            $result_tematres = authorities::tematres($ref["journal-title"], $tematres_url);
            if (!empty($result_tematres["found_term"])) {
                $ref["original"]["journal-title"] = $ref["journal-title"];
                $ref["journal-title"] = $result_tematres["found_term"];
                $body_upsert["doc"]["USP"]["crossref"]["message"][$i]["reference"]["original"] = $ref;
            }
        }
        
        if (isset($ref["year"])) {
            preg_match("/\b(\d+)\b/", $ref["year"], $refYearMatch);
            $ref["original"]["year"] = $ref["year"];
            $ref["year"] = $refYearMatch[0];
        }
        $body_upsert["doc"]["USP"]["crossref"]["message"]["reference"][$i] = $ref;
        $i++;

    }

    if (isset($body_upsert)) {
        $body_upsert["doc_as_upsert"] = true;
        $body_upsert["doc"]["USP"]["crossref"]["checkReferences"] = true;
        //print_r($body_upsert);
        $resultado_upsert = elasticsearch::elastic_update($r["_id"], $type, $body_upsert);
        print_r($resultado_upsert);
        unset($body_upsert);            
    }
    
    

}


function processReferenceGrobid($unstructuredReference) 
{
    // // initialise the curl request
    $request = curl_init('143.107.154.38:8070/api/processCitation');

    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt(
        $request,
        CURLOPT_POSTFIELDS, "citations=$unstructuredReference"
    );
    // // output the response
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($request);    
    $xml_grobid = simplexml_load_string($result);
    return $xml_grobid;

    //$xml_grobid->teiHeader->fileDesc->sourceDesc->biblStruct->idno->attributes()->type == "DOI"

}         


?>