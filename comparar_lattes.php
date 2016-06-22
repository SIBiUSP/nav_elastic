<!DOCTYPE html>
<?php include('inc/functions.php'); ?>        
<html>
    <head>
        <title>Comparar registros do Lattes</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <!-- < ?php include('inc/barrausp.php'); ?> -->
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">
                
                <h1>XML do Lattes</h1>
                
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" name="btn_submit" value="Upload File" />

<?php
if (isset($_FILES['file'])) {    

    $xml = simplexml_load_file(''.$_FILES['file']['tmp_name'].'') or die("Error: Cannot create object");
    
    echo '<br/><br/>';
    echo 'Idenficador Lattes: '.$xml['NUMERO-IDENTIFICADOR'][0].'<br/>';
    echo 'Nome: '.$xml->{'DADOS-GERAIS'}[0]->attributes()->{'NOME-COMPLETO'}.'<br/>';
    echo '<br/><br/><br/>';
    
    
    
    foreach ($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'TRABALHOS-EM-EVENTOS'}->{'TRABALHO-EM-EVENTOS'} as $trab_evento) {
        print_r($trab_evento);
        echo '<br/><br/>';
    } 
    
    print_r($xml->{'PRODUCAO-BIBLIOGRAFICA'}->{'TRABALHOS-EM-EVENTOS'}[0]);
    
    echo '<br/><br/><br/>';
    print_r($xml);
    
    
    
    
}
?>
                

                
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>