<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?> 
        <title>BDPI USP - Comparar registros do Lattes</title>
    </head>
    <body>
        <?php include_once("inc/analyticstracking.php") ?>

        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-margin-top uk-margin-bottom">       
                
            <h1><a href="comparar_lattes.php">XML do Lattes</a></h1>
            <p>Para obter o XML do Lattes, é necessário entrar no Currículo desejado e no topo à direita tem um botão de baixar XML. Depois é necessário dezipar o arquivo. O arquivo aceito é o curriculo.xml. Para resultados mais precisos, favor preencher o Número USP do autor.</p>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ui form">

<div class="field">
    <label>Número USP</label>
    <input name="codpes" placeholder="Número USP" type="text">
</div><br/>  
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Subir arquivo" />
            </form><br/>

<?php
if (isset($_FILES['file'])) { 
    
    if (empty($codpes)){
        $codpes = "";
    }

    $xml = simplexml_load_file(''.$_FILES['file']['tmp_name'].'') or die("Error: Cannot create object");
    
    echo '<br/><br/>';
    echo 'Idenficador Lattes: '.$xml['NUMERO-IDENTIFICADOR'][0].'<br/>';
    echo 'Nome: '.$xml->{'DADOS-GERAIS'}[0]->attributes()->{'NOME-COMPLETO'}.'<br/>';
    echo '<br/><br/><br/>';
    
    
    echo '<table class="uk-table uk-h6">
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
        $doi="";
        compararRegistrosLattes($client,"TRABALHO DE EVENTO",$year,$title,$doi,$author,$codpes);        
    } 
    
    echo '</tbody></table>';

  
    echo '<table class="uk-table uk-h6">
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
        if (!empty($artigo->{'DADOS-BASICOS-DO-ARTIGO'}->attributes()->{'DOI'})){
            $doi = $artigo->{'DADOS-BASICOS-DO-ARTIGO'}->attributes()->{'DOI'};
        } else {
            $doi = "";
        }
                
        compararRegistrosLattes($client,"ARTIGO PUBLICADO",$year,$title,$doi,$author,$codpes);        
    }     
    
    echo '</tbody></table>';
    
    echo '<table class="uk-table uk-h6">
        <thead>
            <tr>
                <th>Tipo de material pesquisado</th>
                <th>Ano do material pesquisado</th>                
                <th>Título pesquisado</th>
                <th>ISBN</th>
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
    
    foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'LIVROS-E-CAPITULOS'}->{'LIVROS-PUBLICADOS-OU-ORGANIZADOS'}->{'LIVRO-PUBLICADO-OU-ORGANIZADO'} as $livro) {
        $title = $livro->{'DADOS-BASICOS-DO-LIVRO'}->attributes()->{'TITULO-DO-LIVRO'};
        $year = $livro->{'DADOS-BASICOS-DO-LIVRO'}->attributes()->{'ANO'};
        $author = $livro->{'AUTORES'}->attributes()->{'NOME-COMPLETO-DO-AUTOR'};
        $isbn = $livro->{'DETALHAMENTO-DO-LIVRO'}->attributes()->{'ISBN'};        
        compararRegistrosLattes($client,"MONOGRAFIA/LIVRO",$year,$title,$isbn,$author,$codpes);        
    }     
    
     echo '</tbody></table>';    
    
    echo '<table class="uk-table uk-h6">
        <thead>
            <tr>
                <th>Tipo de material pesquisado</th>
                <th>Ano do material pesquisado</th>                
                <th>Título pesquisado</th>
                <th>ISBN</th>
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
    
    foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'LIVROS-E-CAPITULOS'}->{'CAPITULOS-DE-LIVROS-PUBLICADOS'}->{'CAPITULO-DE-LIVRO-PUBLICADO'} as $capitulo) {
        $title = $capitulo->{'DADOS-BASICOS-DO-CAPITULO'}->attributes()->{'TITULO-DO-CAPITULO-DO-LIVRO'};
        $year = $capitulo->{'DADOS-BASICOS-DO-CAPITULO'}->attributes()->{'ANO'};
        $author = $capitulo->{'AUTORES'}->attributes()->{'NOME-COMPLETO-DO-AUTOR'};
        $isbn = $capitulo->{'DETALHAMENTO-DO-CAPITULO'}->attributes()->{'ISBN'};        
        compararRegistrosLattes($client,"PARTE DE MONOGRAFIA/LIVRO",$year,$title,$isbn,$author,$codpes);        
    }     
    
     echo '</tbody></table>';      

    if (!empty($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'TEXTOS-EM-JORNAIS-OU-REVISTAS'}->{'TEXTO-EM-JORNAL-OU-REVISTA'})){
        echo '<table class="uk-table uk-h6">
            <thead>
                <tr>
                    <th>Tipo de material pesquisado</th>
                    <th>Ano do material pesquisado</th>                
                    <th>Título pesquisado</th>
                    <th>ISBN</th>
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

        foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'TEXTOS-EM-JORNAIS-OU-REVISTAS'}->{'TEXTO-EM-JORNAL-OU-REVISTA'} as $jornal) {
            $title = $jornal->{'DADOS-BASICOS-DO-TEXTO'}->attributes()->{'TITULO-DO-TEXTO'};
            $year = $jornal->{'DADOS-BASICOS-DO-TEXTO'}->attributes()->{'ANO-DO-TEXTO'};
            $author = $jornal->{'AUTORES'}->attributes()->{'NOME-COMPLETO-DO-AUTOR'};
            $isbn = "";        
            compararRegistrosLattes($client,"ARTIGO DE JORNAL",$year,$title,$isbn,$author,$codpes);        
        }     

         echo '</tbody></table>';          
    }
    
    
    
    
    
}
?>
                
<hr>
          <?php include('inc/footer.php'); ?>                        
        </div>
        <?php include('inc/offcanvas.php'); ?>
    </body>
</html>