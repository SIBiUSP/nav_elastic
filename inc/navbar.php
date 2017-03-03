<div class="uk-position-top">
<div class="uk-visible@m">
    <nav class="uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click">      
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                <li class="uk-active">
                    <a href="#" class="" aria-expanded="false">Busca institucional</a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 1000.5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div>
                                <form class="uk-form" role="form" action="result.php" method="get">
                                    <fieldset data-uk-margin>
                                        <legend><?php echo $t->gettext('Número USP'); ?></legend>
                                        <input id='searchCodpes' type="text" placeholder="Insira um número USP" name="search[]" data-validation="required">
                                        <button class="uk-button" type="submit" onclick="document.getElementById('searchCodpes').value = 'codpes.keyword:' + String.fromCharCode(34) + document.getElementById('searchCodpes').value.trim() + String.fromCharCode(34)"><?php echo $t->gettext('Buscar'); ?></button>
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
                    <a href="#"><?php echo $t->gettext('Busca avançada'); ?></a>
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
                    <a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a>     
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
