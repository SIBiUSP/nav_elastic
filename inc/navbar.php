<?php 
	include_once("inc/config.php");
	include_once("admin/staffAdmins.php");
?>

<div style="height: 0.3em; background: #fcb421;"></div>
<div style="height: 0.4em; background: #64c4d2;"></div>
<div class="uk-card uk-card-default" >
<div class="uk-visible@m">
    <div id="menu" class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click" uk-navbar>
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav uk-link-text">
                <li class="uk-active"><a class="uk-link-heading" href="<?php echo $url_base; ?>/index.php"><?php echo $t->gettext('Início'); ?></a></li>
                <li class="uk-active">
                    <a href="<?php echo $url_base; ?>/sobre.php"><?php echo $t->gettext('Sobre'); ?></a>
                </li>
                <li class="uk-active">
                    <a href="#modal-full" uk-toggle><?php echo $t->gettext('Unidades USP'); ?></a>

                    <div id="modal-full" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                            <div class="uk-grid-collapse uk-child-width-1-4@s uk-flex-middle" uk-grid>
                                <div class="uk-background-cover" style="background-image: url('<?php echo $url_base; ?>/inc/images/PracaDoRelogio-MarcosSantos.jpg');" uk-height-viewport></div>
                                <div class="uk-padding">
                                    <h3><?php echo $t->gettext('Unidades USP'); ?></h3>
                                    <?php $count = 0; ?>
				    <?php foreach($unidades as $key => $value ): ?>
					<?php if(!in_array($key, $schoolsFilterRemove)): ?>
                                            <?php $count++;?>
                                            <a href="result.php?filter[]=unidadeUSP:&quot;<?php echo $key; ?>&quot;" class="uk-text-small" style="color:#333">
                                                <?php echo $value; ?>
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
	<a class="uk-navbar-item uk-logo" href="<?php echo $url_base; ?>/index.php"><h1 style="font-family: Arial, sans-serif; color: #123e72;"><?php echo $branch_abrev_nav; ?></h1></a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                
                
		<li class="uk-active">
                    <?php if (empty($_SESSION['oauthuserdata'])) : ?>
                    <li><a href="https://www.repositorio.usp.br/aut/oauth.php" rel="nofollow"><?php echo $t->gettext('Usuário'); ?></a></li>
                    <?php else: ?>
                    <li class="uk-active"><a href="#modal-user" uk-toggle><?php echo $_SESSION['oauthuserdata']->{'nomeUsuario'}; ?></a></li>
                    <div id="modal-user" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                            <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
                                <div class="uk-background-cover" style="background-image: url('<?php echo $url_base; ?>/inc/images/PracaDoRelogio-MarcosSantos.jpg');" uk-height-viewport></div>
                                <div class="uk-padding">
                                    <h3><?php echo 'Bem vindo, '.$_SESSION['oauthuserdata']->{'nomeUsuario'}.'';?></h3>
                                    <p>Aqui você pode:</p>
                                    <ul>
                                    <li><a href="<?php echo $url_base; ?>/result.php?filter[]=authorUSP.codpes%3A&quot;<?php echo($_SESSION['oauthuserdata']->{'loginUsuario'}); ?>&quot;">Pesquisar por sua produção</a></li>
                                    <li><a href="<?php echo $url_base; ?>/tools/export.php?filter[]=authorUSP.codpes%3A&quot;<?php echo($_SESSION['oauthuserdata']->{'loginUsuario'}); ?>&quot;&format=ris">Exportar sua produçao em formato RIS</a></li>
                                    <li><a href="<?php echo $url_base; ?>/tools/export.php?filter[]=authorUSP.codpes%3A&quot;<?php echo($_SESSION['oauthuserdata']->{'loginUsuario'}); ?>&quot;&format=bibtex">Exportar sua produçao em formato Bibtex</a></li>
                                    <?php
                                    if (in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffUsers)) {
                                        echo '<li><a href="'.$url_base.'/admin/index.php">Administração</a></li>';
                                    }
                                    ?>
                                    <li><a href="<?php echo $url_base; ?>/aut/logout.php">Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </li>

                <!--<li class="uk-active"><a href="http://aguia.usp.br" target="_blank" rel="noopener noreferrer">AGUIA</a></li>-->

                <!--<?php if ($_SESSION['localeToUse'] == 'en_US') : ?>
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=pt_BR"><img src="inc/images/br.png" style="width: 1.6em;">
                    </a></li>
                <?php else : ?>
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=en_US"><img src="inc/images/en.png" style="width: 1.6em;">
                    </a></li>
                <?php endif ; ?>-->
                <li>
                    <?php
                        $link = "http://".$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"]."?".preg_replace("/^&locale=[a-z]{2}_[A-Z]{2}$/", "", $_SERVER["QUERY_STRING"]);
                    ?>
                    <a href="<?php echo $link; ?>&locale=pt_BR" style="padding-right: 0.2em;">
                        <img src="<?php echo $url_base; ?>/inc/images/br.png" style="width: 1.6em;">
                    </a>
                </li>
                <li>
                    <a href="<?php echo $link; ?>&locale=en_US" style="padding-left: 0.2em;">
                        <img src="<?php echo $url_base; ?>/inc/images/en.png" style="width: 1.6em;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php if(!empty($_SESSION['oauthuserdata']) && in_array($_SESSION['oauthuserdata']->{'loginUsuario'}, $staffAdmins)): ?>
    <div class="uk-align-right" style="position: absolute; top: 25px; right: 20px;"><a href="<?php echo $url_base; ?>/admin/staffusers.php" style="color: #1094ab  !important;">Gerenciar Usuários</a></div>
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
                    <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                    <li class="uk-active"><a href="advanced_search.php"><?php echo $t->gettext('Busca técnica'); ?></a></li>
                    <li class="uk-nav-divider"></li>
                    <li class="uk-active"><a href="contact.php"><?php echo $t->gettext('Contato'); ?></a></li>
                    <li class="uk-active"><a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a></li>
                    <li class="uk-active"><a href="http://www.aguia.usp.br" target="_blank" rel="noopener noreferrer">AGUIA</a></li>
                </ul>

            </div>
        </div>
    </div>
</div>

</div>
