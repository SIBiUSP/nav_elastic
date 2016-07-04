<!DOCTYPE html>
<html>
    <head>
        <title>BDPI USP - Biblioteca Digital da Produção Intelectual da Universidade de São Paulo</title>
        <?php include('inc/meta-header.php'); ?>
        <?php include('inc/functions.php'); ?>       
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">

                <div class="ui two column stackable grid">
                    <div class="ten wide column">

                        <div class="ui instant move reveal">
                            <img class="visible content" src="inc/images/BDPI.png">
                            <img class="hidden content" src="http://www.producao.usp.br/jspui/image/08102014imagensdocampusfotomarcossantos015.jpg">
                        </div>    

                        <div class="ui vertical stripe segment" id="search">
                            <h3 class="ui header">Buscar</h3>
                            <form role="form" action="result.php" method="get">
                                <div class="ui fluid action input">
                                    <input placeholder="Pesquisar..." type="text" name="search_index">
                                    <button class="ui button" type="submit">Buscar</button>
                                </div>
                            </form>
                        </div>

                        <div class="ui vertical stripe segment">
                            <div class="ui text container">
                                <h3 class="ui header">Alguns números</h3><br/><br/>
                                <div class="ui one statistics">
                                    <div class="statistic">
                                        <div class="value">
                                            <i class="file icon"></i> <?php contar_registros(); ?>                    
                                        </div>
                                        <div class="label">
                                            Quantidade de registros
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="ui vertical stripe segment">
                            <?php ultimos_registros(); ?>
                        </div>
            
                    </div>
                    
                    <div class="six wide column">
                        

                        
                        <div class="ui search search_unidade">
                            <div class="ui fluid icon input">
                                <form role="form" action="result.php" method="get">
                                    <div class="ui form">
                                      <div class="field prompt">
                                        <label>Escolha a Unidade USP</label>
                                        <select multiple="" class="ui dropdown" name="unidadeUSPtrabalhos[]">
                                            <option value="">Selecione a Unidade</option>
                                            <option value="CEBIMAR">Centro de Biologia Marinha (CEBIMar)</option>  
                                            <option value="CENA">Centro de Energia Nuclear na Agricultura (CENA)</option>
                                            <option value="EACH">Escola de Artes, Ciências e Humanidades (EACH)</option>
                                            <option value="ECA">Escola de Comunicações e Artes (ECA)</option>                                            
                                            <option value="EE">Escola de Enfermagem (EE)</option>
                                            <option value="EEFE">Escola de Educação Física e Esporte (EEFE)</option>
                                            <option value="EEFERP">Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)</option>
                                            <option value="EEL">Escola de Engenharia de Lorena (EEL)</option>
                                            <option value="EERP">Escola de Enfermagem de Ribeirão Preto (EERP)</option>
                                            <option value="EP">Escola Politécnica (Poli)</option>
                                            <option value="EESC">Escola de Engenharia de São Carlos (EESC)</option>
                                            <option value="ESALQ">Escola Superior de Agricultura Luiz de Queiroz (ESALQ)</option>
                                            <option value="FAU">Faculdade de Arquitetura e Urbanismo (FAU)</option>
                                            <option value="FCF">Faculdade de Ciências Farmacêuticas (FCF)</option>
                                            <option value="FCFRP">Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)</option>
                                            <option value="FD">Faculdade de Direito (FD)</option>
                                            <option value="FDRP">Faculdade de Direito de Ribeirão Preto (FDRP)</option>
                                            <option value="FEA">Faculdade de Economia, Administração e Contabilidade (FEA)</option>
                                            <option value="FEARP">Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)</option>
                                            <option value="FE">Faculdade de Educação (FE)</option>
                                            <option value="FFCLRP">Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)</option>
                                            <option value="FFLCH">Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)</option>
                                            <option value="FM">Faculdade de Medicina (FM)</option>
                                            <option value="FMRP">Faculdade de Medicina de Ribeirão Preto (FMRP)</option>
                                            <option value="FMVZ">Faculdade de Medicina Veterinária e Zootecnia (FMVZ)</option>
                                            <option value="FO">Faculdade de Odontologia (FO)</option>
                                            <option value="FOB">Faculdade de Odontologia de Bauru (FOB)</option>
                                            <option value="FORP">Faculdade de Odontologia de Ribeirão Preto (FORP)</option>
                                            <option value="FSP">Faculdade de Saúde Pública (FSP)</option>
                                            <option value="FZEA">Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)</option>
                                            <option value="IAG">Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)</option>
                                            <option value="IAU">Instituto de Arquitetura e Urbanismo (IAU)</option>
                                            <option value="IB">Instituto de Biociências (IB)</option>
                                            <option value="ICB">Instituto de Ciências Biomédicas (ICB)</option>
                                            <option value="ICMC">Instituto de Ciências Matemáticas e de Computação (ICMC)</option>
                                            <option value="IEE">Instituto de Energia e Ambiente (IEE)</option>
                                            <option value="IEA">Instituto de Estudos Avançados (IEA)</option>
                                            <option value="IEB">Instituto de Estudos Brasileiros (IEB)</option>
                                            <option value="IF">Instituto de Física (IF)</option>
                                            <option value="IFSC">Instituto de Física de São Carlos (IFSC)</option>
                                            <option value="IGc">Instituto de Geociências (IGc)</option>
                                            <option value="IME">Instituto de Matemática e Estatística (IME)</option>
                                            <option value="IMT">Instituto de Medicina Tropical de São Paulo (IMT)</option>
                                            <option value="IP">Instituto de Psicologia (IP)</option>
                                            <option value="IQ">Instituto de Química (IQ)</option>
                                            <option value="IQSC">Instituto de Química de São Carlos (IQSC)</option>
                                            <option value="IRI">Instituto de Relações Internacionais (IRI)</option>
                                            <option value="IO">Instituto Oceanográfico (IO)</option>
                                        </select>
                                      </div>
                                    </div>  
                                    <button class="ui button" type="submit"><i class="search icon" ></i></button>
                                </form>
                            </div>
                            <div class="results"></div>
                        </div>    

                        <?php criar_unidadeUSP_inicio (); ?>
                    </div>
                </div>                
            </div>
        </div>

        <?php include('inc/footer.php'); ?>
    
        <script>
        $('.ui.fluid.card')
          .popup()
        ;
        </script>
        <script>
        $('.ui.dropdown')
          .dropdown()
        ;
        </script>
        <script>
        $(document).ready(function()
        {
          $('div#logosusp').attr("style", "z-index:0;");
        });
        </script> 

    </body>
</html>
