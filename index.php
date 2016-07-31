<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php include('inc/functions.php'); ?> 
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BDPI USP - Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo</title>
        <link rel="shortcut icon" href="inc/images/faviconUSP.ico" type="image/x-icon">
        <!-- <link rel="stylesheet" href="inc/uikit/css/uikit.min.css"> -->
        <link rel="stylesheet" href="inc/uikit/css/uikit.css">
        <link rel="stylesheet" href="inc/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>        
        <script src="inc/uikit/js/uikit.min.js"></script>
        <script src="inc/uikit/js/components/grid.js"></script>
    </head>

    <body>        

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
                                <?php unidadeUSP_inicio(); ?>
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
                                <?php base_inicio(); ?>
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
                                <li><?php contar_registros(); ?> registros</li>
                                <li><?php contar_unicos(authorUSP); ?> autores vinculados à USP</li>                                
                            </ul>
                        </div>
                    </div>
                </div>                
            </div>

                
            <div id="unidades" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-1">
                    <h2 class="uk-h3">Todas as Unidades USP</h2>           

                    <ul id="filter" class="uk-subnav uk-subnav-pill">
                        <li class="uk-active" data-uk-filter=""><a href="">Todas</a></li>
                        <li data-uk-filter="filter-h" class=""><a href="">Humanas</a></li>
                        <li data-uk-filter="filter-e" class=""><a href="">Exatas</a></li>
                        <li data-uk-filter="filter-b" class=""><a href="">Biológicas</a></li>
                        <li data-uk-filter="filter-i" class=""><a href="">Centros, Institutos Especializados e Museus</a></li>
                    </ul>

                            <div class="uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-10 tm-grid-heights" data-uk-grid="{controls: '#filter'}" style="position: relative; margin-left: -10px; height: 394px;">
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 0px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex">
                                    <a href="result.php?unidadeUSP[]=CEBIMAR">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/CEBIMAR.jpg" alt="CEBIMAR">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Biologia Marinha (CEBIMAR)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex">
                                    <a href="result.php?unidadeUSP[]=CDCC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/CDCC.jpg" alt="CDCC">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Divulgação Científica e Cultural (CDCC)</p></small>
                                        </div>
                                    </a>
                                </div>                                
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false" class="uk-flex">
                                    <a href="result.php?unidadeUSP[]=CENA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/CENA.jpg" alt="CENA">
                                            </div>
                                            <small><p class="uk-text-center">Centro de Energia Nuclear na Agricultura (CENA)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-h,filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EACH">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EACH.jpg" alt="EACH">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Artes, Ciências e Humanidades (EACH)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 0px; left: 632.049px; opacity: 1; display: block;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=ECA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/ECA.jpg" alt="ECA">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Comunicações e Artes (ECA)</p></small>
                                        </div>
                                    </a>
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 132px; left: 0px; opacity: 1; display: block;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EE">
                                        <div class="uk-panel uk-panel-hover uk-text-center" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EE.jpg" alt="EE">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Enfermagem (EE)</p></small>
                                        </div>
                                    </a>                                    
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 152px; left: 210.683px; opacity: 1; display: block;" aria-hidden="false">
                                    <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                        <div class="uk-panel-teaser uk-text-center">
                                            <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EERP.jpg" alt="EERP">
                                        </div>
                                        <small><p class="uk-text-center">Escola de Enfermagem de Ribeirão Preto (EERP)</p></small>
                                    </div> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 212px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EEFE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EEFE.jpg" alt="EEFE">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Educação Física e Esporte (EEFE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 212px; left: 421.366px; opacity: 1; display: block;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EEFERP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EEFERP.jpg" alt="EEFERP">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EEL">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EEL.jpg" alt="EEL">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Engenharia de Lorena (EEL)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EESC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EESC.jpg" alt="EESC">
                                            </div>
                                            <small><p class="uk-text-center">Escola de Engenharia de São Carlos (EESC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=EP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/EP.jpg" alt="EP">
                                            </div>
                                            <small><p class="uk-text-center">Escola Politécnica (EP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b,filter-e,filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=ESALQ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/ESALQ.jpg" alt="ESALQ">
                                            </div>
                                            <small><p class="uk-text-center">Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FAU">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FAU.jpg" alt="FAU">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Arquitetura e Urbanismo (FAU)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FCF">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FCF.jpg" alt="FCF">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Ciências Farmacêuticas (FCF)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FCFRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FCFRP.jpg" alt="FCFRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FD">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FD.jpg" alt="FD">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Direito (FD)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FDRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FDRP.jpg" alt="FDRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Direito de Ribeirão Preto (FDRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FEA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FEA.jpg" alt="FEA">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Economia, Administração e Contabilidade (FEA)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FEARP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FEARP.jpg" alt="FEARP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FE.jpg" alt="FE">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Educação (FE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h,filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FFCLRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FFCLRP.jpg" alt="FFCLRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FFLCH">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FFLCH.jpg" alt="FFLCH">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FM">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FM.jpg" alt="FM">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina (FM)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FMRP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FMRP.jpg" alt="FMRP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina de Ribeirão Preto (FMRP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FMVZ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FMVZ.jpg" alt="FMVZ">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Medicina Veterinária e Zootecnia (FMVZ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                 
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FO" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FO.jpg" alt="FO">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia (FO)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FOB" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover" style="padding:0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FOB.jpg" alt="FOB">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia de Bauru (FOB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FORP" style="padding:15px 0 0 0">
                                        <div class="uk-panel uk-panel-hover" style="padding:0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FORP.jpg" alt="FORP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Odontologia de Ribeirão Preto (FORP)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FSP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FSP.jpg" alt="FSP">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Saúde Pública (FSP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b,filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=FZEA">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/FZEA.jpg" alt="FZEA">
                                            </div>
                                            <small><p class="uk-text-center">Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IAU">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IAU.jpg" alt="IAU">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Arquitetura e Urbanismo (IAU)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IAG">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IAG.jpg" alt="IAG">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IB.jpg" alt="IB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Biociências (IB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=ICB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/ICB.jpg" alt="ICB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Ciências Biomédicas (ICB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=ICMC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/ICMC.jpg" alt="ICMC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Ciências Matemáticas e de Computação (ICMC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IEE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IEE.jpg" alt="IEE">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Energia e Ambiente (IEE)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IEB">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IEB.jpg" alt="IEB">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Estudos Brasileiros (IEB)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IF">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IF.jpg" alt="IF">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Física (IF)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IFSC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IFSC.jpg" alt="IFSC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Física de São Carlos (IFSC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IGC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IGC.jpg" alt="IGC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Geociências (IGc)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-e" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IME">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IME.jpg" alt="IME">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Matemática e Estatística (IME)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IMT">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IMT.jpg" alt="IMT">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Medicina Tropical de São Paulo (IMT)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IP.jpg" alt="IP">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Psicologia (IP)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IQ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IQ.jpg" alt="IQ">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Química (IQ)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IQSC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IQSC.jpg" alt="IQSC">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Química de São Carlos (IQSC)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-h" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IRI">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IRI.jpg" alt="IRI">
                                            </div>
                                            <small><p class="uk-text-center">Instituto de Relações Internacionais (IRI)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-b" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=IO">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/IO.jpg" alt="IO">
                                            </div>
                                            <small><p class="uk-text-center">Instituto Oceanográfico (IO)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=MAE">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/MAE.jpg" alt="MAE">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Arqueologia e Etnologia (MAE)</p></small>
                                        </div>
                                    </a> 
                                </div>                                
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=MAC">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/MAC.jpg" alt="MAC">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Arte Contemporânea (MAC)</p></small>
                                        </div>
                                    </a> 
                                </div>
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=MZ">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/MZ.jpg" alt="MZ">
                                            </div>
                                            <small><p class="uk-text-center">Museu de Zoologia (MZ)</p></small>
                                        </div>
                                    </a> 
                                </div>                                  
                                <div data-uk-filter="filter-i" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; padding-left: 20px; padding-bottom: 20px; top: 172px; left: 632.049px; opacity: 1;" aria-hidden="false">
                                    <a href="result.php?unidadeUSP[]=MP">
                                        <div class="uk-panel uk-panel-hover" style="padding:15px 0 0 0">
                                            <div class="uk-panel-teaser uk-text-center">
                                                <img src="http://bdpife3.sibi.usp.br/inc/images/logosusp/MP.jpg" alt="MP">
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
                    <?php ultimos_registros_new();?>       
                     
                </div>
            </div>               
            
            <hr class="uk-grid-divider">
            
<?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>