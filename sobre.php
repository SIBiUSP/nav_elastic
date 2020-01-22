<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php'; 
            require 'inc/meta-header.php';
        ?>
        <title>BDPI USP - Sobre</title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>

        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-width-1-1@s uk-width-1-1@m uk-width-3-5@l uk-margin-large-top" style="position: relative; padding-bottom: 17em;">
            <h1>Sobre</h1>
            <hr class="uk-grid-divider">

            <p>O Repositório da Produção da USP é a Biblioteca Digital da Produção Intelectual da Universidade de São Paulo (BDPI), inaugurada em 22 de outubro de 2012. É o Repositório institucional e oficial da produção intelectual (científica, artística, acadêmica e técnica) da Universidade de São Paulo, em consonância com a Política de Informação da Universidade definida na <a href="http://www.leginf.usp.br/?resolucao=resolucao-no-6444-de-22-de-outubro-de-2012" target="_blank">Resolução nº 6.444</a> de outubro de 2012. É um sistema de gestão, descoberta e disseminação cujos objetivos são:</p>

            <ul class="uk-list uk-list-bullet">
                <li>Aumentar a visibilidade, acessibilidade e difusão dos resultados da atividade acadêmica e de pesquisa da USP por meio da coleta, organização e preservação em longo prazo;</li>

                <li>Facilitar a gestão e o acesso à informação sobre a produção intelectual da USP, por meio da oferta de indicadores confiáveis e validados;</li>

                <li>Integrar-se a um conjunto de iniciativas nacionais e internacionais, por meio de padrões e protocolos de integração qualificados e normalizados.</li>
            </ul>

        </div>
        <div style="position: relative; max-width: initial;">
            <?php require 'inc/footer.php'; ?>
        </div>
    <?php require 'inc/offcanvas.php'; ?>

    </body>
</html>
