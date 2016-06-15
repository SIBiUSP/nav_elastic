<html>
    <head>
        <title>BDPI USP - Biblioteca Digital da Produção Intelectual da Universidade de São Paulo</title>
        <?php include('inc/meta-header.php'); ?>
          <?php include('inc/functions.php'); ?>
        
        <script>
            var content = [
                { title: 'EACH', value:'Escola de Artes, Ciências e Humanidades (EACH)' },
                { title: 'ECA', value:'Escola de Comunicações e Artes (ECA)' },
                { title: 'EEFE', value:'Escola de Educação Física e Esporte (EEFE)' },
                { title: 'EE', value:'Escola de Enfermagem (EE)' },
                { title: 'EP', value:'Escola Politécnica (Poli)' },
                { title: 'FAU', value:'Faculdade de Arquitetura e Urbanismo (FAU)' },
                { title: 'FCF', value:'Faculdade de Ciências Farmacêuticas (FCF)' },
                { title: 'FD', value:'Faculdade de Direito (FD)' },
                { title: 'FEA', value:'Faculdade de Economia, Administração e Contabilidade (FEA)' },
                { title: 'FE', value:'Faculdade de Educação (FE)' },
                { title: 'FFLCH', value:'Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)' },
                { title: 'FM', value:'Faculdade de Medicina (FM)' },
                { title: 'FMVZ', value:'Faculdade de Medicina Veterinária e Zootecnia (FMVZ)' },
                { title: 'FO', value:'Faculdade de Odontologia (FO)' },
                { title: 'FSP', value:'Faculdade de Saúde Pública (FSP)' },
                { title: 'IAG', value:'Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)' },
                { title: 'IB', value:'Instituto de Biociências (IB)' },
                { title: 'ICB', value:'Instituto de Ciências Biomédicas (ICB)' },
                { title: 'IEE', value:'Instituto de Energia e Ambiente (IEE)' },
                { title: 'IEA', value:'Instituto de Estudos Avançados (IEA)' },
                { title: 'IEB', value:'Instituto de Estudos Brasileiros (IEB)' },
                { title: 'IF', value:'Instituto de Física (IF)' },
                { title: 'IGc', value:'Instituto de Geociências (IGc)' },
                { title: 'IME', value:'Instituto de Matemática e Estatística (IME)' },
                { title: 'IMT', value:'Instituto de Medicina Tropical de São Paulo (IMT)' },
                { title: 'IP', value:'Instituto de Psicologia (IP)' },
                { title: 'IQ', value:'Instituto de Química (IQ)' },
                { title: 'IRI', value:'Instituto de Relações Internacionais (IRI)' },
                { title: 'IO', value:'Instituto Oceanográfico (IO)' },
                { title: 'FOB', value:'Faculdade de Odontologia de Bauru (FOB)' },
                { title: 'EESC', value:'Escola de Engenharia de São Carlos (EESC)' },
                { title: 'IAU', value:'Instituto de Arquitetura e Urbanismo (IAU)' },
                { title: 'ICMC', value:'Instituto de Ciências Matemáticas e de Computação (ICMC)' },
                { title: 'IFSC', value:'Instituto de Física de São Carlos (IFSC)' },
                { title: 'IQSC', value:'Instituto de Química de São Carlos (IQSC)' },
                { title: 'EEL', value:'Escola de Engenharia de Lorena (EEL)' },
                { title: 'CENA', value:'Centro de Energia Nuclear na Agricultura (CENA)' },
                { title: 'ESALQ', value:'Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)' },
                { title: 'FZEA', value:'Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)' },
                { title: 'EEFERP', value:'Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)' },
                { title: 'EERP', value:'Escola de Enfermagem de Ribeirão Preto (EERP)' },
                { title: 'FCFRP', value:'Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)' },
                { title: 'FDRP', value:'Faculdade de Direito de Ribeirão Preto (FDRP)' },
                { title: 'FEARP', value:'Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)' },
                { title: 'FFCLRP', value:'Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)' },
                { title: 'FMRP', value:'Faculdade de Medicina de Ribeirão Preto (FMRP)' },
                { title: 'FORP', value:'Faculdade de Odontologia de Ribeirão Preto (FORP)' },
                { title: 'CEBIMAR', value:'Centro de Biologia Marinha (CEBIMar)' }
            ]; 
        </script>        
        
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
                                    <input class="prompt" placeholder="Pesquise pela Unidade ..." type="text" name="unidadeUSPtrabalhos">
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
        <script>
        $('.ui.search_unidade')
          .search({
            source: content,
            searchFields   : [
              'value', 'title'
            ]
          })
        ;  
        </script>        

    </body>
</html>
