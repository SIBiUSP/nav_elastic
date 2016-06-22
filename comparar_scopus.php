<!DOCTYPE html>
<?php include('inc/functions.php'); ?>        
<html>
    <head>
        <title>Comparar registros</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <!-- < ?php include('inc/barrausp.php'); ?> -->
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <h1>CSV do Scopus</h1>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Upload File" />

<?php
if (isset($_FILES['file'])) {    
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');

    
    echo '<table class="ui celled table">
        <thead>
            <tr>
                <th>Ano do material pesquisado</th>
                <th>Tipo de material pesquisado</th>
                <th>Título pesquisado</th>
                <th>Autores</th>
                <th>Tipo de material recuperado</th>
                <th>Título recuperado</th>
                <th>Autores</th>
                <th>Ano recuperado</th>
                <th>Pontuação</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
     ';
    
    
    while( ($row = fgetcsv($fh, 8192,"\t")) !== FALSE ) {    
        compararRegistrosScopus("Artigo",$row[3],$row[0],$row[1],$row[13]);
    }
    
    echo '</tbody></table>';
}
?>
                

                
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>