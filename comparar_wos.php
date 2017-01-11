<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>
        <title>BDPI USP - Comparar registros da Web of Science</title>
        <!-- Save as javascript -->
        <script src="http://cdn.jsdelivr.net/g/filesaver.js"></script>
        <script>
              function SaveAsFile(t,f,m) {
                    try {
                        var b = new Blob([t],{type:m});
                        saveAs(b, f);
                    } catch (e) {
                        window.open("data:"+m+"," + encodeURIComponent(t), '_blank','');
                    }
                }
        </script> 
    </head>
    <body>
        <?php include_once("inc/analyticstracking.php") ?>
        <?php include('inc/navbar.php'); ?>
    <div class="uk-container uk-margin-top">   
                
        <h1><a href="comparar_wos.php">CSV da Web of Science</a></h1>
        <p>Para obter o arquivo, faça uma busca na Web of Science, selecione os registros, clique em 'Salvar em outros formatos de arquivo', selecione 'Autor, Título e Fonte' e em formato de arquivo 'Separado por tabulações (Win, UTF-8)'. Será salvo um arquivo chamado saverecs.txt, que é aceito pelo comparador.</p>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Upload File" />

        </form><br/>

    
    
    
<?php
if (isset($_FILES['file'])) {    
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');

    $record = [];
    $record[] = 'Ano do material pesquisado\\tTipo de material pesquisado\\tTítulo pesquisado\\tDOI pesquisado\\tAutores\\tTipo de material recuperado\\tTítulo recuperado\\tDOI recuperado\\tAutores\\tAno recuperado\\tPontuação\\tID\\tUnidade';
    
    
    while( ($row = fgetcsv($fh, 8192,"\t")) !== FALSE ) {    
         $record[] = compararRegistrosWos($client,"Artigo",$row[44],$row[8],$row[1],$row[54]);
    }
    
    $record_blob = implode("\\n", $record);
     
    echo '<br/><br/><br/>
    <button class="ui blue label" onclick="SaveAsFile(\''.$record_blob.'\',\'comparativo_wos.csv\',\'text/plain;charset=utf-8\')">
        Exportar resultado em CSV
    </button>
    ';
    
}
?>
        <hr>
     <?php include('inc/footer.php'); ?>           
           
        </div>
        <?php include('inc/offcanvas.php'); ?>
    </body>
</html>