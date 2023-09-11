<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php'; 
            require 'inc/meta-header.php';
        ?>
	<title><?=$branch_abbrev?> - <?=$t->gettext('Sobre')?></title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>

        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-width-1-1@s uk-width-1-1@m uk-width-3-5@l uk-margin-large-top" style="position: relative; padding-bottom: 23em;">
            <h1><?=$t->gettext('Sobre')?></h1>
	    <hr class="uk-grid-divider">



	<?php
	/*************************************************
	 ************ Repositório pt_BR ******************
	 ************************************************/
	?>
	<?php if(!is_bdta() && $locale == 'pt_BR'): ?>
		<p>O Repositório da Produção USP é a Biblioteca Digital da Produção Intelectual da Universidade de São Paulo (BDPI), inaugurada em 22 de outubro de 2012. É o Repositório institucional e oficial da Universidade de São Paulo que concentra o registro e armazena as publicações oriundas de pesquisa e a produção científica, artística, acadêmica e técnica em formato digital de seus autores, departamentos, unidades, institutos, centros, museus e órgãos centrais.</p>
                <p>Tem como objetivos:</p>

                <ul class="uk-list uk-list-bullet">
			<li>Aumentar a visibilidade, acessibilidade e difusão de conteúdos digitais dos resultados das atividades acadêmicas e de pesquisa da universidade por meio da coleta, organização, registro e preservação de sua memória institucional;</li>
    			<li>Facilitar a gestão e o acesso à informação sobre a produção de pesquisa da USP, por meio da oferta de indicadores confiáveis e validados;</li>
			<li>Contribuir para a preservação do conhecimento produzido na universidade, seu uso e impacto científico, acadêmico e social, integrando-se a outras iniciativas nacionais e internacionais.</li>
		</ul>



	<?php
	/*************************************************
	 ************ Repositório en_US ******************
	 ************************************************/
	?>
	<?php elseif(!is_bdta() && $locale == 'en_US'): ?> 
		<p>The Open Access Repository of the Universidade de Sao Paulo is the Digital Library of Research Production of the university, launched on October 22, 2012.  It is the institutional and official repository of research publications and scientific, artistic, academic and technical production in digital format by its authors, departments, units, institutes, centers, museums and central bodies.</p><p>Its objectives are:</p>

                <ul class="uk-list uk-list-bullet">
			<li>To increase the visibility, accessibility and dissemination of digital contents of the results of research and academic activities through the collection, organization, registration and preservation of its institutional memory; </li>
			<li>To facilitate the management and access to information on USP scientific production, through the offer of reliable and validated indicators;</li>
			<li>To contribute to the preservation of knowledge produced at the university, its use and scientific, academic and social impact, integrating with other national and international initiatives.</li>
                </ul>



	<?php
	/*************************************************
	 ******************* BDTA pt_BR ******************
	 ************************************************/
	?>
	<?php elseif(is_bdta() && $locale == 'pt_BR'): ?>

		<p>A Biblioteca Digital de Trabalhos Acadêmicos da USP (BDTA) é um repositório online que oferece acesso aberto ao texto completo de Trabalhos de Conclusão de Cursos nos seguintes Graus: Graduação, Especialização, Master of Business Administration (MBA), Residência Médica e Aperfeiçoamento Profissional concluídos na Universidade de São Paulo.</p>

		<p>A BDTA atende à <a href="http://www.leginf.usp.br/?resolucao=resolucao-cocex-cog-no-7497-de-09-de-abril-de-2018" title="Resolução CoCEx-CoG número 7497, de 9 de abril de 2018" target="_blank">Resolução CoCEx-CoG Nº 7497</a>, de 09 de abril de 2018, que “Regulamenta a disponibilização de trabalhos de conclusão de curso ou outros trabalhos acadêmicos equivalentes na Biblioteca Digital de Trabalhos Acadêmicos da Universidade de São Paulo”.</p>



	<?php
	/*************************************************
	 ******************* BDTA en_US *****************
	 ************************************************/
	?>
	<?php elseif(is_bdta() && $locale == 'en_US'): ?>
		<p>The Digital Library of Academic Works at USP (BDTA) is an online repository that provides open access to the full text of thesis in the following degrees: Undergraduate, Specialization, Master of Business Administration (MBA), Medical Residency, and Professional Enhancement, all completed at the University of São Paulo.</p>

		<p>BDTA complies with <a href="http://www.leginf.usp.br/?resolucao=resolucao-cocex-cog-no-7497-de-09-de-abril-de-2018" title="Resolution CoCEx-CoG No. 7497, dated April 9, 2018" target="_blank">Resolution CoCEx-CoG No. 7497, dated April 9, 2018</a>, which "Regulates the availability of course completion works or equivalent academic works in the Digital Library of Academic Works at the University of São Paulo."</p>


       <?php endif; ?>
        </div>
        <div style="position: relative; max-width: initial;">
            <?php require 'inc/footer.php'; ?>
        </div>
    <?php require 'inc/offcanvas.php'; ?>

    </body>
</html>
