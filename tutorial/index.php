<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>Tutorial da Biblioteca Digital da Produção Intelectual da Universidade de São Paulo</title>

        <link rel="stylesheet" href="css/reveal.css">
        <link rel="stylesheet" href="css/theme/sky.css" id="theme">

        <!-- Theme used for syntax highlighting of code -->
        <link rel="stylesheet" href="lib/css/zenburn.css">

        <!-- Printing and PDF exports -->
        <script>
            var link = document.createElement( 'link' );
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = window.location.search.match( /print-pdf/gi ) ? 'css/print/pdf.css' : 'css/print/paper.css';
            document.getElementsByTagName( 'head' )[0].appendChild( link );
        </script>
    </head>
    <body>
        <?php
        if (file_exists("../inc/analyticstracking.php")) {
            include_once "../inc/analyticstracking.php";
        }
        ?>
        <div class="reveal">
            <div class="slides">
                <section data-background="http://www.imagens.usp.br/wp-content/uploads/9072_09082011caph038.jpg">
                        <h3>Tutorial BDPI</h3>
                        <p style="font-size: 60%"><a href="//bdpi.usp.br" target="_blank" rel="noopener noreferrer nofollow">Biblioteca Digital da Produção Intelectual</a></p>
                        <p style="font-size: 50%">
                            Universidade de São Paulo<br/>
                            Sistema Integrado de Bibliotecas<br/>
                            Divisão de Gestão de Tratamento da Informação
                        </p>
                        <p style="font-size: 50%">Para visualizar, utilize a seta para a direita no teclado -></p>
                </section>
                <section>
                    <h4>O que você encontra na BDPI USP?</h4>
                    <ul>
                        <li style="font-size: 50%">Registros da Base de Produção Intelectual (Produção Científica, Técnica e Artística)</li>
                        <li style="font-size: 50%">Registros da Base de Teses e Dissertações</li>
                    </ul>
                </section>
                <section>
                    <img src="images/bdpi.usp.br.jpeg" height="650px">
                </section>
                <section>
                        <h4>Busca simples</h4>
                        <a href="//bdpi.usp.br" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_busca_simples.png" height="250px"></a>
                        <ul>
                                <li style="font-size: 50%">Operador padrão: E</li>
                                <li style="font-size: 50%">Ignora maiúsculas e minúsculas</li>
                                <li style="font-size: 50%">Busca nos campos: Título, Autor, Assunto, Resumo e Unidade USP</li>
                                <li style="font-size: 50%">Possibilidade de limitar a busca por base (Produção Intelectual ou Teses e Dissertações) e Unidade USP</li>
                                <li style="font-size: 50%">Dica: Você pode substituir uma letra por (?) ou usar o * para buscar as variações. Ex: 199? (Para pesquisar em todos os anos da década de 90) e Biblio* (Para pesquisar palabras como Biblioteca, Biblioteconomia, Bibiografia, etc..) </li>
                        </ul>

                </section>
                <section>
                        <h4>Busca avançada</h4>
                        <img src="images/bdpi_avancada.png" height="500px">
                        <ul>
                                <li style="font-size: 50%">Possibilidade de busca em campos específicos</li>
                        </ul>
                </section>
                <section>
                        <h4>Busca avançada - Por autor USP</h4>
                        <img src="images/bdpi_autorUSP.png" height="450px">
                        <ul>
                                <li style="font-size: 50%">Busca por obras de determinado autor USP por número USP, com a possibilidade de escolher os formatos ABNT, Tabela ou RIS em lote</li>
                        </ul>
                </section>
                <section>
                        <h4>Busca avançada - Busca pelo Vocabulário Controlado do SIBiUSP</h4>
                        <img src="images/bdpi_avancada2.png" height="500px">
                        <ul>
                                <li style="font-size: 50%">Possibilidade de busca em campos específicos</li>
                        </ul>
                </section>
                <section>
                        <h4>Busca pelo Vocabulário Controlado do SIBiUSP - Resultado</h4>
                        <img src="images/bdpi_vocab.png" height="200px">
                        <ul>
                                <li style="font-size: 50%">Na busca pelo Vocabulário Controlado do SIBiUSP, aparece uma caixa com a hierarquia do termo buscado</li>
                        </ul>
                </section>
                <section>
                        <h4>Todos os registros por Unidade USP</h4>
                        <img src="images/bdpi_unidadesUSP.png" height="400px">
                        <ul>
                                <li style="font-size: 50%">Acesso pelo link: UNIDADES USP, na menu superior</li>
                                <li style="font-size: 50%">Lista todas os registros em todas as bases por Unidade</li>
                        </ul>

                </section>
                <section>
                        <h4>Resultado da Busca</h4>
                        <a href="//bdpi.usp.br/result.php" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_resultado_busca.png" height="500px"></a>
                        <ul>
                                <li style="font-size: 50%">Ordenação por data</li>
                                <li style="font-size: 50%">Facetas são filtros para novas buscas</li>
                        </ul>

                </section>
                <section>
                        <h4>Facetas</h4>
                        <p><a href="//bdpi.usp.br/result.php" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_facetas.png" height="400px"></a></p>
                        <ul>
                                <li style="font-size: 50%">Ordenação por quantidade de ocorrências</li>
                                <li style="font-size: 50%">Possibilita limitar os resultados</li>
                        </ul>

                </section>
                <section>
                        <h4>Enriquecimento de Registros com dados de APIs</h4>
                        <a href="//bdpi.usp.br/result.php" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_metricas.png" height="500px"></a>
                        <ul>
                                <li style="font-size: 50%">Fontes: QUALIS, Web of Scielo, Scopus, AMiner, OpenCitations</li>
                                <li style="font-size: 50%">Preenchumento automático, seguindo critérios definidos para cada fonte</li>
                        </ul>

                </section>
                <section>
                        <h4>Enriquecimento de Registros com dados de APIs -2</h4>
                        <a href="//bdpi.usp.br/result.php" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_metricas2.png" height="500px"></a>
                        <ul>
                                <li style="font-size: 50%">Complementação com dados obtidos no momento da consulta</li>
                                <li style="font-size: 50%">Fontes: Altmetric.com, PlumX, Citações na Scopus</li>
                        </ul>

                </section>
                <section>
                        <h4>Registros</h4>
                        <a href="//bdpi.usp.br/single.php?_id=002790839"><img src="images/bdpi_ris.png" height="500px"></a>
                        <ul>
                                <li style="font-size: 50%">Exportação no formato RIS (Compatível com EndNote e Mendeley)</li>
                        </ul>
                </section>
                <section>
                        <h4>Exemplares físicos</h4>
                        <a href="//bdpi.usp.br/single.php?_id=002187951" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_exemplares.png" height="170px"></a>
                        <ul>
                                <li style="font-size: 50%">Caso as Bibliotecas possuam exemplares físicos do material, a informação sobre os exemplares será exibida</li>
                        </ul>
                </section>
                <section>
                        <h4>Como citar</h4>
                        <a href="//bdpi.usp.br/single.php?_id=002790839" target="_blank" rel="noopener noreferrer nofollow"><img src="images/bdpi_como_citar.png" height="500px"></a>
                        <ul>
                                <li style="font-size: 50%">Referências geradas automaticamente nos formatos: ABNT, APA, NLM e Vancouver</li>
                        </ul>
                </section>
                <section>
                        <h4>Contato</h4>
                        <p style="font-size: 50%">atendimento@dt.sibi.usp.br</p>
                </section>

            </div>
        </div>

        <script src="lib/js/head.min.js"></script>
        <script src="js/reveal.js"></script>

        <script>
            // More info about config & dependencies:
            // - https://github.com/hakimel/reveal.js#configuration
            // - https://github.com/hakimel/reveal.js#dependencies
            Reveal.initialize({
                dependencies: [
                    { src: 'plugin/markdown/marked.js' },
                    { src: 'plugin/markdown/markdown.js' },
                    { src: 'plugin/notes/notes.js', async: true },
                    { src: 'plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } }
                ]
            });
        </script>
    </body>
</html>
