<?php 
	include_once(__DIR__."/config.php");
	include_once(__DIR__."/../admin/staffAdmins.php");
?>

<div style="height: 0.3em; background: #fcb421;"></div>
<div style="height: 0.4em; background: #64c4d2;"></div>
<div class="uk-card uk-card-default" >
<div class="uk-visible@m">
    <div id="menu" class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click" uk-navbar>
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav uk-link-text">
                <li class="uk-active"><a class="uk-link-heading" href="<?=$url_base?>/index.php"><?=$t->gettext('Início')?></a></li>
                <li class="uk-active">
                    <a href="<?=$url_base?>/sobre.php"><?=$t->gettext('Sobre')?></a>
                </li>
                <li class="uk-active">
                    <a href="#modal-full" uk-toggle><?=$t->gettext('Unidades USP')?></a>

                    <div id="modal-full" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                            <div class="uk-grid-collapse uk-child-width-1-4@s uk-flex-middle" uk-grid>
                                <div class="uk-background-cover" style="background-image: url('<?=$url_base?>/inc/images/PracaDoRelogio-MarcosSantos.jpg');" uk-height-viewport></div>
                                <div class="uk-padding">
                                    <h3><?=$t->gettext('Unidades USP')?></h3>
                                    <?php $count = 0; ?>
				    <?php foreach($unidades as $key => $value ): ?>
					<?php if(!in_array($key, $schoolsFilterRemove)): ?>
                                            <?php $count++;?>
                                            <a href="result.php?filter[]=unidadeUSP:&quot;<?=$key?>&quot;" class="uk-text-small" style="color:#333">
                                                <?=$value?>
                                            </a>
                                            <br/>
                                            <?php if($count == 18 || $count == 36): ?>
                                                </div>
                                                <div class="uk-padding">
					    <?php endif; ?>
					<?php endif; ?> 
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </li>
                
             </ul>
        </div>
        <div class="uk-navbar-center" style="top: 60%;">
	<a class="uk-navbar-item uk-logo" href="<?=$url_base?>/index.php"><h1 style="font-family: Arial, sans-serif; color: #123e72;"><?=$branch_abrev_nav?></h1></a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                
                
		<li class="uk-active">
                    <?php if (empty($_SESSION['oauthuserdata'])) : ?>
		    <li><a href="<?=$url_base;?>/aut/oauth.php" rel="nofollow"><?=$t->gettext('Usuário')?></a></li>
                    <?php else: ?>
                    <li class="uk-active"><a href="#modal-user" uk-toggle><?=$_SESSION['oauthuserdata']->{'nomeUsuario'}?></a></li>
                    <div id="modal-user" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                            <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
                                <div class="uk-background-cover" style="background-image: url('<?=$url_base?>/inc/images/PracaDoRelogio-MarcosSantos.jpg');" uk-height-viewport></div>
                                <div class="uk-padding">
				    <h3>Bem vindo, <?=$_SESSION['oauthuserdata']->{'nomeUsuario'}?></h3>
                                    <p>Aqui você pode:</p>
                                    <ul>
                                    <li><a href="<?=$url_base?>/result.php?filter[]=authorUSP.codpes%3A&quot;<?=$_SESSION['oauthuserdata']->{'loginUsuario'}?>&quot;">Pesquisar por sua produção</a></li>
                                    <li><a href="<?=$url_base?>/tools/export.php?filter[]=authorUSP.codpes%3A&quot;<?=$_SESSION['oauthuserdata']->{'loginUsuario'}?>&quot;&format=ris">Exportar sua produçao em formato RIS</a></li>
                                    <li><a href="<?=$url_base?>/tools/export.php?filter[]=authorUSP.codpes%3A&quot;<?=$_SESSION['oauthuserdata']->{'loginUsuario'}?>&quot;&format=bibtex">Exportar sua produçao em formato Bibtex</a></li>
				    <?php if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)): ?>
				    <li><a href="<?=$url_base?>/admin/index.php">Administração</a></li>';
				    <?php endif;?>
                                    <li><a href="<?=$url_base?>/aut/logout.php">Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </li>

			<!--<li class="uk-active"><a href="<?=$institution_site?>" target="_blank" rel="noopener noreferrer"><?=$institution_acronym?></a></li>-->

                <!--<?php if ($_SESSION['localeToUse'] == 'en_US') : ?>
		    <li><a href="http://<?=$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"]?>?<?=$_SERVER["QUERY_STRING"]?>&locale=pt_BR"><img src="inc/images/br.png" style="width: 1.6em;">
                    </a></li>
                <?php else : ?>
                    <li><a href="http://<?=$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"]?>?<?=$_SERVER["QUERY_STRING"]; ?>&locale=en_US"><img src="inc/images/en.png" style="width: 1.6em;">
                    </a></li>
                <?php endif ; ?>-->
                <li>
                    <?php
                        $link = "http://".$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"]."?".preg_replace("/^&locale=[a-z]{2}_[A-Z]{2}$/", "", $_SERVER["QUERY_STRING"]);
                    ?>
                    <a href="<?=$link?>&locale=pt_BR" style="padding-right: 0.2em;">
                        <img src="<?=$url_base?>/inc/images/br.png" style="width: 1.6em;">
                    </a>
                </li>
                <li>
                    <a href="<?=$link?>&locale=en_US" style="padding-left: 0.2em;">
                        <img src="<?=$url_base?>/inc/images/en.png" style="width: 1.6em;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php if(!empty($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffAdmins)): ?>
    <div class="uk-align-right" style="position: absolute; top: 25px; right: 20px;"><a href="<?=$url_base?>/admin/gerencia.php" style="color: #1094ab  !important;">Gerenciar Staff e Bibliotecas</a></div>
    <?php endif; ?>
</div>


<div class="uk-hidden@m">
    <div class="uk-offcanvas-content">

        <div class="uk-navbar-left">
            <a class="uk-navbar-toggle" href="#" uk-toggle="target: #offcanvas-nav-primary" style="color:black"><span uk-navbar-toggle-icon></span> <span class="uk-margin-small-left">Menu</span></a>
        </div>

        <div id="offcanvas-nav-primary" uk-offcanvas="overlay: true">
            <div class="uk-offcanvas-bar uk-flex uk-flex-column">

                <ul class="uk-nav uk-nav-primary uk-nav-center uk-margin-auto-vertical">
                    <li class="uk-active"><a href="index.php"><?=$t->gettext('Início')?></a></li>
                    <li class="uk-active"><a href="advanced_search.php"><?=$t->gettext('Busca técnica')?></a></li>
                    <li class="uk-nav-divider"></li>
                    <li class="uk-active"><a href="contact.php"><?=$t->gettext('Contato')?></a></li>
                    <li class="uk-active"><a href="sobre.php"><?=$t->gettext('Sobre')?></a></li>
		    <li class="uk-active"><a href="<?=$institution_site?>" target="_blank" rel="noopener noreferrer"><?=$institution_acronym?></a></li>
                </ul>

            </div>
        </div>
    </div>
</div>

</div>
