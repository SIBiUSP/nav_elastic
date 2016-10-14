<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?>         
        <title>Comparar registros</title>
        
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
            <div class="uk-container uk-container-center uk-margin-top uk-margin-bottom">  
                
                <h1>CSV do Scopus</h1>
                <p>Separado por tabulações</p>
                
                <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <input type="submit" name="btn_submit" value="Upload File" />
                </form>
                <br/><br/>


    
    
    
<?php
if (isset($_FILES['file'])) {    
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');

    $record_scopus = [];
    $record_scopus[] = 'Ano do material pesquisado\\tTipo de material pesquisado\\tTítulo pesquisado\\tDOI pesquisado\\tAutores\\tTipo de material recuperado\\tTítulo recuperado\\tDOI recuperado\\tAutores\\tAno recuperado\\tPontuação\\tID\\tUnidade';
    
    
    while( ($row = fgetcsv($fh, 8192,",")) !== FALSE ) {    
         $record_scopus[] = compararCSVScopus($server,"Artigo",$row[2],$row[1],$row[0],$row[11]);
    }
    
    $record_blob = implode("\\n", $record_scopus);
     
    echo '<br/><br/><br/>
    <button class="ui blue label" onclick="SaveAsFile(\''.$record_blob.'\',\'comparativo_scopus.csv\',\'text/plain;charset=utf-8\')">
        Exportar resultado em CSV
    </button>
    ';
    
}
?>
                

                
        
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>