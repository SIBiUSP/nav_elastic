        <div class="barrausp">
            <div class="uk-container uk-container-center">

            <nav class="uk-margin-top">
                <a class="uk-navbar-brand uk-hidden-small" href="index.php" style="color:white">BDPI USP</a>
                <ul class="uk-navbar-nav uk-hidden-small">
                    <li>
                        <a href="index.php" style="color:white"><?php echo $t->gettext('Início'); ?></a>
                    </li>
                    <li>
                        <a href="#" data-uk-toggle="{target:'#busca_contextualizada'}" style="color:white">Busca contextualizada</a>
                    </li>
                    <li>
                        <a href="#" data-uk-toggle="{target:'#busca_avancada'}" style="color:white"><?php echo $t->gettext('Busca avançada'); ?></a>
                    </li>                    
                </ul>
                    <div class="uk-navbar-flip">
                        <ul class="uk-navbar-nav uk-hidden-small">
                            <li data-uk-dropdown="{mode:'click'}">
                                <a href="" style="color:white">
                                    <?php echo $t->gettext('Idioma'); ?>
                                    <i class="uk-icon-caret-down"></i>
                                </a>
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li style="color:black"><a href="index.php?locale=pt_BR"><?php echo $t->gettext('Português'); ?></a></li>
                                        <li><a href="index.php?locale=en_US"><?php echo $t->gettext('Inglês'); ?></a></li>
                                    </ul>
                                </div> 
                            </li>
                            <li>
                                <a href="contato.php" style="color:white"><?php echo $t->gettext('Contato'); ?></a>
                            </li>
                            <!--
                            <li>
                                <a href="about.php" style="color:white">Sobre</a>
                            </li>
                            -->
                            <li data-uk-dropdown="" aria-haspopup="true" aria-expanded="false">
                                <a href="" style="color:white"><i class="uk-icon-home"></i> <?php echo $t->gettext('Usuário'); ?></a>

                                <div class="uk-dropdown uk-dropdown-navbar uk-dropdown-bottom" style="top: 40px; left: 0px;">
                                    <ul class="uk-nav uk-nav-navbar">
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
                                            <li><a href="aut/logout.php">Logout</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                            </li>
                            <a class="uk-navbar-brand uk-hidden-small" href="http://sibi.usp.br" style="color:white">SIBiUSP</a>
                        </ul>
                    </div>                
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" style="color:white" data-uk-offcanvas></a>
                <div class="uk-navbar-brand uk-navbar-center uk-visible-small" style="color:white">BDPI USP</div>
            </nav>
                
            </div>
            
            <div id="busca_contextualizada" class="uk-container uk-container-center uk-hidden" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
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
            
            <div id="busca_avancada" class="uk-container uk-container-center uk-hidden" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
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
        </div>