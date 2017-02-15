<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php 
            include('inc/config.php');             
            include('inc/meta-header.php');
            include('inc/functions.php');
            
            if(!empty($_SESSION['oauthuserdata'])) { 
                store_user($_SESSION['oauthuserdata'],$client);
            }
        
            /* Define variables */
            define('authorUSP','authorUSP');
        ?> 
        <title>BDPI USP - Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo</title>
        <!-- Facebook Tags - START -->
        <meta property="og:locale" content="pt_BR">
        <meta property="og:url" content="http://bdpi.usp.br">
        <meta property="og:title" content="Base de Produção Intelectual da USP - Página Principal">
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
        
        
        <div class="uk-background-image@s uk-background-cover uk-height-viewport" >
            <div class="uk-container">
                <div class="uk-position-cover uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-height-viewport" style="background-image: url(http://www.imagens.usp.br/wp-content/uploads/Cientificamente_Oficina-CSI_020-16_foto-Cec%C3%ADlia-Bastos-37.jpg);">
                    <?php include('inc/navbar.php'); ?>
                    <div class="uk-overlay uk-overlay-primary">
                    <h2 style="color:#fcb421">Base de Produção Intelectual da Universidade de São Paulo</h2>
                    <p>Memória documental da produção científica, técnica e artística gerada nas Unidades da Universidade de São Paulo.</p>
                        <form class="uk-form-stacked" action="result.php">

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Termos de busca</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input" id="form-stacked-text" type="text" placeholder="<?php echo $t->gettext('Pesquise por termo ou autor'); ?>" name="search[]" data-validation="required">
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-select">Selecione a base</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="search[]">
                                        <option disabled selected value><?php echo $t->gettext('Todas as bases'); ?></option>
                                        <option value="base.keyword:&quot;Produção científica&quot;">Produção Científica</option>
                                        <option value="base.keyword:&quot;Teses e dissertações&quot;">Teses e Dissertações</option>
                                    </select>
                                </div>
                            </div>
                            <button class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom">Pesquisar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
        
        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span>Mais informações</span></h1>        
            <div class="uk-child-width-expand@s uk-text-center" uk-grid>
                <div>
                    <div class="uk-card">
                        <h3 class="uk-card-title">Unidades USP e Programas de Pós-Graduação Interunidades</h3>
                        <ul class="uk-list uk-list-divider">
                            <?php paginaInicial::unidadeUSP_inicio(); ?>
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="uk-card">
                        <h3 class="uk-card-title">Bases</h3>
                        <ul class="uk-list uk-list-divider">
                            <?php paginaInicial::base_inicio(); ?>
                        </ul>                      
                    </div>
                </div>
                <div>
                    <div class="uk-card">
                        <h3 class="uk-card-title"><?php echo $t->gettext('Estatísticas da base'); ?></h3>
                        <ul class="uk-list uk-list-divider">
                            <li><?php echo number_format(paginaInicial::contar_registros(),0,',','.'); ?> registros</li>
                            <li><?php echo number_format(paginaInicial::contar_unicos("authorUSP"),0,',','.'); ?> autores vinculados à USP</li>
                            <li><?php echo number_format(paginaInicial::contar_arquivos(),0,',','.'); ?> arquivos de texto integral</li>                        
                        </ul>
                    </div>
                </div>
            </div>
        </div>  
        
        
        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span>Últimos regístros</span></h1>
            <?php paginaInicial::ultimos_registros($client);?>
        </div>        

        <div class="uk-container uk-section uk-margin-large-bottom">
             <h1 class="uk-heading-line uk-text-center"><span>Unidades USP</span></h1>
        
<div class="uk-child-width-1-3 uk-child-width-1-6@s uk-grid-match uk-grid-small" uk-grid>
    <?php echo paginaInicial::card_unidade("CEBIMAR","Centro de Biologia Marinha (CEBIMAR)"); ?>
    <?php echo paginaInicial::card_unidade("CDCC","Centro de Divulgação Científica e Cultural (CDCC)"); ?>
    <?php echo paginaInicial::card_unidade("CENA","Centro de Energia Nuclear na Agricultura (CENA)"); ?>
    <?php echo paginaInicial::card_unidade("EACH","Escola de Artes, Ciências e Humanidades (EACH)"); ?>
    <?php echo paginaInicial::card_unidade("ECA","Escola de Comunicações e Artes (ECA)"); ?>
    <?php echo paginaInicial::card_unidade("EE","Escola de Enfermagem (EE)"); ?>
    <?php echo paginaInicial::card_unidade("EERP","Escola de Enfermagem de Ribeirão Preto (EERP)"); ?>
    <?php echo paginaInicial::card_unidade("EEFE","Escola de Educação Física e Esporte (EEFE)"); ?>
    <?php echo paginaInicial::card_unidade("EEFERP","Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)"); ?>
    <?php echo paginaInicial::card_unidade("EEL","Escola de Engenharia de Lorena (EEL)"); ?>
    <?php echo paginaInicial::card_unidade("EESC","Escola de Engenharia de São Carlos (EESC)"); ?>
    <?php echo paginaInicial::card_unidade("EP","Escola Politécnica (EP)"); ?>
    <?php echo paginaInicial::card_unidade("ESALQ","Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)"); ?>
    <?php echo paginaInicial::card_unidade("FAU","Faculdade de Arquitetura e Urbanismo (FAU)"); ?>
    <?php echo paginaInicial::card_unidade("FCF","Faculdade de Ciências Farmacêuticas (FCF)"); ?>
    <?php echo paginaInicial::card_unidade("FCFRP","Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)"); ?>
    <?php echo paginaInicial::card_unidade("FD","Faculdade de Direito (FD)"); ?>
    <?php echo paginaInicial::card_unidade("FDRP","Faculdade de Direito de Ribeirão Preto (FDRP)"); ?>
    <?php echo paginaInicial::card_unidade("FEA","Faculdade de Economia, Administração e Contabilidade (FEA)"); ?>
    <?php echo paginaInicial::card_unidade("FEARP","Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)"); ?>
    <?php echo paginaInicial::card_unidade("FE","Faculdade de Educação (FE)"); ?>
    <?php echo paginaInicial::card_unidade("FFCLRP","Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)"); ?>
    <?php echo paginaInicial::card_unidade("FFLCH","Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)"); ?>
    <?php echo paginaInicial::card_unidade("FM","Faculdade de Medicina (FM)"); ?>
    <?php echo paginaInicial::card_unidade("FMRP","Faculdade de Medicina de Ribeirão Preto (FMRP)"); ?>
    <?php echo paginaInicial::card_unidade("FMVZ","Faculdade de Medicina Veterinária e Zootecnia (FMVZ)"); ?>
    <?php echo paginaInicial::card_unidade("FO","Faculdade de Odontologia (FO)"); ?>
    <?php echo paginaInicial::card_unidade("FOB","Faculdade de Odontologia de Bauru (FOB)"); ?>
    <?php echo paginaInicial::card_unidade("FORP","Faculdade de Odontologia de Ribeirão Preto (FORP)"); ?>
    <?php echo paginaInicial::card_unidade("FSP","Faculdade de Saúde Pública (FSP)"); ?>
    <?php echo paginaInicial::card_unidade("FZEA","Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)"); ?>
    <?php echo paginaInicial::card_unidade("HRAC","Hospital de Reabilitação de Anomalias Craniofaciais (HRAC)"); ?>
    <?php echo paginaInicial::card_unidade("HU","Hospital Universitário (HU)"); ?>
    <?php echo paginaInicial::card_unidade("IAU","Instituto de Arquitetura e Urbanismo (IAU)"); ?>
    <?php echo paginaInicial::card_unidade("IAG","Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)"); ?>
    <?php echo paginaInicial::card_unidade("IB","Instituto de Biociências (IB)"); ?>
    <?php echo paginaInicial::card_unidade("ICB","Instituto de Ciências Biomédicas (ICB)"); ?>
    <?php echo paginaInicial::card_unidade("ICMC","Instituto de Ciências Matemáticas e de Computação (ICMC)"); ?>
    <?php echo paginaInicial::card_unidade("IEE","Instituto de Energia e Ambiente (IEE)"); ?>
    <?php echo paginaInicial::card_unidade("IEB","Instituto de Estudos Brasileiros (IEB)"); ?>
    <?php echo paginaInicial::card_unidade("IF","Instituto de Física (IF)"); ?>
    <?php echo paginaInicial::card_unidade("IFSC","Instituto de Física de São Carlos (IFSC)"); ?>
    <?php echo paginaInicial::card_unidade("IGC","Instituto de Geociências (IGc)"); ?>
    <?php echo paginaInicial::card_unidade("IME","Instituto de Matemática e Estatística (IME)"); ?>
    <?php echo paginaInicial::card_unidade("IMT","Instituto de Medicina Tropical de São Paulo (IMT)"); ?>
    <?php echo paginaInicial::card_unidade("IP","Instituto de Psicologia (IP)"); ?>
    <?php echo paginaInicial::card_unidade("IQ","Instituto de Química (IQ)"); ?>
    <?php echo paginaInicial::card_unidade("IQSC","Instituto de Química de São Carlos (IQSC)"); ?>
    <?php echo paginaInicial::card_unidade("IRI","Instituto de Relações Internacionais (IRI)"); ?>
    <?php echo paginaInicial::card_unidade("IO","Instituto Oceanográfico (IO)"); ?>
    <?php echo paginaInicial::card_unidade("MAE","Museu de Arqueologia e Etnologia (MAE)"); ?>
    <?php echo paginaInicial::card_unidade("MAC","Museu de Arte Contemporânea (MAC)"); ?>
    <?php echo paginaInicial::card_unidade("MZ","Museu de Zoologia (MZ)"); ?>
    <?php echo paginaInicial::card_unidade("MP","Museu Paulista (MP)"); ?>    
</div>      

                 
            
            <hr class="uk-grid-divider">
            
<?php include('inc/footer.php'); ?>

        </div>
        
        
<?php include('inc/offcanvas.php'); ?>
            
        
    </body>
</html>