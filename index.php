<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php'); 
            include('inc/functions.php');
            include('inc/meta-header.php'); 
        ?> 
        <title>BDPI USP - Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo</title>
        <!-- Facebook Tags - START -->
        <meta property="og:locale" content="pt_BR">
        <meta property="og:url" content="http://bdpi.usp.br"> 
        <meta property="og:site_name" content="Base de Produção Intelectual da USP">
        <meta property="og:description" content="Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo.">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800"> 
        <meta property="og:image:height" content="600"> 
        <meta property="og:type" content="website">
        <!-- Facebook Tags - END -->
        
    </head>

    <body>        
        <?php include_once("inc/analyticstracking.php") ?>
        <?php include('inc/navbar.php'); ?>
        
            
            <div class="uk-grid uk-margin-large-bottom" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <div class="uk-vertical-align uk-text-center uk-responsive-width" style="background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNi4wLjQsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB3aWR0aD0iMTEzMHB4IiBoZWlnaHQ9IjQ1MHB4IiB2aWV3Qm94PSIwIDAgMTEzMCA0NTAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDExMzAgNDUwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxyZWN0IGZpbGw9IiNGNUY1RjUiIHdpZHRoPSIxMTMwIiBoZWlnaHQ9IjQ1MCIvPg0KPC9zdmc+DQo=') 50% 0 no-repeat; height: 350px;">
                        <div class="uk-vertical-align-middle uk-width-1-2">
                            <h1>Base de Produção Intelectual da USP</h1>
                            <p>Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo.</p>
                            <form class="uk-form" action="result.php" method="get">
                                <fieldset data-uk-margin>
                                    <legend>Pesquisa</legend>
                                    <input type="text" placeholder="Pesquise por termo ou autor" class="uk-form-width-medium" name="search_index">                                        
                                    <select name="base[]">
                                        <option value="all">Todas as bases</option>
                                        <option value="Produção científica">Produção científica</option>
                                        <option value="Teses e dissertações">Teses e dissertações</option>
                                    </select>
                                    <button class="uk-button-primary">Buscar</button>                                    
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <div class="uk-container uk-container-center uk-margin-large-bottom">
        
            <hr class="uk-grid-divider">
            
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    <div class="uk-grid">
                        <div class="uk-width-1-6">
                            <i class="uk-icon-university uk-icon-large uk-text-primary"></i>
                        </div>
                        <div class="uk-width-5-6">
                            <h2 class="uk-h3">Unidades USP e Programas de Pós-Graduação Interunidades</h2>
                            <ul class="uk-list uk-list-striped">
                                <?php unidadeUSP_inicio($server); ?>
                            </ul>                            
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-3">
                    <div class="uk-grid">
                        <div class="uk-width-1-6">
                            <i class="uk-icon-file uk-icon-large uk-text-primary"></i>
                        </div>
                        <div class="uk-width-5-6">
                            <h2 class="uk-h3">Base</h2>
                            <ul class="uk-list uk-list-striped">
                                <?php base_inicio($server); ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-3">
                    <div class="uk-grid">
                        <div class="uk-width-1-6">
                            <i class="uk-icon-bar-chart uk-icon-large uk-text-primary"></i>
                        </div>
                        <div class="uk-width-5-6">
                            <h2 class="uk-h3">Nossos números</h2>
                            <ul class="uk-list uk-list-striped">
                                <li><?php echo number_format(contar_registros($server),0,',','.'); ?> registros</li>
                                <li><?php echo number_format(contar_unicos(authorUSP,$server),0,',','.'); ?> autores vinculados à USP</li>                                
                            </ul>
                        </div>
                    </div>
                </div>                
            </div>

                
            <div id="unidades" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <h2 class="uk-h3">Navegar pelas Unidades USP</h2>           

                    <ul id="filter" class="uk-subnav uk-subnav-pill">
                        <li class="uk-active" data-uk-filter=""><a href="">Todas</a></li>
                        <li data-uk-filter="filter-h" class=""><a href="">Humanas</a></li>
                        <li data-uk-filter="filter-e" class=""><a href="">Exatas</a></li>
                        <li data-uk-filter="filter-b" class=""><a href="">Biológicas</a></li>
                        <li data-uk-filter="filter-i" class=""><a href="">Centros, Hospitais, Institutos Especializados e Museus</a></li>
                        <li data-uk-sort="filter"><a href="">Siglas (A -> Z)</a></li>
                        <li data-uk-sort="filter:desc"><a href="">Siglas (Z -> A)</a></li>
                    </ul>

                            <div class="uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-10 tm-grid-heights" data-uk-grid="{controls: '#filter'}" style="position: relative; margin-left: -10px; height: 394px;">
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 0px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex" data-filter="cebimar">
                                    <a href="result.php?unidadeUSPtrabalhos[]=CEBIMAR">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/CEBIMAR.jpg" alt="CEBIMAR">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Biologia Marinha (CEBIMAR)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex" data-filter="cdcc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=CDCC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/CDCC.jpg" alt="CDCC">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Divulgação Científica e Cultural (CDCC)</p></small>
                                        </div>
                                    </a>
                                </div>                                
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex" data-filter="cena">
                                    <a href="result.php?unidadeUSPtrabalhos[]=CENA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/CENA.jpg" alt="CENA">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Energia Nuclear na Agricultura (CENA)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-h,filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false" data-filter="each">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EACH">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EACH.jpg" alt="EACH">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Artes, Ciências e Humanidades (EACH)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 632.049px; opacity: 1; display: block;" aria-hidden="false" data-filter="eca">
                                    <a href="result.php?unidadeUSPtrabalhos[]=ECA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/ECA.jpg" alt="ECA">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Comunicações e Artes (ECA)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 132px; left: 0px; opacity: 1; display: block;" aria-hidden="false" data-filter="ee">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EE">
                                        <div class="uk-panel uk-panel-hover uk-text-center" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser">
                                                <img src="inc/images/logosusp/EE.jpg" alt="EE">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Enfermagem (EE)</p></small>
                                        </div>
                                    </a>                                    
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 152px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false" data-filter="eerp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EERP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EERP.jpg" alt="EERP">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Enfermagem de Ribeirão Preto (EERP)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 212px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false" data-filter="eefe">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EEFE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EEFE.jpg" alt="EEFE">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Educação Física e Esporte (EEFE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 212px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false" data-filter="eeferp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EEFERP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EEFERP.jpg" alt="EEFERP">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="eel">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EEL">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EEL.jpg" alt="EEL">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Engenharia de Lorena (EEL)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="eesc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EESC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EESC.jpg" alt="EESC">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Engenharia de São Carlos (EESC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ep">
                                    <a href="result.php?unidadeUSPtrabalhos[]=EP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/EP.jpg" alt="EP">
                                            </div>
                                            <small><p class="uk-text-center">Escola Politécnica (EP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b,filter-e,filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="esalq">
                                    <a href="result.php?unidadeUSPtrabalhos[]=ESALQ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/ESALQ.jpg" alt="ESALQ">
                                            </div>
                                            <small><p class="uk-text-center">Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fau">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FAU">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FAU.jpg" alt="FAU">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Arquitetura e Urbanismo (FAU)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fcf">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FCF">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FCF.jpg" alt="FCF">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Ciências Farmacêuticas (FCF)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fcfrp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FCFRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FCFRP.jpg" alt="FCFRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fd">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FD">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FD.jpg" alt="FD">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Direito (FD)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fdrp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FDRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FDRP.jpg" alt="FDRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Direito de Ribeirão Preto (FDRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fea">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FEA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FEA.jpg" alt="FEA">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Economia, Administração e Contabilidade (FEA)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fearp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FEARP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FEARP.jpg" alt="FEARP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fe">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FE.jpg" alt="FE">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Educação (FE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h,filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ffclrp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FFCLRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FFCLRP.jpg" alt="FFCLRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fflch">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FFLCH">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FFLCH.jpg" alt="FFLCH">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fm">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FM">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FM.jpg" alt="FM">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina (FM)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fmrp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FMRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FMRP.jpg" alt="FMRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina de Ribeirão Preto (FMRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fmvz">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FMVZ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FMVZ.jpg" alt="FMVZ">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina Veterinária e Zootecnia (FMVZ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                 
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fo">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FO" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FO.jpg" alt="FO">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia (FO)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fob">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FOB" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover" style="padding:0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FOB.jpg" alt="FOB">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia de Bauru (FOB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="forp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FORP" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover" style="padding:0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FORP.jpg" alt="FORP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia de Ribeirão Preto (FORP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fsp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FSP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FSP.jpg" alt="FSP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Saúde Pública (FSP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="fzea">
                                    <a href="result.php?unidadeUSPtrabalhos[]=FZEA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/FZEA.jpg" alt="FZEA">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="hrac">
                                    <a href="result.php?unidadeUSPtrabalhos[]=HRAC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/HRAC.jpg" alt="HRAC">
                                            </div>
                                            <small><p class="uk-text-center">Hospital de Reabilitação de Anomalias Craniofaciais (HRAC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                  
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="hu">
                                    <a href="result.php?unidadeUSPtrabalhos[]=HU">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/HU.jpg" alt="HU">
                                            </div>
                                            <small><p class="uk-text-center">Hospital Universitário (HU)</p></small>
                                        </div>
                                    </a> 
                                </div>                                 
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iau">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IAU">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IAU.jpg" alt="IAU">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Arquitetura e Urbanismo (IAU)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iag">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IAG">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IAG.jpg" alt="IAG">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ib">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IB.jpg" alt="IB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Biociências (IB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="icb">
                                    <a href="result.php?unidadeUSPtrabalhos[]=ICB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/ICB.jpg" alt="ICB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Ciências Biomédicas (ICB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="icmc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=ICMC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/ICMC.jpg" alt="ICMC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Ciências Matemáticas e de Computação (ICMC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iee">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IEE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IEE.jpg" alt="IEE">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Energia e Ambiente (IEE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ieb">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IEB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IEB.jpg" alt="IEB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Estudos Brasileiros (IEB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="if">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IF">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IF.jpg" alt="IF">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Física (IF)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ifsc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IFSC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IFSC.jpg" alt="IFSC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Física de São Carlos (IFSC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="igc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IGC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IGC.jpg" alt="IGC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Geociências (IGc)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ime">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IME">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IME.jpg" alt="IME">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Matemática e Estatística (IME)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="imt">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IMT">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IMT.jpg" alt="IMT">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Medicina Tropical de São Paulo (IMT)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="ip">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IP.jpg" alt="IP">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Psicologia (IP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iq">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IQ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IQ.jpg" alt="IQ">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Química (IQ)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iqsc">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IQSC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IQSC.jpg" alt="IQSC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Química de São Carlos (IQSC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="iri">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IRI">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IRI.jpg" alt="IRI">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Relações Internacionais (IRI)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="io">
                                    <a href="result.php?unidadeUSPtrabalhos[]=IO">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/IO.jpg" alt="IO">
                                            </div>
                                            <small><p class="uk-text-center">Instituto Oceanográfico (IO)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="mae">
                                    <a href="result.php?unidadeUSPtrabalhos[]=MAE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/MAE.jpg" alt="MAE">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Arqueologia e Etnologia (MAE)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="mac">
                                    <a href="result.php?unidadeUSPtrabalhos[]=MAC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/MAC.jpg" alt="MAC">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Arte Contemporânea (MAC)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="mz">
                                    <a href="result.php?unidadeUSPtrabalhos[]=MZ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/MZ.jpg" alt="MZ">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Zoologia (MZ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                  
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false" data-filter="mp">
                                    <a href="result.php?unidadeUSPtrabalhos[]=MP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="inc/images/logosusp/MP.jpg" alt="MP">
                                            </div>
                                            <small><p class="uk-text-center">Museu Paulista (MP)</p></small>
                                        </div>
                                    </a> 
                                </div>                               
                    </div>                
                </div>
            </div> 

            <hr class="uk-grid-divider">
            
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <h2 class="uk-h3">Últimos registros</h2>
                    <?php ultimos_registros($server);?>       
                     
                </div>
            </div>               
            
            <hr class="uk-grid-divider">
            
<?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>