<?php
    include('inc/config.php'); 

    $cursor = elasticsearch::elastic_get($_GET['_id'],$type,null);

    ob_start();
    $mpdf = new mPDF();
    
    $mpdf->SetImportUse();

    $pagecount = $mpdf->SetSourceFile($_GET['file']);   

    $mpdf->AddPage();
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if(finfo_file($finfo, $_GET['file']) === 'application/pdf') {
        
        $mpdf->Image('inc/images/USP.jpg',15,20,50,0,'JPG');
        $mpdf->Image('inc/images/Logo_SIBi.png',150,13,44,0,'PNG');
        $mpdf->WriteHTML('<h4>Universidade de São Paulo</h4>');
        $mpdf->WriteHTML('<h4>Biblioteca Digital da Produção Intelectual - BDPI</h4>');
        $mpdf->WriteHTML('<hr/>');
        $mpdf->WriteHTML('<p>'.$cursor["_source"]["type"].'</p>');      
        $mpdf->WriteHTML('<h1>'.$cursor["_source"]["name"].'</h1>');
    
        if (!empty($cursor["_source"]['datePublished'])) { 
            $mpdf->WriteHTML('Ano de publicação: '.$cursor["_source"]['datePublished'].'</h1>');
        }
    
        if (!empty($cursor["_source"]['author'])) {
            $mpdf->WriteHTML('<p>Autores:</p>');
            foreach ($cursor["_source"]['author'] as $authors) { 
                $mpdf->WriteHTML('<p>'.$authors["person"]["name"].'</p>');
            }
        }    
        
        if (!empty($cursor["_source"]['isPartOf'])) {
            $mpdf->WriteHTML('<p>Informações sobre a publicação original:</p>');
            $mpdf->WriteHTML('<p>Título da publicação: '.$cursor["_source"]["isPartOf"]["name"].'</p>');
            if (!empty($cursor["_source"]['isPartOf']['issn'][0])) {
                $mpdf->WriteHTML('<p>ISSN: '.$cursor['_source']['isPartOf']['issn'][0].'</p>');
            }
            if (!empty($cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"])) {
                $mpdf->WriteHTML('<p>Volume/Número/Paginação/Ano: '.$cursor["_source"]["isPartOf"]["USP"]["dados_do_periodico"].'</p>');
            }
            if (!empty($cursor["_source"]["url"])) {
                foreach ($cursor["_source"]['url'] as $url) {
                    $mpdf->WriteHTML('<p>URL: '.$url.'</p>');
                }            
            }        
        }
    
        if (!empty($cursor["_source"]["doi"])) {
            $mpdf->WriteHTML('<p>DOI: <a href="https://doi.org/'.$cursor["_source"]["doi"].'">'.$cursor["_source"]["doi"].'</a></p>');
        }
    
        $mpdf->WriteHTML('<h3>Download em: <a href="//bdpi.usp.br/single.php?_id='.$_GET['_id'].'">//bdpi.usp.br/single.php?_id='.$_GET['_id'].'</a></h3>');
    
    
        // Import the last page of the source PDF file
    
        // iterate through all pages
        for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {
            // import a page
            $templateId = $mpdf->importPage($pageNo);
            // get the size of the imported page
            $size = $mpdf->getTemplateSize($templateId);
    
            // create a page (landscape or portrait depending on the imported page size)
            if ($size['w'] > $size['h']) {
                $mpdf->AddPage('L', array($size['w'], $size['h']));
            } else {
                $mpdf->AddPage('P', array($size['w'], $size['h']));
            }        
            // use the imported page
            $mpdf->useTemplate($templateId);
        }       

    } else {
        $mpdf->WriteHTML(''.$_GET["file"].' not a PDF');
    }  

    //$mpdf->Output();

    // or Output the file as forced download
    $mpdf->Output("".$_GET['_id'].".pdf", 'I');

?>
