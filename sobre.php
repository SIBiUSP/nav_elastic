<?php
	$sobre_pt_br = "";
	$sobre_en = "";
	
	if($about == "repositorio"){
		$sobre_pt_br = '<p>O Repositório da Produção da USP é a Biblioteca Digital da Produção Intelectual da Universidade de São Paulo (BDPI), inaugurada em 22 de outubro de 2012. É o Repositório institucional e oficial da produção intelectual (científica, artística, acadêmica e técnica) da Universidade de São Paulo, em consonância com a Política de Informação da Universidade definida na <a href="http://www.leginf.usp.br/?resolucao=resolucao-no-6444-de-22-de-outubro-de-2012" target="_blank">Resolução nº 6.444</a> de outubro de 2012. É um sistema de gestão, descoberta e disseminação cujos objetivos são:</p>

                <ul class="uk-list uk-list-bullet">
                    <li>Aumentar a visibilidade, acessibilidade e difusão dos resultados da atividade acadêmica e de pesquisa da USP por meio da coleta, organização e preservação em longo prazo;</li>

                    <li>Facilitar a gestão e o acesso à informação sobre a produção intelectual da USP, por meio da oferta de indicadores confiáveis e validados;</li>

                    <li>Integrar-se a um conjunto de iniciativas nacionais e internacionais, por meio de padrões e protocolos de integração qualificados e normalizados.</li>
                </ul>';
		$sobre_en = '<p>The Open Access Repository of the Universidade de Sao Paulo is the Digital Library of Research Production of the University, launched on October 22, 2012. It is the institutional and official repository of research publications (scientific, artistic, academic and technical) of the Universidade de São Paulo, according to Information Policy defined by the <a href="http://www.leginf.usp.br/?resolucao=resolucao-no-6444-de-22-de-outubro-de-2012" target="_blank">Resolution Nº 6,444</a> of October 2012. It is a management, discovery and dissemination system whose objectives are:</p>

                <ul class="uk-list uk-list-bullet">
                    <li>Increase the visibility, accessibility and dissemination of the results of USP\'s academic and research activity through the collection, organization and long-term preservation;</li>
                                                                                                                                                                                                                                                                 <li>Facilitate the management and access to information on USP research outcomes, by offering reliable and validated indicators;</li>

                    <li>Integrate with a set of national and international initiatives, through qualified and standardized integration standards and protocols.</li>
                </ul>';
	} elseif ($about == "bdta") {
		$sobre_pt_br = "";
		$sobre_en = "";
	}

?>


<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
    <head>
        <?php
            require 'inc/config.php'; 
            require 'inc/meta-header.php';
        ?>
        <title>BDPI USP - <?php echo $t->gettext('Sobre'); ?></title>
    </head>

    <body style="height: 100vh; min-height: 45em; position: relative;">
        <?php
        if (file_exists("inc/analyticstracking.php")) {
            include_once "inc/analyticstracking.php";
        }
        ?>

        <?php require 'inc/navbar.php'; ?>
        <div class="uk-container uk-width-1-1@s uk-width-1-1@m uk-width-3-5@l uk-margin-large-top" style="position: relative; padding-bottom: 17em;">
            <h1><?php echo $t->gettext('Sobre'); ?></h1>
            <hr class="uk-grid-divider">
	    <?php
            $sobre_pt_br = '';
            $sobre_en = '';

            if($about == "repositorio"){
		$sobre_pt_br = '
				<p>O Repositório da Produção da USP é a Biblioteca Digital da Produção Intelectual da Universidade de São Paulo (BDPI), inaugurada em 22 de outubro de 2012. É o Repositório institucional e oficial da produção intelectual (científica, artística, acadêmica e técnica) da Universidade de São Paulo, em consonância com a Política de Informação da Universidade definida na <a href="http://www.leginf.usp.br/?resolucao=resolucao-no-6444-de-22-de-outubro-de-2012" target="_blank">Resolução nº 6.444</a> de outubro de 2012. É um sistema de gestão, descoberta e disseminação cujos objetivos são:</p>

							                    <ul class="uk-list uk-list-bullet">
									                        <li>Aumentar a visibilidade, acessibilidade e difusão dos resultados da atividade acadêmica e de pesquisa da USP por meio da coleta, organização e preservação em longo prazo;</li>

                    <li>Facilitar a gestão e o acesso à informação sobre a produção intelectual da USP, por meio da oferta de indicadores confiáveis e validados;</li>

                    <li>Integrar-se a um conjunto de iniciativas nacionais e internacionais, por meio de padrões e protocolos de integração qualificados e normalizados.</li>
                </ul>';
		$sobre_en = '<p>The Open Access Repository of the Universidade de Sao Paulo is the Digital Library of Research Production of the University, launched on October 22, 2012. It is the institutional and official repository of research publications (scientific, artistic, academic and technical) of the Universidade de São Paulo, according to Information Policy defined by the <a href="http://www.leginf.usp.br/?resolucao=resolucao-no-6444-de-22-de-outubro-de-2012" target="_blank">Resolution Nº 6,444</a> of October 2012. It is a management, discovery and dissemination system whose objectives are:</p>

			                <ul class="uk-list uk-list-bullet">
					                    <li>Increase the visibility, accessibility and dissemination of the results of USP\'s academic and research activity through the collection, organization and long-term preservation;</li>
							                                                                                                                                                                                                                                                                     <li>Facilitate the management and access to information on USP research outcomes, by offering reliable and validated indicators;</li>

                    <li>Integrate with a set of national and international initiatives, through qualified and standardized integration standards and protocols.</li>
                </ul>';
	} elseif ($about == "bdta") {
		$sobre_pt_br = '
			<p>A Biblioteca Digital de Trabalhos Acadêmicos da USP proporciona à sociedade em geral o acesso ao texto completo dos Trabalhos de Conclusão de Curso da Universidade. Inclui em seu acervo: Trabalho de Conclusão de Curso de Graduação, Trabalho de Conclusão de Curso de Especialização (MBA), Trabalho de Conclusão de Residência (TCR) da Universidade de São Paulo.
A BDTA atende à <strong><a href="http://www.leginf.usp.br/?resolucao=resolucao-cocex-cog-no-7497-de-09-de-abril-de-2018" title="Resolução CoCEx-CoG número 7497" target="_blank">Resolução CoCEx-CoG Nº 7497, de 09 de abril de 2018</a></strong>, que Regulamenta a disponibilização de trabalhos de conclusão de curso ou outros trabalhos acadêmicos equivalentes na Biblioteca Digital de Trabalhos Acadêmicos da Universidade de São Paulo.</p>

			<p>A Pró-Reitora de Cultura e Extensão Universitária e o Pró-Reitor de Graduação, no uso de suas atribuições legais, e tendo em vista o deliberado pelo Conselho de Cultura e Extensão Universitária, em sessão realizada em 24 de agosto de 2017, pelo Conselho de Graduação, em sessão realizada em 21 de setembro de 2017 e pela Comissão de Legislação e Recursos", baixou a Resolução em sessão realizada em 20 de fevereiro de 2018.</p>

			<p>Essa normativa determina que todos os cursos de Graduação, cursos de Especialização e atividades de Residência uniprofissionais ou multiprofissionais da Universidade de São Paulo que adotam apresentação de trabalho de conclusão de curso ou monografia podem ter seus trabalhos depositados na BDTA, de acordo com algumas condições:<br/><br/>

			Artigo 1º da Resolução: a banca examinadora ou instância avaliadora equivalente deverá informar se recomenda ou não a inclusão dos referidos documentos na Biblioteca Digital de Trabalhos Acadêmicos (BDTA) da USP.<br/>
			<ul>
				<li>§ 1º  Para os cursos de Graduação, além da recomendação da banca examinadora, a publicação dependerá de homologação pela Comissão de Graduação.</li>
				<li>§ 2º  Para os cursos de Especialização e atividades de Residência uniprofissionais ou multiprofissionais, além da recomendação da banca examinadora, a publicação dependerá de ratificação pelo Coordenador do Curso e posterior homologação pela Comissão de Cultura e Extensão Universitária da Unidade e/ou órgão equivalente.</li>
				<li>§ 3º  Os autores dos trabalhos indicados e homologados deverão receber ciência antes do encaminhamento para publicação.</li>
				<li>§ 4º  Ficará a cargo da Unidade a definição do fluxo completo, desde a submissão até a publicação. A Biblioteca da Unidade será a responsável pela catalogação do conteúdo.</li>
			</ul>
			</p>

			<p>Nesse sentido, a Resolução estabelece os princípios para o depósito legal de Monografias e Trabalhos de Conclusão de Cursos de Graduação e Pós-Graduação Lato Sensu da Universidade de São Paulo na Biblioteca da Biblioteca Digital de Trabalhos Acadêmicos (BDTA).</p>

			<p>A Agência USP de Gestão da Informação Acadêmica (AGUIA), que incorporou as atividades do antigo Sistema Integrado de Bibliotecas (SIBiUSP), é responsável pela hospedagem, manutenção e aprimoramento da BDTA, além da capacitação das equipes bibliotecárias das Unidades na execução do fluxo de submissão.</p>
		';
		$sobre_en = '
			<p>The USP Digital Library of Academic Works provides society in general the access to the full text of the University\'s Course Completion Works. It includes in its collection: Graduation Course Conclusion Work, Specialization Course Conclusion Work (MBA), Residency Conclusion Work (TCR) of the University of São Paulo.</p>

			<p>BDTA complies with <strong><a href="http://www.leginf.usp.br/?resolucao=resolucao-cocex-cog-no-7497-de-09-de-abril-de-2018" title="CoCEx-CoG Resolution No. 7497, of April 9, 2018" target="_blank">CoCEx-CoG Resolution No. 7497, of April 9, 2018</a></strong> which Regulates the availability of course completion papers or other equivalent academic papers in the Library Digital of Academic Works of the University of São Paulo.</p>

			<p>The Pro-Rector of Culture and University Extension and the Pro-Rector of Graduation, in the use of their legal attributions, and in view of the resolution of the Council of Culture and University Extension, in a session held on August 24, 2017, by the Council of Graduation, in a session held on September 21, 2017 and by the Legislation and Resources Commission ", downloaded the Resolution in a session held on February 20, 2018.</p>

			<p>This regulation determines that all Undergraduate courses, Specialization courses and uniprofessional or multiprofessional residency activities at the University of São Paulo that adopt the presentation of a course conclusion paper or monograph may have their work deposited at BDTA, according to some conditions:<br/><br/>

			Article 1 of the Resolution: the examining board or equivalent evaluating body must inform whether or not it recommends the inclusion of these documents in USP\'s Digital Library of Academic Works (BDTA).<br/>

			<ul>
				<li>§ 1 - For Undergraduate courses, in addition to the recommendation of the examining board, publication will depend on approval by the Undergraduate Committee.</li>
				<li>§ 2 - For the Specialization courses and uniprofessional or multiprofessional Residence activities, in addition to the recommendation of the examining board, publication will depend on ratification by the Course Coordinator and subsequent approval by the Unit\'s Culture and University Extension Committee and / or equivalent body.</li>
				<li>§ 3 - The authors of the indicated and homologated works must be informed before being sent for publication.</li>
				<li>§ 4 - The Unit will be responsible for defining the complete flow, from submission to publication. The Unit Library will be responsible for cataloging the content.</li>
			</ul>
			</p>
	
			<p>In this sense, the Resolution establishes the principles for the legal deposit of Monographs of Lato Sensu Graduation and Post-Graduation from the Universidade de São Paulo at the Library of the Digital Library of Academic Works (BDTA).</p>

			<p>The USP Academic Information Management Agency (AGUIA), which incorporated the activities of the old Integrated Library System (SIBiUSP), is responsible for hosting, maintaining and improving the BDTA, in addition to training the Units\' librarian teams in the execution of the flow of information submission.</p>
		';
        }

	if($locale == "pt_BR"){
		echo $sobre_pt_br;	
	} else {
		echo $sobre_en;
	}
        ?>
        </div>
        <div style="position: relative; max-width: initial;">
            <?php require 'inc/footer.php'; ?>
        </div>
    <?php require 'inc/offcanvas.php'; ?>

    </body>
</html>
