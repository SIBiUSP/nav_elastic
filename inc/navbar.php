<div class="uk-position-top">
<div class="uk-visible@m">
    <nav class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click">      
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false"><?php echo $t->gettext('Busca institucional'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 1000.5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div>
                                <form class="uk-form" role="form" action="result.php" method="get">
                                    <fieldset data-uk-margin>
                                        <legend><?php echo $t->gettext('Número USP'); ?></legend>
                                        <input id='searchCodpes' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                        <button class="uk-button" type="submit" onclick="document.getElementById('searchCodpes').value = 'authorUSP.codpes.keyword:' + String.fromCharCode(34) + document.getElementById('searchCodpes').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
                                    </fieldset>
                                </form>
                                <form class="uk-form" role="form" action="result.php" method="get" name="searchIBox">
                                    <fieldset data-uk-margin>
                                        <legend>Assunto do Vocabulário Controlado</legend>
                                        <label><a href="#" onclick="creaPopup('inc/popterms/index.php?t=searchIBox&f=searchIBox&v=http://143.107.154.55/pt-br/services.php&loadConfig=1'); return false;">Consultar o Vocabulário Controlado USP</a></label><br/>
                                        <input id='searchIBox' type="text" name="search[]" data-validation="required">
                                        <button class="uk-button" type="submit" onclick="document.getElementById('searchIBox').value = 'subject.keyword:' + String.fromCharCode(34) + document.getElementById('searchIBox').value.trim() + String.fromCharCode(34)" ><?php echo $t->gettext('Buscar'); ?></button>                          
                                    </fieldset>
                                </form>        
                            </div>
                        </div> 
                    </div>
                </li>
                <li class="uk-active">
                    <a href="advanced_search.php"><?php echo $t->gettext('Busca avançada'); ?></a>
                </li>
             </ul>
        </div>
        <div class="uk-navbar-center">
            <a class="uk-navbar-item uk-logo" href="index.php"><img src="http://www.scs.usp.br/identidadevisual/wp-content/uploads/2013/08/usp-logo-png.png" width="110px"></a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <li class="uk-active">
                    <a href="contact.php"><?php echo $t->gettext('Contato'); ?></a>
                </li>               
                <li class="uk-active">
                    <a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a>     
                </li>
                <li class="uk-active">
                    <a href="" class="" aria-expanded="false"><?php echo $t->gettext('Usuário'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 913.503px;">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-nav-header">Acesso</li>
                            <?php if(empty($_SESSION['oauthuserdata'])): ?>
                                <li><a href="aut/oauth.php">Login</a></li>
                            <?php else: ?>
                                <li><a href="#"><?php echo 'Bem vindo, '.$_SESSION['oauthuserdata']->{'nomeUsuario'}.'';?></a></li>
                                <li><a href="admin.php">Administração</a></li>
                                <li><a href="aut/logout.php">Logout</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>                
                </li>
                
                <?php if ($_SESSION['localeToUse'] == 'en_US') : ?>
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=pt_BR">Português</a></li>
                <?php else : ?>
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=en_US">English</a></li>
                <?php endif ; ?>                
                
                
                
                <li class="uk-active"><a href="http://sibi.usp.br">SIBiUSP</a></li>
            </ul>
        </div>            
    </nav>
</div>


<div class="uk-hidden@m">
    <div class="uk-offcanvas-content">

        <button class="uk-button uk-button-default uk-margin-small-right" type="button" uk-toggle="target: #offcanvas-nav-primary">Menu</button>

        <div id="offcanvas-nav-primary" uk-offcanvas="overlay: true">
            <div class="uk-offcanvas-bar uk-flex uk-flex-column">

                <ul class="uk-nav uk-nav-primary uk-nav-center uk-margin-auto-vertical">
                    <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                    <li class="uk-active"><a href="advanced_search.php"><?php echo $t->gettext('Busca avançada'); ?></a></li>
                    <li class="uk-nav-divider"></li>
                    <li class="uk-active"><a href="contact.php"><?php echo $t->gettext('Contato'); ?></a></li>
                    <li class="uk-active"><a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a></li>
                    <li class="uk-active"><a href="http://sibi.usp.br">SIBiUSP</a></li>
                </ul>

            </div>
        </div>
    </div>
</div>

</div> 
