<?php
include_once(__DIR__."/../inc/config.php");
include_once(__DIR__."/staffAdmins.php");

if (!(isset($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffAdmins ))){
	header ("location: $url_base");
	die('você não está logado ou não é um administrador do sistema');
}

?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
            <?php
            require_once '../inc/meta-header.php';
	            ?>
        <title><?=$branch?></title>
        <!-- Facebook Tags - START -->
        <!--<meta property="og:locale" content="pt_BR">-->
        <meta property="og:locale" content="<?=$locale?>">
        <meta property="og:url" content="<?=$url_base?>">
        <meta property="og:title" content="<?=$t->gettext($branch)?> - <?=$t->gettext('Gestão do staff e das unidades')?>">
        <meta property="og:site_name" content="<?=$t->gettext($branch)?>">
        <meta property="og:description" content="<?=$t->gettext($branch_description)?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
        <meta property="og:type" content="website">
	<!-- Facebook Tags - END -->
    </head>

    <body style="height: auto; /*min-height: 45em;*/ position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>
        <?php require '../inc/navbar.php'; ?>
	<div class="uk-container uk-margin" style="position: relative;">

	    <div class="uk-width-1-1@s uk-child-width-1-1@m uk-align-center" style="position: relative; margin-bottom:300px" uk-grid>
		<div class="uk-align-center">
			<h2 style="color:#1094ab">Gestão do staff e bibliotecas</h2>
			<!--<h2 style="color:#1094ab">Gestão do staff, bibliotecas e unidades</h2>-->
			<a class="uk-button uk-button-primary" href="staffusers.php" uk-toggle>Colaboradores</a>
			<a class="uk-button uk-button-primary" href="bibliotecas.php" uk-toggle>Bibliotecas</a>
			<!--<a class="uk-button uk-button-primary" href="unidades.php" uk-toggle>Unidades</a>-->
		</div>
            </div>

        </div>
	<?php require_once('../inc/footer.php'); ?>
    </body>
</html>
