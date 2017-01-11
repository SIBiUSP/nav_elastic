<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?> 
        <title>BDPI USP - Comparar registros da WeRUSP</title>
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
                
        <h1>CSV do weRUSP</h1>
                
        <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" name="btn_submit" value="Upload File" />
        </form>
        <?php
            if (isset($_FILES['file'])) {    
                $fh = fopen($_FILES['file']['tmp_name'], 'r+');


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


                while( ($row = fgetcsv($fh, 8192,";")) !== FALSE ) {    
                    compararRegistros($server,$row[1],$row[0],$row[8],$row[9]);
                }

                echo '</tbody></table>';
            }
        ?>    

        <hr>        
        <?php include('inc/footer.php'); ?>       
        </div>
        <?php include('inc/offcanvas.php'); ?>
    </body>
</html>