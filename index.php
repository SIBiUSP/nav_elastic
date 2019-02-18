<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php';
            require 'inc/meta-header.php';
        ?>
        <title><?php echo $branch; ?></title>
        <!-- Facebook Tags - START -->
        <meta property="og:locale" content="pt_BR">
        <meta property="og:url" content="//bdpi.usp.br">
        <meta property="og:title" content="<?php echo $t->gettext(''.$branch.''); ?> - <?php echo $t->gettext('Página Principal'); ?>">
        <meta property="og:site_name" content="<?php echo $t->gettext(''.$branch.''); ?>">
        <meta property="og:description" content="<?php echo $t->gettext(''.$branch_description.''); ?>">
        <meta property="og:image" content="http://www.imagens.usp.br/wp-content/uploads/USP.jpg">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
        <meta property="og:type" content="website">
        <!-- Facebook Tags - END -->

    </head>

    <body>

        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>

        <div class="uk-background-image@s uk-background-cover uk-height-viewport" >
            <div class="uk-container">
                <div class="uk-position-cover uk-inline uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-height-viewport" style="background-image: url(<?php echo ${$prefix . $background_number}; ?>);">
                    <?php require 'inc/navbar.php'; ?>
                    <div class="uk-inline-clip uk-overlay-primary uk-padding">
                    <h2 style="color:#fcb421"><?php echo $t->gettext(''.$branch.''); ?></h2>
                        <form class="uk-form-stacked" action="result.php">
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text"><?php echo $t->gettext('Termos de busca'); ?></label>
                                <div class="uk-form-controls">
                                    <input class="uk-input" id="form-stacked-text" type="text" placeholder="<?php echo $t->gettext('Pesquise por termo ou autor'); ?>" name="search[]" data-validation="required">
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-select"><?php echo $t->gettext('Selecione a base'); ?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="filter[]">
                                        <option disabled selected value><?php echo $t->gettext('Todas as bases'); ?></option>
                                        <option value="base:&quot;Produção científica&quot;" style="color:#333"><?php echo $t->gettext('Produção Científica'); ?></option>
                                        <option value="base:&quot;Teses e dissertações&quot;" style="color:#333"><?php echo $t->gettext('Teses e Dissertações'); ?></option>
                                    </select>
                                </div>
                            </div>
                             <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-select"><?php echo $t->gettext('Selecione uma Unidade USP para filtrar a busca'); ?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="form-stacked-select" name="filter[]">
                                        <option disabled selected value><?php echo $t->gettext('Todas as Unidades USP'); ?></option>
                                        <option value="unidadeUSP:&quot;EACH&quot;" style="color:#333"><?php echo $t->gettext('Escola de Artes, Ciências e Humanidades (EACH)'); ?></option>
                                        <option value="unidadeUSP:&quot;ECA&quot;" style="color:#333"><?php echo $t->gettext('Escola de Comunicações e Artes (ECA)'); ?></option>
                                        <option value="unidadeUSP:&quot;EE&quot;" style="color:#333"><?php echo $t->gettext('Escola de Enfermagem (EE)'); ?></option>
                                        <option value="unidadeUSP:&quot;EERP&quot;" style="color:#333"><?php echo $t->gettext('Escola de Enfermagem de Ribeirão Preto (EERP)'); ?></option>
                                        <option value="unidadeUSP:&quot;EEFE&quot;" style="color:#333"><?php echo $t->gettext('Escola de Educação Física e Esporte (EEFE)'); ?></option>
                                        <option value="unidadeUSP:&quot;EEFERP&quot;" style="color:#333"><?php echo $t->gettext('Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)'); ?></option>
                                        <option value="unidadeUSP:&quot;EEL&quot;" style="color:#333"><?php echo $t->gettext('Escola de Engenharia de Lorena (EEL)'); ?></option>
                                        <option value="unidadeUSP:&quot;EESC&quot;" style="color:#333"><?php echo $t->gettext('Escola de Engenharia de São Carlos (EESC)'); ?></option>
                                        <option value="unidadeUSP:&quot;EP&quot;" style="color:#333"><?php echo $t->gettext('Escola Politécnica (EP)'); ?></option>
                                        <option value="unidadeUSP:&quot;ESALQ&quot;" style="color:#333"><?php echo $t->gettext('Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)'); ?></option>
                                        <option value="unidadeUSP:&quot;FAU&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Arquitetura e Urbanismo (FAU)'); ?></option>
                                        <option value="unidadeUSP:&quot;FCF&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Ciências Farmacêuticas (FCF)'); ?></option>
                                        <option value="unidadeUSP:&quot;FCFRP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FD&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Direito (FD)'); ?></option>
                                        <option value="unidadeUSP:&quot;FDRP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Direito de Ribeirão Preto (FDRP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FEA&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Economia, Administração e Contabilidade (FEA)'); ?></option>
                                        <option value="unidadeUSP:&quot;FEARP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FE&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Educação (FE)'); ?></option>
                                        <option value="unidadeUSP:&quot;FFCLRP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FFLCH&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)'); ?></option>
                                        <option value="unidadeUSP:&quot;FM&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina (FM)'); ?></option>
                                        <option value="unidadeUSP:&quot;FMRP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina de Ribeirão Preto (FMRP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FMVZ&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina Veterinária e Zootecnia (FMVZ)'); ?></option>
                                        <option value="unidadeUSP:&quot;FO&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia (FO)'); ?></option>
                                        <option value="unidadeUSP:&quot;FOB&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia de Bauru (FOB)'); ?></option>
                                        <option value="unidadeUSP:&quot;FORP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia de Ribeirão Preto (FORP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FSP&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Saúde Pública (FSP)'); ?></option>
                                        <option value="unidadeUSP:&quot;FZEA&quot;" style="color:#333"><?php echo $t->gettext('Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)'); ?></option>
                                        <option value="unidadeUSP:&quot;IAU&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Arquitetura e Urbanismo (IAU)'); ?></option>
                                        <option value="unidadeUSP:&quot;IAG&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)'); ?></option>
                                        <option value="unidadeUSP:&quot;IB&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Biociências (IB)'); ?></option>
                                        <option value="unidadeUSP:&quot;ICB&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Ciências Biomédicas (ICB)'); ?></option>
                                        <option value="unidadeUSP:&quot;ICMC&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Ciências Matemáticas e de Computação (ICMC)'); ?></option>
                                        <option value="unidadeUSP:&quot;IF&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Física (IF)'); ?></option>
                                        <option value="unidadeUSP:&quot;IFSC&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Física de São Carlos (IFSC)'); ?></option>
                                        <option value="unidadeUSP:&quot;IGC&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Geociências (IGc)'); ?></option>
                                        <option value="unidadeUSP:&quot;IME&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Matemática e Estatística (IME)'); ?></option>
                                        <option value="unidadeUSP:&quot;IMT&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Medicina Tropical de São Paulo (IMT)'); ?></option>
                                        <option value="unidadeUSP:&quot;IP&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Psicologia (IP)'); ?></option>
                                        <option value="unidadeUSP:&quot;IQ&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Química (IQ)'); ?></option>
                                        <option value="unidadeUSP:&quot;IQSC&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Química de São Carlos (IQSC)'); ?></option>
                                        <option value="unidadeUSP:&quot;IRI&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Relações Internacionais (IRI)'); ?></option>
                                        <option value="unidadeUSP:&quot;IO&quot;" style="color:#333"><?php echo $t->gettext('Instituto Oceanográfico (IO)'); ?></option>
                                        <option disabled value><?php echo $t->gettext('Centros, Hospitais, Institutos especializados e Museus'); ?></option>
                                        <option value="unidadeUSP:&quot;CEBIMAR&quot;" style="color:#333"><?php echo $t->gettext('Centro de Biologia Marinha (CEBIMAR)'); ?></option>
                                        <option value="unidadeUSP:&quot;CDCC&quot;" style="color:#333"><?php echo $t->gettext('Centro de Divulgação Científica e Cultural (CDCC)'); ?></option>
                                        <option value="unidadeUSP:&quot;CENA&quot;" style="color:#333"><?php echo $t->gettext('Centro de Energia Nuclear na Agricultura (CENA)'); ?></option>
                                        <option value="unidadeUSP:&quot;HRAC&quot;" style="color:#333"><?php echo $t->gettext('Hospital de Reabilitação de Anomalias Craniofaciais (HRAC)'); ?></option>
                                        <option value="unidadeUSP:&quot;HU&quot;" style="color:#333"><?php echo $t->gettext('Hospital Universitário (HU)'); ?></option>
                                        <option value="unidadeUSP:&quot;IEE&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Energia e Ambiente (IEE)'); ?></option>
                                        <option value="unidadeUSP:&quot;IEB&quot;" style="color:#333"><?php echo $t->gettext('Instituto de Estudos Brasileiros (IEB)'); ?></option>
                                        <option value="unidadeUSP:&quot;MAE&quot;" style="color:#333"><?php echo $t->gettext('Museu de Arqueologia e Etnologia (MAE)'); ?></option>
                                        <option value="unidadeUSP:&quot;MAC&quot;" style="color:#333"><?php echo $t->gettext('Museu de Arte Contemporânea (MAC)'); ?></option>
                                        <option value="unidadeUSP:&quot;MZ&quot;" style="color:#333"><?php echo $t->gettext('Museu de Zoologia (MZ)'); ?></option>
                                        <option value="unidadeUSP:&quot;MP&quot;" style="color:#333"><?php echo $t->gettext('Museu Paulista (MP)'); ?></option>

                                    </select>
                                </div>
                             </div>
                             <div class="uk-child-width-1-2 uk-text-center" uk-grid>
                                <div><button class="uk-button uk-button-primary"><?php echo $t->gettext('Buscar'); ?></button></div>
                                <div><button class="uk-button uk-button-danger" type="reset" value="Reset"><?php echo $t->gettext('Limpar formulário'); ?></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span><?php echo $t->gettext('Mais informações'); ?></span></h1>
            <div class="uk-child-width-expand@s uk-text-center" uk-grid>
                <div>
                    <div class="uk-card">
                        <h3 class="uk-card-title"><?php echo $t->gettext('Bases'); ?></h3>
                        <ul class="uk-list uk-list-divider">
                            <?php Homepage::baseInicio(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <div class="uk-section uk-container">
            <h1 class="uk-heading-line uk-text-center"><span><?php echo $t->gettext('Últimos registros'); ?></span></h1>
            <?php Homepage::ultimosRegistros();?>
        </div>

<?php require 'inc/footer.php'; ?>

        </div>


<?php require 'inc/offcanvas.php'; ?>


    </body>
</html>
