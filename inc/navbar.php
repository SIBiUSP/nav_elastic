<div class="uk-position-top">
<div class="uk-visible@m">
    <nav class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click">      
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="index.php">Início</a></li>
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false">Busca institucional</a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 1000.5px;">
                        <div class="uk-width-1-1@m">
                            <div class="uk-alert uk-alert-large">
                                <form class="uk-form" role="form" action="result.php" method="get">
                                    <fieldset data-uk-margin>
                                        <legend><?php echo $t->gettext('Número USP'); ?></legend>
                                        <input type="text" placeholder="Insira um número USP" name="codpes" data-validation="required">
                                        <button class="uk-button" type="submit"><?php echo $t->gettext('Buscar'); ?></button>
                                    </fieldset>
                                </form>
                                <form class="uk-form" role="form" action="result.php" method="get" name="assunto">
                                    <fieldset data-uk-margin>
                                        <legend>Assunto do Vocabulário Controlado</legend>
                                        <label><a href="#" onclick="creaPopup('inc/popterms/index.php?t=assunto&f=assunto&v=http://143.107.154.55/pt-br/services.php&loadConfig=1'); return false;">Consultar o Vocabulário Controlado USP</a></label><br/>
                                        <input type="text" name="assunto" data-validation="required">
                                        <button class="uk-button" type="submit"><?php echo $t->gettext('Buscar'); ?></button>
                                    </fieldset>
                                </form>        
                            </div>
                        </div> 
                    </div>
                </li>
                <li class="uk-active">
                    <a href="#">Busca avançada</a>
                    <div class="uk-navbar-dropdown">
                        <div class="uk-width-1-1@m">
                            <div class="uk-alert uk-alert-large">
                                <form class="uk-form" role="form" action="result.php" method="get">
                                    <fieldset data-uk-margin>
                                        <legend>String de busca avançada</legend>
                                        <p>Selecionar campos para realizar a busca: </p>
                                        <label><input type="checkbox" name="fields[]" value="title" checked> Título</label>
                                        <label><input type="checkbox" name="fields[]" value="authors_index" checked> Autores</label>
                                        <label><input type="checkbox" name="fields[]" value="authorUSP" checked> Autores USP</label>
                                        <label><input type="checkbox" name="fields[]" value="unidadeUSPtrabalhos"> Unidade USP</label>
                                        <label><input type="checkbox" name="fields[]" value="departamentotrabalhos"> Departamento</label>
                                        <label><input type="checkbox" name="fields[]" value="subject" checked> Assuntos</label>
                                        <label><input type="checkbox" name="fields[]" value="colab_instituicao_corrigido"> Colaboração institucional</label>
                                        <label><input type="checkbox" name="fields[]" value="fomento"> Agência de Fomento</label>
                                        <label><input type="checkbox" name="fields[]" value="sysno"> Sysno</label>
                                        <br/>
                                        <script>
                                            $( function() {
                                            $( "#slider-range" ).slider({
                                              range: true,
                                              min: 1900,
                                              max: 2030,
                                              values: [ 1900, 2030 ],
                                              slide: function( event, ui ) {
                                                $( "#amount" ).val( "year:[" + ui.values[ 0 ] + " TO " + ui.values[ 1 ] + "]" );
                                              }
                                            });
                                            $( "#amount" ).val( "year:[" + $( "#slider-range" ).slider( "values", 0 ) +
                                              " TO " + $( "#slider-range" ).slider( "values", 1 ) + "]");
                                            } );
                                        </script>
                                        <p>
                                          <label for="amount">Selecionar período de tempo:</label>
                                          <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;" name="search[]">
                                        </p>

                                        <div id="slider-range"></div>                                
                                        <br/>
                                        <textarea type="text" class="uk-form-width-large" placeholder="Insira sua string de busca avançada" name="search[]" data-validation="required"></textarea>
                                        <button class="uk-button" type="submit"><?php echo $t->gettext('Buscar'); ?></button>
                                        <br/><br/><br/><a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html" target="_blank">Consultar referência</a>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
             </ul>
        </div>
        <div class="uk-navbar-center">
            <a class="uk-navbar-item uk-logo" href="index.php"><img src="http://www.scs.usp.br/identidadevisual/wp-content/uploads/2013/08/usp-logo-png.png" width="110px"></a>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false"><?php echo $t->gettext('Contato'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 1000.5px;">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </div>
                </li>               
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false">Sobre</a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 913.503px;">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-active"><a href="#" style="height:57px">Active</a></li>
                             <li class="uk-parent">
                                <a href="#">Parent</a>
                                <ul class="uk-nav-sub">
                                    <li><a href="#">Sub item</a></li>
                                    <li><a href="#">Sub item</a></li>
                                </ul>
                            </li>
                            <li class="uk-nav-header">Header</li>
                            <li><a href="#"><span class="uk-margin-small-right uk-icon" uk-icon="icon: table"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" icon="table" width="20" height="20" ratio="1">
                            <rect x="1" y="3" width="18" height="1"></rect>
                            <rect x="1" y="7" width="18" height="1"></rect>
                            <rect x="1" y="11" width="18" height="1"></rect>
                            <rect x="1" y="15" width="18" height="1"></rect>
                        </svg></span> Item</a></li>
                                                            <li><a href="#"><span class="uk-margin-small-right uk-icon" uk-icon="icon: thumbnails"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" icon="thumbnails" width="20" height="20" ratio="1">
                            <rect fill="none" stroke="#000" x="3.5" y="3.5" width="5" height="5"></rect>
                            <rect fill="none" stroke="#000" x="11.5" y="3.5" width="5" height="5"></rect>
                            <rect fill="none" stroke="#000" x="11.5" y="11.5" width="5" height="5"></rect>
                            <rect fill="none" stroke="#000" x="3.5" y="11.5" width="5" height="5"></rect>
                        </svg></span> Item</a></li>
                                                            <li class="uk-nav-divider"></li>
                                                            <li><a href="#"><span class="uk-margin-small-right uk-icon" uk-icon="icon: trash"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" icon="trash" width="20" height="20" ratio="1">
                            <polyline fill="none" stroke="#000" points="6.5 3 6.5 1.5 13.5 1.5 13.5 3"></polyline>
                            <polyline fill="none" stroke="#000" points="4.5 4 4.5 18.5 15.5 18.5 15.5 4"></polyline>
                            <rect x="8" y="7" width="1" height="9"></rect>
                            <rect x="11" y="7" width="1" height="9"></rect>
                            <rect x="2" y="3" width="16" height="1"></rect>
                        </svg></span> Item</a></li>
                        </ul>
                    </div>
                </li>
                <li class="uk-active">
                    <a href="" class="" aria-expanded="false"><?php echo $t->gettext('Usuário'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 913.503px;">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-nav-header"><?php echo $t->gettext('Ferramentas'); ?></li>
                            <li><a href="comparar_lattes.php">Comparador Lattes</a></li>
                            <li><a href="comparar_wos.php">Comparador WoS</a></li>
                            <li><a href="comparar_werusp.php">Comparador weRUSP</a></li>
                            <li><a href="comparar_csv_scopus.php">Comparador Scopus</a></li>
                            <li class="uk-nav-divider"></li>
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
                    <li><a href="?locale=pt_BR"><img src="inc/images/br.jpg" width="25px"></a></li>
                <?php else : ?>
                    <li><a href="?locale=en_US"><img src="inc/images/en.png" width="25px"></a></li>
                <?php endif ; ?>                
                
                
                
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
