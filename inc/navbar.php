<div class="uk-position-top">
<div class="uk-visible@m">
    <div class="uk-navbar uk-container uk-navbar-container uk-margin uk-navbar-transparent" uk-navbar="dropbar: true; dropbar-mode: push; mode: click">      
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                <li class="uk-active">
                    <a href="advanced_search.php"><?php echo $t->gettext('Busca avançada'); ?></a>
                </li>
                <li class="uk-active">

<a href="#modal-full" uk-toggle><?php echo $t->gettext('Unidades USP'); ?></a>

<div id="modal-full" class="uk-modal-full" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-4@s uk-flex-middle" uk-grid>
            <div class="uk-background-cover" style="background-image: url('http://www.imagens.usp.br/wp-content/uploads/Pra%C3%A7a-do-rel%C3%B3gio-Foto-Marcos-Santos-USP-Imagens-5.jpg');" uk-height-viewport></div>
            <div class="uk-padding">
                <h3><?php echo $t->gettext('Unidades USP'); ?></h3>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EACH&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Artes, Ciências e Humanidades (EACH)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;ECA&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Comunicações e Artes (ECA)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EE&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Enfermagem (EE)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EERP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Enfermagem de Ribeirão Preto (EERP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EEFE&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Educação Física e Esporte (EEFE)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EEFERP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Educação Física e Esporte de Ribeirão Preto (EEFERP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EEL&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Engenharia de Lorena (EEL)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EESC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola de Engenharia de São Carlos (EESC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;EP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola Politécnica (EP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;ESALQ&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Escola Superior de Agricultura “Luiz de Queiroz” (ESALQ)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FAU&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Arquitetura e Urbanismo (FAU)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FCF&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Ciências Farmacêuticas (FCF)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FCFRP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Ciências Farmacêuticas de Ribeirão Preto (FCFRP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FD&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Direito (FD)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FDRP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Direito de Ribeirão Preto (FDRP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FEA&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Economia, Administração e Contabilidade (FEA)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FEARP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Economia, Administração e Contabilidade de Ribeirão Preto (FEARP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FE&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Educação (FE)'); ?></a><br/>
            </div>
            <div class="uk-padding">
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FFCLRP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Filosofia, Ciências e Letras de Ribeirão Preto (FFCLRP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FFLCH&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Filosofia, Letras e Ciências Humanas (FFLCH)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FM&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina (FM)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FMRP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina de Ribeirão Preto (FMRP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FMVZ&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Medicina Veterinária e Zootecnia (FMVZ)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FO&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia (FO)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FOB&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia de Bauru (FOB)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FORP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Odontologia de Ribeirão Preto (FORP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FSP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Saúde Pública (FSP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;FZEA&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Faculdade de Zootecnia e Engenharia de Alimentos (FZEA)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IAU&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Arquitetura e Urbanismo (IAU)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IAG&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Astronomia, Geofísica e Ciências Atmosféricas (IAG)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IB&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Biociências (IB)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;ICB&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Ciências Biomédicas (ICB)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;ICMC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Ciências Matemáticas e de Computação (ICMC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IF&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Física (IF)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IFSC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Física de São Carlos (IFSC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IGC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Geociências (IGc)'); ?></a><br/>
            </div>
            <div class="uk-padding">
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IME&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Matemática e Estatística (IME)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IMT&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Medicina Tropical de São Paulo (IMT)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Psicologia (IP)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IQ&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Química (IQ)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IQSC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Química de São Carlos (IQSC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IRI&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Relações Internacionais (IRI)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IO&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto Oceanográfico (IO)'); ?></a><br/>
                <h4>Centros, Hospitais, Institutos especializados e Museus</h4>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;CEBIMAR&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Centro de Biologia Marinha (CEBIMAR)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;CDCC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Centro de Divulgação Científica e Cultural (CDCC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;CENA&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Centro de Energia Nuclear na Agricultura (CENA)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;HRAC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Hospital de Reabilitação de Anomalias Craniofaciais (HRAC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;HU&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Hospital Universitário (HU)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IEE&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Energia e Ambiente (IEE)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;IEB&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Instituto de Estudos Brasileiros (IEB)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;MAE&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Museu de Arqueologia e Etnologia (MAE)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;MAC&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Museu de Arte Contemporânea (MAC)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;MZ&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Museu de Zoologia (MZ)'); ?></a><br/>
<a href="result.php?search[]=+unidadeUSP.keyword:&quot;MP&quot;" class="uk-text-small" style="color:#333"><?php echo $t->gettext('Museu Paulista (MP)'); ?></a>
            
            </div>            
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
                    <a href="contact.php"><?php echo $t->gettext('Contato'); ?></a>
                </li>               
                <li class="uk-active">
                    <a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a>     
                </li>
                <li class="uk-active">
                    <a href="" class="" aria-expanded="false"><?php echo $t->gettext('Usuário'); ?></a>
                    <div class="uk-navbar-dropdown uk-navbar-dropdown-bottom-right" style="top: 80.1333px; left: 913.503px;">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
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
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=pt_BR">Português</a></li>
                <?php else : ?>
                    <li><a href="http://<?php echo ''.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"].''; ?>?<?php echo $_SERVER["QUERY_STRING"]; ?>&locale=en_US">English</a></li>
                <?php endif ; ?>                
                
                
                
                <li class="uk-active"><a href="http://sibi.usp.br">SIBiUSP</a></li>
            </ul>
        </div>            
    </div>
</div>


<div class="uk-hidden@m">
    <div class="uk-offcanvas-content">

        <button class="uk-button uk-button-default uk-margin-small-right" type="button" uk-toggle="target: #offcanvas-nav-primary">Menu</button>

        <div id="offcanvas-nav-primary" uk-offcanvas="overlay: true">
            <div class="uk-offcanvas-bar uk-flex uk-flex-column">

                <ul class="uk-nav uk-nav-primary uk-nav-center uk-margin-auto-vertical">
                    <li class="uk-active"><a href="index.php"><?php echo $t->gettext('Início'); ?></a></li>
                    <li class="uk-active"><a href="advanced_search.php"><?php echo $t->gettext('Busca avançada'); ?></a></li>
                    <li class="uk-nav-divider"></li>
                    <li class="uk-active"><a href="contact.php"><?php echo $t->gettext('Contato'); ?></a></li>
                    <li class="uk-active"><a href="sobre.php"><?php echo $t->gettext('Sobre'); ?></a></li>
                    <li class="uk-active"><a href="http://sibi.usp.br">SIBiUSP</a></li>
                </ul>

            </div>
        </div>
    </div>
</div>

</div> 
