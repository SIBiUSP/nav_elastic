<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php include('inc/functions.php'); ?> 
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Comparar registros do Lattes</title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <!-- <link rel="stylesheet" href="inc/uikit/css/uikit.min.css"> -->
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>        
        <script src="inc/uikit/js/uikit.min.js"></script>
        <script src="inc/uikit/js/components/grid.js"></script>
    </head>
    <body>

        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-container-center uk-margin-top uk-margin-bottom">       
                
                <h1>XML do Lattes</h1>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ui form">

<div class="field">
    <label>Número USP</label>
    <input name="codpes" placeholder="Número USP" type="text">
</div>  
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Subir arquivo" />
            </form>

<?php
if (isset($_FILES['file'])) {    

    $xml = simplexml_load_file(''.$_FILES['file']['tmp_name'].'') or die("Error: Cannot create object");
    
    echo '<br/><br/>';
    echo 'Idenficador Lattes: '.$xml['NUMERO-IDENTIFICADOR'][0].'<br/>';
    echo 'Nome: '.$xml->{'DADOS-GERAIS'}[0]->attributes()->{'NOME-COMPLETO'}.'<br/>';
    echo '<br/><br/><br/>';
    
    
    echo '<table class="ui celled table">
        <thead>
            <tr>
                <th>Tipo de material pesquisado</th>
                <th>Ano do material pesquisado</th>                
                <th>Título pesquisado</th>
                <th>DOI</th>
                <th>Autores</th>
                <th>Tipo de material recuperado</th>
                <th>Título recuperado</th>
                <th>DOI recuperado</th>
                <th>Autores</th>
                <th>Ano recuperado</th>
                <th>Pontuação</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
     ';
    
    foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'TRABALHOS-EM-EVENTOS'}->{'TRABALHO-EM-EVENTOS'} as $trab_evento) {
        $title = $trab_evento->{'DADOS-BASICOS-DO-TRABALHO'}->attributes()->{'TITULO-DO-TRABALHO'};
        $year = $trab_evento->{'DADOS-BASICOS-DO-TRABALHO'}->attributes()->{'ANO-DO-TRABALHO'};
        $author = $xml->{'DADOS-GERAIS'}[0]->attributes()->{'NOME-COMPLETO'};
        compararRegistrosLattes("TRABALHO EM EVENTOS",$year,$title,$doi,$author,$codpes);        
    } 
    
    echo '</tbody></table>';

  
    echo '<table class="ui celled table">
        <thead>
            <tr>
                <th>Tipo de material pesquisado</th>
                <th>Ano do material pesquisado</th>                
                <th>Título pesquisado</th>
                <th>DOI</th>
                <th>Autores</th>
                <th>Tipo de material recuperado</th>
                <th>Título recuperado</th>
                <th>DOI recuperado</th>
                <th>Autores</th>
                <th>Ano recuperado</th>
                <th>Pontuação</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
     ';    
    
    foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'ARTIGOS-PUBLICADOS'}->{'ARTIGO-PUBLICADO'} as $artigo) {
        $title = $artigo->{'DADOS-BASICOS-DO-ARTIGO'}->attributes()->{'TITULO-DO-ARTIGO'};
        $year = $artigo->{'DADOS-BASICOS-DO-ARTIGO'}->attributes()->{'ANO-DO-ARTIGO'};
        $author = $xml->{'DADOS-GERAIS'}[0]->attributes()->{'NOME-COMPLETO'};
        $doi = $artigo->{'DADOS-BASICOS-DO-ARTIGO'}->attributes()->{'DOI'};        
        compararRegistrosLattes("ARTIGO PUBLICADO",$year,$title,$doi,$author,$codpes);        
    }     
    
     echo '</tbody></table>';
    
//    print_r($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'ARTIGOS-PUBLICADOS'}[0]);
    
//    echo '<br/><br/><br/>';
//    print_r($xml);
    
    
    
    
}
?>
                
<hr>
          <?php include('inc/footer.php'); ?>                        
        </div>
        <?php include('inc/offcanvas.php'); ?>
    </body>
</html>