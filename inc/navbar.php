<div class="uk-position-top">
<div class="uk-visible@m">
    <nav class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click">      
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
            </ul>
        </div>
        <div class="uk-navbar-center">
            <a class="uk-navbar-item uk-logo" href="index.php"><img src="http://www.scs.usp.br/identidadevisual/wp-content/uploads/2013/08/usp-logo-png.png" width="110px"></a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false"><?php echo $t->gettext('Contato'); ?></a>
                    <div class="uk-navbar-dropdown">
                        <p><b>Biblioteca da ECA</b></p>
                        <p>Atendimento:</p>
                        <p>(11) 3091.4071 / 4481</p>
                        <p>ecabiblioteca@usp.br</p>
                    </div>
                </li>               
                <li class="uk-active">
                    <a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a>     
                </li>
                <!--
                <li class="uk-active">
                    <a href="" class="" aria-expanded="false">< ?php echo $t->gettext('Usuário'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 913.503px;">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-nav-header">Acesso</li>
                            < ?php if(empty($_SESSION['oauthuserdata'])): ?>
                                <li><a href="aut/oauth.php">Login</a></li>
                            < ?php else: ?>
                                <li><a href="#">< ?php echo 'Bem vindo, '.$_SESSION['oauthuserdata']->{'nomeUsuario'}.'';?></a></li>
                                <li><a href="admin.php">Administração</a></li>
                                <li><a href="aut/logout.php">Logout</a></li>
                            < ?php endif; ?>
                        </ul>
                    </div>                
                </li>
                -->
                
                <?php if ($_SESSION['localeToUse'] == 'en_US') : ?>
                    <li><a href="?locale=pt_BR"><img src="inc/images/br.jpg" width="25px"></a></li>
                <?php else : ?>
                    <li><a href="?locale=en_US"><img src="inc/images/en.png" width="25px"></a></li>
                <?php endif ; ?>                
                
                
                <li class="uk-active"><a href="http://www3.eca.usp.br/biblioteca" style="color:#fff">Biblioteca da ECA</a></li>
                <li class="uk-active"><a href="http://sibi.usp.br">SIBiUSP</a></li>
            </ul>
        </div>            
    </nav>
</div>         
<div class="uk-hidden@m">
    <nav class="uk-navbar uk-navbar-container uk-navbar-transparent uk-margin">
        <div class="uk-navbar-left">
            <a class="uk-navbar-toggle" href="#offcanvas" style="color:#333" uk-toggle>
                <span uk-navbar-toggle-icon></span> <span class="uk-margin-small-left">Menu</span>
            </a>
        </div>
    </nav>
    <div id="offcanvas" uk-offcanvas>
        <div class="uk-offcanvas-bar">

            <h3>Title</h3>

            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

            <button class="uk-button uk-button-default uk-offcanvas-close uk-width-1-1 uk-margin" type="button">Close</button>

        </div>
    </div>                            
</div>

</div> 
