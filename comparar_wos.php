<!DOCTYPE html>
<?php include('inc/functions.php'); ?>        
<html>
    <head>
        <title>Comparar registros</title>
        <?php include('inc/meta-header.php'); ?>
        
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
        <!-- < ?php include('inc/barrausp.php'); ?> -->
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <h1>CSV da Web of Science</h1>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Upload File" />



    
    
    
<?php
if (isset($_FILES['file'])) {    
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');

    $record_scopus = [];
    $record_scopus[] = 'Ano do material pesquisado\\tTipo de material pesquisado\\tTítulo pesquisado\\tDOI pesquisado\\tAutores\\tTipo de material recuperado\\tTítulo recuperado\\tDOI recuperado\\tAutores\\tAno recuperado\\tPontuação\\tID\\tUnidade';
    
    
    while( ($row = fgetcsv($fh, 8192,"\t")) !== FALSE ) {    
         $record_scopus[] = compararRegistrosWos("Artigo",$row[44],$row[8],$row[1],$row[54]);
    }
    
    $record_blob = implode("\\n", $record_scopus);
     
    echo '<br/><br/><br/>
    <button class="ui blue label" onclick="SaveAsFile(\''.$record_blob.'\',\'comparativo_wos.csv\',\'text/plain;charset=utf-8\')">
        Exportar resultado em CSV
    </button>
    ';
    
}
?>
                

                
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>