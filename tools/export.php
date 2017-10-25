<?php

if ($_GET["format"] == "table") {

    $file="export_bdpi.tsv";
    header('Content-type: text/tab-separated-values');
    header("Content-Disposition: attachment; filename=$file");

    // Set directory to ROOT
    chdir('../');
    // Include essencial files
    include('inc/config.php'); 
    include('inc/functions.php');

    if (!empty($_GET)) {
        $result_get = get::analisa_get($_GET);
        $query = $result_get['query'];  
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 10000;
        $params["from"] = $skip;
        $params["body"] = $query; 

        $cursor = $client->search($params);
        $total = $cursor["hits"]["total"];

        $content[] = "Sysno\tDOI\tTítulo\tAutores\tFonte da publicação\tPaginação\tAno de publicação\tISSN\tLocal de publicação\tEditora\tNome do evento\tTipo de Material\tAutores USP\tNúmero USP\tUnidades USP\tDepartamentos\tQualis 2013/2016\tJCR - Journal Impact Factor - 2016\tCitescore - 2016";
        
        foreach ($cursor["hits"]["hits"] as $r){

            $fields[] = $r['_id'];
        
            if (!empty($r["_source"]['doi'])) {
                $fields[] = $r["_source"]['doi'];
            } else {
                $fields[] = "";
            }
        
            $fields[] = $r["_source"]['name'];
            

            foreach ($r["_source"]['author'] as $authors) {
                $authors_array[]= $authors["person"]["name"];                
            }
            $fields[] = implode(";",$authors_array);
            unset($authors_array);

            if (!empty($r["_source"]['isPartOf']["name"])) {
                $fields[] = $r["_source"]['isPartOf']["name"];
            } else {
                $fields[] = "";
            } 

            if (!empty($r["_source"]['isPartOf']['USP']['dados_do_periodico'])) {
                $fields[] = $r["_source"]['isPartOf']['USP']['dados_do_periodico'];
            } else {
                $fields[] = "";
            } 

            if (!empty($r["_source"]['datePublished'])) {
                $fields[] = $r["_source"]['datePublished'];
            } else {
                $fields[] = "";
            } 
            
            if (!empty($r["_source"]['isPartOf']['issn'])) {
                foreach ($r["_source"]['isPartOf']['issn'] as $issn) {
                    $issn_array[]= $issn;                
                }
                $fields[] = implode(";",$issn_array);
                unset($issn_array);
            } else {
                $fields[] = "";
            } 
            
            if (!empty($r["_source"]['publisher']['organization']['location'])) {
                $fields[] = $r["_source"]['publisher']['organization']['location'];
            } else {
                $fields[] = "";
            }
            
            if (!empty($r["_source"]['publisher']['organization']['name'])) {
                $fields[] = $r["_source"]['publisher']['organization']['name'];
            } else {
                $fields[] = "";
            } 
            
            if (!empty($r["_source"]['releasedEvent'])) {
                $fields[] = $r["_source"]['releasedEvent'];
            } else {
                $fields[] = "";
            }  
            
            if (!empty($r["_source"]['type'])) {
                $fields[] = $r["_source"]['type'];
            } else {
                $fields[] = "";
            } 
            
            if (!empty($r["_source"]['authorUSP'])) {

                foreach ($r["_source"]['authorUSP'] as $authorsUSP) {
                    $authorsUSP_array[]= $authorsUSP["name"];                
                }
                $fields[] = implode(";",$authorsUSP_array);
                unset($authorsUSP_array);

                foreach ($r["_source"]['authorUSP'] as $numUSP) {
                    if (!empty($numUSP["codpes"])) {
                        $numUSP_array[]= $numUSP["codpes"]; 
                    }               
                }
                if (!empty($numUSP_array)) {
                    $fields[] = implode(";",$numUSP_array);
                    unset($numUSP_array);
                }

                foreach ($r["_source"]['authorUSP'] as $unidadesUSP_aut) {
                    $unidadesUSP_array[]= $unidadesUSP_aut["unidadeUSP"];                
                }
                $fields[] = implode(";",$unidadesUSP_array);
                unset($unidadesUSP_array);

                foreach ($r["_source"]['authorUSP'] as $departament_aut) {
                    if (!empty($departament_aut["departament"])) {
                        $departament_array[]= $departament_aut["departament"];
                    }                
                }
                if (!empty($departament_array)) {
                    $fields[] = implode(";",$departament_array);
                    unset($departament_array);
                }

            }
            
            if (!empty($r["_source"]['USP']['serial_metrics']['qualis']['2016'])) {
                foreach ($r["_source"]['USP']['serial_metrics']['qualis']['2016'] as $qualis) {
                    $qualis_array[]= $qualis["area_nota"];                
                }
                $fields[] = implode(";",$qualis_array);
                unset($qualis_array);
            } 
            
            if (!empty($r["_source"]['USP']['JCR']['JCR']['2016'][0]['Journal_Impact_Factor'])) {
                $fields[] = $r["_source"]['USP']['JCR']['JCR']['2016'][0]['Journal_Impact_Factor'];
            } else {
                $fields[] = "";
            } 
            
            if (!empty($r["_source"]['USP']['citescore']['citescore']['2016'][0]['citescore'])) {
                $fields[] = $r["_source"]['USP']['citescore']['citescore']['2016'][0]['citescore'];
            } else {
                $fields[] = "";
            }              

            
            $content[] = implode("\t",$fields);
            unset($fields);

        
        }
        echo implode("\n",$content);            

    }    

} elseif($_GET["format"] == "ris") {

    $file="export_bdpi.ris";
    header('Content-type: application/x-research-info-systems');
    header("Content-Disposition: attachment; filename=$file");

    // Set directory to ROOT
    chdir('../');
    // Include essencial files
    include('inc/config.php'); 
    include('inc/functions.php');


    $result_get = get::analisa_get($_GET);
    $query = $result_get['query'];  
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $skip = $result_get['skip'];

    if (isset($_GET["sort"])) {
        $query['sort'] = [
            ['name.keyword' => ['order' => 'asc']],
        ];
    } else {
        $query['sort'] = [
            ['datePublished.keyword' => ['order' => 'desc']],
        ];
    }

    $params = [];
    $params["index"] = $index;
    $params["type"] = $type;
    $params["size"] = 10000;
    $params["from"] = $skip;
    $params["body"] = $query; 

    $cursor = $client->search($params); 

    foreach ($cursor["hits"]["hits"] as $r) { 
        /* Exportador RIS */
        $record_blob[] = exporters::RIS($r);
    }
    foreach ($record_blob as $record) {
        $record_array = explode('\n',$record);
        echo implode("\n",$record_array);
    }
    


}




?>