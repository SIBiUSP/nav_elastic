<!DOCTYPE html>
<html>
    <head>
        <title>Entre em contato</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">

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

               
            </div>            
        </div>
        <?php include('inc/footer.php'); ?>
<script>
$('.activating.element')
  .popup()
;
</script>
<script>
$(document).ready(function()
{
  $('div#logosusp').attr("style", "z-index:0;");
});
</script>
<!-- ###### Script para criar o pop-up do popterms ###### -->
<script>
    function creaPopup(url)
    {
      tesauro=window.open(url,
      "Tesauro",
      "directories=no, menubar =no,status=no,toolbar=no,location=no,scrollbars=yes,fullscreen=no,height=600,width=450,left=500,top=0"
      )
    }
 </script>        
    </body>
</html>