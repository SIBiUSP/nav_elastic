<?php

/* Citeproc-PHP*/
require '../inc/citeproc-php/CiteProc.php';
$csl_abnt = file_get_contents('../inc/citeproc-php/style/abnt.csl');
$csl_apa = file_get_contents('../inc/citeproc-php/style/apa.csl');
$csl_nlm = file_get_contents('../inc/citeproc-php/style/nlm.csl');
$csl_vancouver = file_get_contents('../inc/citeproc-php/style/vancouver.csl');
$lang = "br";
$citeproc_abnt = new citeproc($csl_abnt, $lang);
$citeproc_apa = new citeproc($csl_apa, $lang);
$citeproc_nlm = new citeproc($csl_nlm, $lang);
$citeproc_vancouver = new citeproc($csl_nlm, $lang);
$mode = "reference";

if (isset($_GET["format"])) {

    if ($_GET["format"] == "table") {

        $file = "export_bdpi.tsv";
        header('Content-type: text/tab-separated-values; charset=utf-8');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';

        if (!empty($_GET)) {
            $result_get = get::analisa_get($_GET);
            $query = $result_get['query'];
            $limit = $result_get['limit'];
            $page = $result_get['page'];
            $skip = $result_get['skip'];

            if (isset($_GET["sort"])) {
                $query['sort'] = [
                    ['name.keyword' => ['order' => 'asc']],
                ];
            } else {
                $query['sort'] = [
                    ['datePublished.keyword' => ['order' => 'desc']],
                ];
            }

            $params = [];
            $params["index"] = $index;
            $params["type"] = $type;
            $params["size"] = 50;
            $params["scroll"] = "30s";
            $params["body"] = $query;

            $cursor = $client->search($params);
            $total = $cursor["hits"]["total"];

            $content[] = "Sysno\tDOI\tTítulo\tAutores\tFonte da publicação\tPaginação\tAno de publicação\tISSN\tLocal de publicação\tEditora\tNome do evento\tTipo de Material\tTipo de tese\tAutores USP\tNúmero USP\tUnidades USP\tDepartamentos\tInternacionalização\tAssuntos\tURL\tResumo\tResumo em inglês\tABNT\tAPA\tQualis 2016";

            foreach ($cursor["hits"]["hits"] as $r) {
                unset($fields);

                $fields[] = $r['_id'];

                if (!empty($r["_source"]['doi'])) {
                    $fields[] = $r["_source"]['doi'];
                } else {
                    $fields[] = "";
                }

                $fields[] = $r["_source"]['name'];


                foreach ($r["_source"]['author'] as $authors) {
                    if (!empty($authors["person"]["potentialAction"])) {
                      $authors_array[]= ''.$authors["person"]["name"].' ('.$authors["person"]["potentialAction"].')';
                    } else {
                      $authors_array[]= $authors["person"]["name"];
                    }



                }
                $fields[] = implode(";", $authors_array);
                unset($authors_array);

                if (!empty($r["_source"]['isPartOf']["name"])) {
                    $fields[] = $r["_source"]['isPartOf']["name"];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['isPartOf']['USP']['dados_do_periodico'])) {
                    $fields[] = $r["_source"]['isPartOf']['USP']['dados_do_periodico'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['datePublished'])) {
                    $fields[] = $r["_source"]['datePublished'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['isPartOf']['issn'])) {
                    foreach ($r["_source"]['isPartOf']['issn'] as $issn) {
                        $issn_array[]= $issn;
                    }
                    $fields[] = implode(";", $issn_array);
                    unset($issn_array);
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['publisher']['organization']['location'])) {
                    $fields[] = $r["_source"]['publisher']['organization']['location'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['publisher']['organization']['name'])) {
                    $fields[] = $r["_source"]['publisher']['organization']['name'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['releasedEvent'])) {
                    $fields[] = $r["_source"]['releasedEvent'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['type'])) {
                    $fields[] = $r["_source"]['type'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['inSupportOf'])) {
                    $fields[] = $r["_source"]['inSupportOf'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['authorUSP'])) {

                    foreach ($r["_source"]['authorUSP'] as $authorsUSP) {
                        $authorsUSP_array[]= $authorsUSP["name"];
                    }
                    $fields[] = implode(";", $authorsUSP_array);
                    unset($authorsUSP_array);

                    foreach ($r["_source"]['authorUSP'] as $numUSP) {
                        if (!empty($numUSP["codpes"])) {
                            $numUSP_array[]= $numUSP["codpes"];
                        }
                    }
                    if (!empty($numUSP_array)) {
                        $fields[] = implode(";", $numUSP_array);
                        unset($numUSP_array);
                    }

                    foreach ($r["_source"]['authorUSP'] as $unidadesUSP_aut) {
                        $unidadesUSP_array[]= $unidadesUSP_aut["unidadeUSP"];
                    }
                    $fields[] = implode(";", $unidadesUSP_array);
                    unset($unidadesUSP_array);

                    foreach ($r["_source"]['authorUSP'] as $departament_aut) {
                        if (!empty($departament_aut["departament"])) {
                            $departament_array[]= $departament_aut["departament"];
                        }
                    }
                    if (!empty($departament_array)) {
                        $fields[] = implode(";", $departament_array);
                        unset($departament_array);
                    } else {
                        $fields[] = "";
                    }

                }

                if (!empty($r["_source"]['USP']['internacionalizacao'])) {
                    $fields[] = $r["_source"]['USP']['internacionalizacao'];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['about'])) {
                    foreach ($r["_source"]['about'] as $about) {
                        $about_array[]= $about;
                    }
                    $fields[] = implode(";", $about_array);
                    unset($about_array);
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['url'])) {
                    $fields[] = $r["_source"]['url'][0];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['description'])) {
                    $fields[] = $r["_source"]['description'][0];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]['descriptionEn'])) {
                    $fields[] = $r["_source"]['descriptionEn'][0];
                } else {
                    $fields[] = "";
                }

                $data_citation = citation::citation_query($r["_source"]);
                $fields[] = $citeproc_abnt->render($data_citation, $mode);

                $fields[] = $citeproc_apa->render($data_citation, $mode);

                if (!empty($r["_source"]["USP"]["qualis"]["qualis"]["2016"])) {
                    foreach ($r["_source"]["USP"]["qualis"]["qualis"]["2016"] as $qualis) {
                        $qualis_array[] = $qualis["area_nota"];                       
                    }

                    if (!empty($qualis_array)) {
                        $fields[] = implode(";", $qualis_array);
                        unset($qualis_array);
                    } else {
                        $fields[] = "";
                    }
                } else {
                    $fields[] = "";
                }


                $content[] = implode("\t", $fields);
                unset($fields);


            }


            while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
                $scroll_id = $cursor['_scroll_id'];
                $cursor = $client->scroll(
                    [
                    "scroll_id" => $scroll_id,
                    "scroll" => "30s"
                    ]
                );

                foreach ($cursor["hits"]["hits"] as $r) {
                    unset($fields);

                    $fields[] = $r['_id'];

                    if (!empty($r["_source"]['doi'])) {
                        $fields[] = $r["_source"]['doi'];
                    } else {
                        $fields[] = "";
                    }

                    $fields[] = $r["_source"]['name'];


                    foreach ($r["_source"]['author'] as $authors) {
                        if (!empty($authors["person"]["potentialAction"])) {
                          $authors_array[]= ''.$authors["person"]["name"].' ('.$authors["person"]["potentialAction"].')';
                        } else {
                          $authors_array[]= $authors["person"]["name"];
                        }



                    }
                    $fields[] = implode(";", $authors_array);
                    unset($authors_array);

                    if (!empty($r["_source"]['isPartOf']["name"])) {
                        $fields[] = $r["_source"]['isPartOf']["name"];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['isPartOf']['USP']['dados_do_periodico'])) {
                        $fields[] = $r["_source"]['isPartOf']['USP']['dados_do_periodico'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['datePublished'])) {
                        $fields[] = $r["_source"]['datePublished'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['isPartOf']['issn'])) {
                        foreach ($r["_source"]['isPartOf']['issn'] as $issn) {
                            $issn_array[]= $issn;
                        }
                        $fields[] = implode(";", $issn_array);
                        unset($issn_array);
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['publisher']['organization']['location'])) {
                        $fields[] = $r["_source"]['publisher']['organization']['location'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['publisher']['organization']['name'])) {
                        $fields[] = $r["_source"]['publisher']['organization']['name'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['releasedEvent'])) {
                        $fields[] = $r["_source"]['releasedEvent'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['type'])) {
                        $fields[] = $r["_source"]['type'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['inSupportOf'])) {
                        $fields[] = $r["_source"]['inSupportOf'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['authorUSP'])) {

                        foreach ($r["_source"]['authorUSP'] as $authorsUSP) {
                            $authorsUSP_array[]= $authorsUSP["name"];
                        }
                        $fields[] = implode(";", $authorsUSP_array);
                        unset($authorsUSP_array);

                        foreach ($r["_source"]['authorUSP'] as $numUSP) {
                            if (!empty($numUSP["codpes"])) {
                                $numUSP_array[]= $numUSP["codpes"];
                            }
                        }
                        if (!empty($numUSP_array)) {
                            $fields[] = implode(";", $numUSP_array);
                            unset($numUSP_array);
                        }

                        foreach ($r["_source"]['authorUSP'] as $unidadesUSP_aut) {
                            $unidadesUSP_array[]= $unidadesUSP_aut["unidadeUSP"];
                        }
                        $fields[] = implode(";", $unidadesUSP_array);
                        unset($unidadesUSP_array);

                        foreach ($r["_source"]['authorUSP'] as $departament_aut) {
                            if (!empty($departament_aut["departament"])) {
                                $departament_array[]= $departament_aut["departament"];
                            }
                        }
                        if (!empty($departament_array)) {
                            $fields[] = implode(";", $departament_array);
                            unset($departament_array);
                        } else {
                            $fields[] = "";
                        }

                    }

                    if (!empty($r["_source"]['USP']['internacionalizacao'])) {
                        $fields[] = $r["_source"]['USP']['internacionalizacao'];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['about'])) {
                        foreach ($r["_source"]['about'] as $about) {
                            $about_array[]= $about;
                        }
                        $fields[] = implode(";", $about_array);
                        unset($about_array);
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['url'])) {
                        $fields[] = $r["_source"]['url'][0];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['description'])) {
                        $fields[] = $r["_source"]['description'][0];
                    } else {
                        $fields[] = "";
                    }

                    if (!empty($r["_source"]['descriptionEn'])) {
                        $fields[] = $r["_source"]['descriptionEn'][0];
                    } else {
                        $fields[] = "";
                    }

                    $data_citation = citation::citation_query($r["_source"]);
                    $fields[] = $citeproc_abnt->render($data_citation, $mode);

                    $fields[] = $citeproc_apa->render($data_citation, $mode);


                    $content[] = implode("\t", $fields);
                    unset($fields);


                }
            }
            echo implode("\n", $content);

        }


    } elseif ($_GET["format"] == "corrigedoi") {

        $file = "corrigedoi.tsv";
        header('Content-type: text/tab-separated-values; charset=utf-8');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';

        if (!empty($_GET)) {
            $query["query"]["query_string"]["query"] = "-_exists_:doi AND _exists_:USP.titleSearchCrossrefDOI";

            $params = [];
            $params["index"] = $index;
            $params["type"] = $type;
            $params["size"] = 50;
            $params["scroll"] = "30s";
            $params["_source"] = ["doi","USP.titleSearchCrossrefDOI"];
            $params["body"] = $query;

            $cursor = $client->search($params);
            $total = $cursor["hits"]["total"];

            $content[] = "Sysno\tDOI Crossref";

            while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
                $scroll_id = $cursor['_scroll_id'];
                $cursor = $client->scroll(
                    [
                    "scroll_id" => $scroll_id,
                    "scroll" => "30s"
                    ]
                );

                foreach ($cursor["hits"]["hits"] as $r) {
                    unset($fields);

                    $fields[] = $r['_id'];

                    if (!empty($r["_source"]['USP']['titleSearchCrossrefDOI'])) {
                        $fields[] = $r["_source"]['USP']['titleSearchCrossrefDOI'];
                    } else {
                        $fields[] = "";
                    }

                    $content[] = implode("\t", $fields);
                    unset($fields);


                }
            }
            echo implode("\n", $content);

        }




    } elseif ($_GET["format"] == "csvThesis") {

        $file="export_bdpi.tsv";
        header('Content-type: text/tab-separated-values; charset=utf-8');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';

        if (!empty($_GET)) {
            $result_get = get::analisa_get($_GET);
            $query = $result_get['query'];
            $limit = $result_get['limit'];
            $page = $result_get['page'];
            $skip = $result_get['skip'];

            if (isset($_GET["sort"])) {
                $query['sort'] = [
                    ['name.keyword' => ['order' => 'asc']],
                ];
            } else {
                $query['sort'] = [
                    ['datePublished.keyword' => ['order' => 'desc']],
                ];
            }

            $params = [];
            $params["index"] = $index;
            $params["type"] = $type;
            $params["size"] = 50;
            $params["scroll"] = "30s";
            $params["body"] = $query;

            $cursor = $client->search($params);
            $total = $cursor["hits"]["total"];


            echo "Sysno\tNúmero de chamada completo\tNúmero USP\tNome Citação (946a)\tNome Citação (100a)\tNome Orientador (700a)\tNúm USP Orientador (946o)\tÁrea de concentração\tPrograma Grau\tIdioma\tTítulo\tResumo português\tAssuntos português\tTítulo inglês\tResumo inglês\tAno de impressão\tLocal de impressão\tData defesa\tURL\n";

            while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
                $scroll_id = $cursor['_scroll_id'];
                $cursor = $client->scroll(
                    [
                    "scroll_id" => $scroll_id,
                    "scroll" => "30s"
                    ]
                );

                foreach ($cursor["hits"]["hits"] as $r) {

                    $fields[] = $r['_id'];
                    $fields[] = "Não foi possível coletar";

                    foreach ($r["_source"]['authorUSP'] as $numUSP_aut) {
                        if (isset($numUSP_aut["codpes"])) {
                            $fields[] = $numUSP_aut["codpes"];
                        } else {
                            $fields[] = "Não preenchido corretamente";
                        }

                        $fields[] = $numUSP_aut["name"];
                    }


                    foreach ($r["_source"]['author'] as $authors) {
                        if (empty($authors["person"]["potentialAction"])) {
                            $fields[] = $authors["person"]["name"];
                        } else {
                            $orientadores_array[] = $authors["person"]["name"];
                        }
                    }
                    if (isset($orientadores_array)) {
                        $array_orientadores = implode("; ", $orientadores_array);
                        unset($orientadores_array);
                        $fields[] = $array_orientadores;
                    } else {
                        $fields[] = "Não preenchido";
                    }

                    if (isset($r["_source"]['USP']['codpesOrientador'])) {
                        foreach ($r["_source"]['USP']['codpesOrientador'] as $codpesOrientador) {
                            $array_codpesOrientador[] = $codpesOrientador;
                        }
                    }
                    if (isset($array_codpesOrientador)) {
                        $array_codpesOrientadores = implode("; ", $array_codpesOrientador);
                        unset($array_codpesOrientador);
                        $fields[] = $array_codpesOrientadores;
                    } else {
                        $fields[] = "Não preenchido";
                    }



                    if (isset($r["_source"]['USP']['areaconcentracao'])) {
                        $fields[] = $r["_source"]['USP']['areaconcentracao'];
                    } else {
                        $fields[] = "Não preenchido";
                    }
                    if (isset($r["_source"]['inSupportOf'])) {
                        $fields[] = $r["_source"]['inSupportOf'];
                    } else {
                        $fields[] = "Não preenchido";
                    }

                    $fields[] = $r["_source"]['language'][0];
                    $fields[] = $r["_source"]['name'];

                    if (isset($r["_source"]['description'][0])) {
                        $fields[] = $r["_source"]['description'][0];
                    } else {
                        $fields[] = "Não preenchido";
                    }

                    foreach ($r["_source"]['about'] as $subject) {
                        $subject_array[]=$subject;
                    }
                    $array_subject = implode("; ", $subject_array);
                    unset($subject_array);
                    $fields[] = $array_subject;

                    if (isset($r["_source"]['alternateName'])) {
                        $fields[] = $r["_source"]['alternateName'];
                    } else {
                        $fields[] = "Não preenchido";
                    }

                    if (isset($r["_source"]['descriptionEn'])) {
                        foreach ($r["_source"]['descriptionEn'] as $descriptionEn) {
                            $descriptionEn_array[] = $descriptionEn;
                        }
                        $array_descriptionEn = implode(" ", $descriptionEn_array);
                        unset($descriptionEn_array);
                        $fields[] = $array_descriptionEn;
                    } else {
                        $fields[] = "Não preenchido";
                    }

                    $fields[] = $r["_source"]['datePublished'];

                    $fields[] = $r["_source"]['publisher']['organization']['location'];

                    if (isset($r["_source"]['dateCreated'])) {
                        $fields[] = $r["_source"]['dateCreated'];
                    }

                    if (isset($r["_source"]['url'])) {
                        foreach ($r["_source"]['url'] as $url) {
                            $url_array[] = $url;
                        }
                        $array_url = implode("| ", $url_array);
                        unset($url_array);
                        $fields[] = $array_url;
                    }


                    // $content[] = implode("\t", $fields);

                    echo implode("\t", $fields)."\n";
                    flush();

                    unset($fields);

                }
            }
            // echo implode("\n", $content);

        }

    } elseif ($_GET["format"] == "ris") {

        $file="export_bdpi.ris";
        header('Content-type: application/x-research-info-systems');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';


        $result_get = get::analisa_get($_GET);
        $query = $result_get['query'];
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 50;
        $params["scroll"] = "30s";
        $params["body"] = $query;

        $cursor = $client->search($params);
        foreach ($cursor["hits"]["hits"] as $r) {
            /* Exportador RIS */
            $record_blob[] = Exporters::RIS($r);
        }

        while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
            $scroll_id = $cursor['_scroll_id'];
            $cursor = $client->scroll(
                [
                "scroll_id" => $scroll_id,
                "scroll" => "30s"
                ]
            );

            foreach ($cursor["hits"]["hits"] as $r) {
                /* Exportador RIS */
                $record_blob[] = Exporters::RIS($r);
            }
        }
        foreach ($record_blob as $record) {
            $record_array = explode('\n', $record);
            echo implode("\n", $record_array);
        }

    } elseif ($_GET["format"] == "bibtex") {

        $file="export_bdpi.bib";
        header('Content-type: text/plain');
        header("Content-Disposition: attachment; filename=$file");



        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';


        $result_get = get::analisa_get($_GET);
        $query = $result_get['query'];
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 50;
        $params["scroll"] = "30s";
        $params["body"] = $query;

        $cursor = $client->search($params);
        foreach ($cursor["hits"]["hits"] as $r) {
            /* Exportador RIS */
            $record_blob[] = Exporters::bibtex($r);
        }

        while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
            $scroll_id = $cursor['_scroll_id'];
            $cursor = $client->scroll(
                [
                "scroll_id" => $scroll_id,
                "scroll" => "30s"
                ]
            );

            foreach ($cursor["hits"]["hits"] as $r) {
                /* Exportador RIS */
                $record_blob[] = Exporters::bibtex($r);
            }
        }

        foreach ($record_blob as $record) {
            $record_array = explode('\n', $record);
            echo "\n";
            echo "\n";
            echo implode("\n", $record_array);
        }

    } elseif ($_GET["format"] == "field") {

      $file = "export_field.tsv";
      header('Content-type: text/tab-separated-values; charset=utf-8');
      header("Content-Disposition: attachment; filename=$file");

      // Set directory to ROOT
      chdir('../');
      // Include essencial files
      include 'inc/config.php';

      if (!empty($_GET)) {
          $result_get = get::analisa_get($_GET);
          $query = $result_get['query'];
          $limit = $result_get['limit'];
          $page = $result_get['page'];
          $skip = $result_get['skip'];

          if (isset($_GET["sort"])) {
              $query['sort'] = [
                  ['name.keyword' => ['order' => 'asc']],
              ];
          } else {
              $query['sort'] = [
                  ['datePublished.keyword' => ['order' => 'desc']],
              ];
          }

          $params = [];
          $params["index"] = $index;
          $params["type"] = $type;
          $params["size"] = 50;
          $params["scroll"] = "30s";
          $params["_source"] = ["_id", $_GET['field']];
          $params["body"] = $query;

          $cursor = $client->search($params);
          $total = $cursor["hits"]["total"];

          $content[] = "ID\t" . $_GET["field"];

          foreach ($cursor["hits"]["hits"] as $r) {
              unset($fields);

              $fields[] = $r['_id'];

              if (isset( $r["_source"][$_GET["field"]])) {
                  $fields[] = $r["_source"][$_GET["field"]];
              }

              $content[] = implode("\t", $fields);
              unset($fields);


          }


          while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
              $scroll_id = $cursor['_scroll_id'];
              $cursor = $client->scroll(
                  [
                  "scroll_id" => $scroll_id,
                  "scroll" => "30s"
                  ]
              );

              foreach ($cursor["hits"]["hits"] as $r) {
                  unset($fields);

                  $fields[] = $r['_id'];

                  if (isset( $r["_source"][$_GET["field"]])) {
                      $fields[] = $r["_source"][$_GET["field"]];
                  }

                  $content[] = implode("\t", $fields);
                  unset($fields);


              }
          }
          echo implode("\n", $content);

      }




    } else {
      echo "Formato não definido";
    }}



?>
