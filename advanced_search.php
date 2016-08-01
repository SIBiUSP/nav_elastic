<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?> 
        <title>BDPI USP - Busca Avançada</title>
    </head>
    <body>

        <?php include('inc/navbar.php'); ?>
        <div class="uk-container uk-container-center uk-margin-top uk-margin-bottom"> 
            <h1>Em breve...</h1>
<div class="ui vertical stripe segment" id="search">
<h3 class="ui header" >Buscar</h3>
<form class="ui form" role="form" action="result.php" method="get">
<div class="inline fields">
<div class="ui form">
<div class="field">
<label>Número USP</label>
<input type="text" name="codpesbusca[]">
</div>
</div>
<button type="submit" id="s" class="ui large button">Buscar</button>
</div>
</form>
</div>
<div class="ui vertical stripe segment" id="search">
<h3 class="ui header" >Assunto</h3>
<a href="#" onclick="creaPopup('inc/popterms/index.php?t=subject&f=assunto&v=http://143.107.154.55/pt-br/services.php&loadConfig=1'); return false;">
Consultar o VCUSP
</a>
<form class="ui form" role="form" action="result.php" method="get" name="assunto">
<div class="inline fields">
<div class="ui form">
<div class="field">
<label>Assunto</label>
<input type="text" name="subject[]">
</div>
</div>
<button type="submit" id="s" class="ui large button">Buscar</button>
</div>
</form>
</div>
<hr>
        <?php include('inc/footer.php'); ?>               
            </div>            
        <?php include('inc/offcanvas.php'); ?>


 
    </body>
</html>