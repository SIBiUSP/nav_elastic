<?php
  $tpTitle = 'BDPI USP - Resultado da Busca';

#  include 'inc/config.php';
#  include 'inc/meta-header.php';
#  include_once 'inc/functions.php';

#  if (empty($_SESSION["citation_style"])) {
#    $_SESSION["citation_style"]="abnt";
#  }
#  if (isset($_POST["citation_style"])) {
#    $_SESSION["citation_style"] = $_POST['citation_style'];
#  }

# if (empty($_SESSION["login_role"])) {
#     $_SESSION["login_role"]="annonymous";
# }
# if (isset($_POST["login_role"])) {
#     $_SESSION["login_role"] = $_POST['login_role'];
# }

/* Citeproc-PHP*/
#  include 'inc/citeproc-php/CiteProc.php';
#  $csl = file_get_contents('inc/citeproc-php/style/'.$_SESSION["citation_style"].'.csl');
#  $lang = "br";
#  $citeproc = new citeproc($csl,$lang);
#  $mode = "reference";


#/* Pegar a URL atual */
#if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
#      $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
#} else {
#      $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?";
#}
#    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

/* Query */
if (empty($_GET)) {
      $query = json_decode('{}');
} elseif (!empty($_GET['search_index'])) {
      $has_quote = strpos($_GET['search_index'], '"');
      if ($has_quote === false) {
          $q_array = explode( ' ', $_GET['search_index'] );
          $q = '\"'.implode('\" \"',$q_array).'\"';
          
      } else {
          $q = str_replace('"', '\\"', $_GET['search_index']);
      }
      unset($_GET['search_index']);
      $consult = '';
    foreach ($_GET as $key => $value) {
          $consult .= '"'.$key.'":"'.$value.'",';
    }
      $query = json_decode('{'.$consult.'"$text": {"$search":"'.$q.'"}}');
    if ((array_key_exists("date_init", $query))||(array_key_exists("date_end", $query))) {
        if (array_key_exists("date_init", $query)) {
            $query["year"]["\$gte"] = $query["date_init"];
        } else {
            $query["year"]["\$gte"] = "1";
        }
        if (array_key_exists("date_end", $query)) {
            $query["year"]["\$lte"] = $query["date_end"];
        } else {
            $query["year"]["\$lte"] = "20500";
        }
        unset($query["date_init"]);
        unset($query["date_end"]);
    }
} else {
      $query = array();
    foreach ($_GET as $key => $value) {
          $query[$key] = $value;
    }
    if ((array_key_exists("date_init", $query))||(array_key_exists("date_end", $query))) {
        if (array_key_exists("date_init", $query)) {
            $query["year"]["\$gte"] = $query["date_init"];
        } else {
            $query["year"]["\$gte"] = "1";
        }
        if (array_key_exists("date_end", $query)) {
            $query["year"]["\$lte"] = $query["date_end"];
        } else {
            $query["year"]["\$lte"] = "20500";
        }
        unset($query["date_init"]);
        unset($query["date_end"]);
    }
}
  /* Pagination variables */
    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    $limit = 15;
    $skip = ($page - 1) * $limit;
    $next = ($page + 1);
    $prev = ($page - 1);
    $sort = array('year' => -1);
  /* Consultas */
    $query_json = json_encode($query);
    $query_new = json_decode('[{"$match":'.$query_json.'},
    {"$lookup":{"from": "producao_bdpi", "localField": "_id", "foreignField": "_id", "as": "bdpi"}},
    {"$sort":{"year":-1}},{"$skip":'.$skip.'},{"$limit":'.$limit.'}]');
    $query_count = json_decode('[{"$match":'.$query_json.'},{"$group":{"_id":null,"count":{"$sum": 1}}}]');
    $cursor = $c->aggregate($query_new);
    $total_count = $c->aggregate($query_count);
    $conta = count($total_count['result']);
if ($conta == 0) {
      $total = 0;
} else {
      $total = $total_count['result'][0]['count'];

}

?>
<script src="https://cdn.rawgit.com/mdehoog/Semantic-UI/6e6d051d47b598ebab05857545f242caf2b4b48c/dist/semantic.min.js"></script>
<!-- Altmetric Script -->
<script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
</head>
<body>
    <?php include 'inc/barrausp.php'; ?> 
  <div class="ui main container">
      <?php include 'inc/header.php'; ?> 
      <?php include 'inc/navbar.php'; ?> 
  <div class="ui main two column stackable grid">
    <div class="four wide column">
      <div class="item">
          Filtros ativos
        <div class="active content">
          <div class="ui form">
            <div class="grouped fields">
              <form method="get" action="result.php">
                <?php foreach ($_GET as $key => $value) : ?>
                  <div class="field">
                  <div class="ui checkbox">
                    <input type="checkbox" checked="checked"  name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                  <label><?php echo $value; ?></label>
                  </div>
              </div>
                <?php endforeach;?>
              <button type="submit" class="ui icon button">Retirar filtros</button>
            </form>
            </div>
          </div>
        </div>
      </div>
      <h3>Navegação</h3>
      <div class="ui fluid vertical accordion menu">
        <?php
      /* Gerar facetas */
        generateFacet($url, $c, $query, '$type', 'count', -1, 'Tipo de publicação', 50);
        generateFacet($url, $c, $query, '$unidadeUSPtrabalhos', 'count', -1, 'Unidade USP', 100);
        generateFacet($url, $c, $query, '$departamentotrabalhos', 'count', -1, 'Departamento', 50);
        generateFacet($url, $c, $query, '$subject', 'count', -1, 'Assuntos', 50);
        if (strpos($_SERVER['REQUEST_URI'], 'unidadeUSPtrabalhos') !== false) {
            generateFacet($url, $c, $query, '$authors', 'count', -1, 'Autores', 50);
        }
        generateFacet($url, $c, $query, '$year', '_id', -1, 'Ano de publicação', 50);
        generateFacet($url, $c, $query, '$language', 'count', -1, 'Idioma', 50);
        generateFacet($url, $c, $query, '$ispartof', 'count', -1, 'Obra da qual a produção faz parte', 50);
        generateFacet($url, $c, $query, '$evento', 'count', -1, 'Nome do evento', 50);
        generateFacet($url, $c, $query, '$country', 'count', -1, 'País de publicação', 50);
        ?>
      </div>
        <h3>Informações administrativas</h3>
        <div class="ui fluid vertical accordion menu">
        <?php
        generateFacetFirst($url, $c, $query, '$dataregistro', 'dataregistro', '_id', -1, 'Data de cadastramento', 50);
        generateFacet($url, $c, $query, '$dataregistro', '_id', -1, 'Alterações nos registros', 50);
        generateFacet($url, $c, $query, '$unidadeUSP', 'count', -1, 'Unidade USP - Participações', 100);
        generateFacet($url, $c, $query, '$departamento', 'count', -1, 'Departamento - Participações', 50);
        generateFacet($url, $c, $query, '$areaconcentracao', 'count', -1, 'Área de concentração', 50);
        generateFacet($url, $c, $query, '$fatorimpacto', '_id', -1, 'Fator de impacto', 50);
        generateFacet($url, $c, $query, '$grupopesquisa', 'count', -1, 'Grupo de pesquisa', 50);
        generateFacet($url, $c, $query, '$colab', 'count', -1, 'País dos autores externos à USP', 50);
        generateFacet($url, $c, $query, '$colab_int', 'count', -1, 'Colaboração - Internacionalização - Participações', 50);
        generateFacet($url, $c, $query, '$colab_int_trab', 'count', -1, 'Colaboração - Internacionalização - Trabalhos', 50);
        generateFacet($url, $c, $query, '$colab_instituicao', 'count', -1, 'Colaboração - Instituição', 50);
        generateFacet($url, $c, $query, '$colab_instituicao_trab', 'count', -1, 'Colaboração - Instituição - Trabalhos', 50);
        generateFacet($url, $c, $query, '$colab_instituicao_corrigido', 'count', -1, 'Colaboração - Instituição - Corrigido', 100);
        generateFacet($url, $c, $query, '$colab_instituicao_naocorrigido', 'count', -1, 'Colaboração - Instituição - Não corrigido', 100);
        generateFacet($url, $c, $query, '$authorUSP', 'count', -1, 'Autores USP', 50);
        generateFacet($url, $c, $query, '$codpesbusca', 'count', -1, 'Número USP', 50);
        generateFacet($url, $c, $query, '$codpes', 'count', -1, 'Número USP / Unidade', 50);
        generateFacet($url, $c, $query, '$issn_part', 'count', -1, 'ISSN do todo', 50);
        generateFacet($url, $c, $query, '$indexado', 'count', -1, 'Indexado em:', 50);
        generateFacet($url, $c, $query, '$fomento', 'count', -1, 'Agência de fomento:', 50);
        generateFacet($url, $c, $query, '$internacionalizacao', 'count', -1, 'Internacionalização', 50);

        ?>
    </div>

    <h3>Filtrar por data</h3>
    <form method="get" action="<?php echo $escaped_url; ?>">
      <div class="ui calendar" id="date_init">
        <div class="ui input left icon">
          <i class="time icon"></i>
          <input type="text" placeholder="Ano inicial" name="date_init">
        </div>
      </div>
      <div class="ui calendar" id="date_end">
        <div class="ui input left icon">
          <i class="time icon"></i>
          <input type="text" placeholder="Ano final" name="date_end">
        </div>
      </div>
        <?php foreach ($_GET as $key => $value) {
            echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
};
        ?>
        <?php if (!empty($q)) {
            echo '<input type="hidden" name="category" value="buscaindice">';
            echo '<input type="hidden" name="q" value="'.$q.'">';
}; ?>
      <button type="submit" class="ui icon button">Limitar datas</button>
    </form>
    <br/><br/>

    <div>
      <form method="post" action="report.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
        <button type="submit" name="page" class="ui icon button" value="<?php echo $escaped_url;?>">
            Gerar relatório
        </button>
      </form>
      <br/><br/>
      <form method="post" action="result.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
        <button  type="submit" name="login_role" class="ui icon button" value="admin">Admin</button>
      </form>
      <br/>
      <form method="post" action="result.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
        <button  type="submit" name="login_role" class="ui icon button" value="annonymous">Anônimo</button>
      </form>
    </div>
  </div>
  <div class="twelve wide column">



    <div class="page-header"><h3>Resultado da busca: <?php print_r($total);?> registros</h3></div></br>

    <?php
    /* Pagination - Start */
    echo '<div class="ui buttons">';
    if ($page > 1) {
        echo '<form method="post" action="'.$escaped_url.'">';
        echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
        echo '<button type="submit" name="page" class="ui labeled icon button active" value="'.$prev.'">
        <i class="left chevron icon"></i>Anterior</button>';
        echo '<button class="ui button">'.$page.' de '.ceil($total / $limit).'</button>';
        if ($page * $limit < $total) {
            echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
            Próximo
            <i class="right chevron icon"></i></button>';
        } else {
            echo '<button class="ui right labeled icon button disabled">
            Próximo
            <i class="right chevron icon"></i></button>';
        }
        echo '</form>';
    } else {
        if ($page * $limit < $total) {
            echo '<form method="post" action="'.$escaped_url.'">';
            echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
            echo '<button class="ui labeled icon button disabled"><i class="left chevron icon"></i>Anterior</button>';
            echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
            echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
            Próximo
            <i class="right chevron icon"></i></button>';
            echo '</form>';
        }
    }
    echo '</div>';
    /* Pagination - End */
    ?>

  <br/>  <br/>
<!--
  <h3> Escolha o estilo da Citação:</h3>
  <div class="ui compact menu">
    <form method="post" action="result.php?< ?php echo $_SERVER['QUERY_STRING']; ?>">
      <button  type="submit" name="citation_style" class="ui icon button" value="apa">APA</button>
    </form>
    <form method="post" action="result.php?< ?php echo $_SERVER['QUERY_STRING']; ?>">
      <button type="submit" name="citation_style" class="ui icon button" value="abnt">ABNT</button>
    </form>
    <form method="post" action="result.php?< ?php echo $_SERVER['QUERY_STRING']; ?>">
      <button type="submit" name="citation_style" class="ui icon button" value="nlm">NLM</button>
    </form>
    <form method="post" action="result.php?< ?php echo $_SERVER['QUERY_STRING']; ?>">
      <button type="submit" name="citation_style" class="ui icon button" value="vancouver">Vancouver</button>
    </form>
  </div>
-->
<div class="ui divided items">
<?php foreach ($cursor['result'] as $r) : ?>
  <div class="item">
    <div class="image">
      <h4 class="ui center aligned icon header">
        <i class="circular file icon"></i>
        <?php if (!empty($r['ispartof'])) : ?>
        <a href="result.php?ispartof=<?php echo $r['ispartof'];?>"><?php echo $r['ispartof'];?></a> |
        <?php endif; ?>
        <a href="result.php?type=<?php echo $r['type'];?>"><?php echo $r['type'];?></a>
        <br/><br/><br/>
        <a class="ui blue label" href="http://dedalus.usp.br/F/?func=direct&doc_number=<?php echo $r['_id'];?>">
            Ver no Dedalus
        </a>
          
    <?php if (!empty($r['doi'])) : ?>
    <br/><br/>
    <object height="50" style="overflow:hidden" data="http://api.elsevier.com/content/abstract/citation-count?doi=<?php echo $r['doi'][0];?>&apiKey=c7af0f4beab764ecf68568961c2a21ea&httpAccept=text/html"></object>
      <div data-badge-popover="right" data-badge-type="donut" data-doi="<?php echo $r['doi'][0];?>" data-hide-no-mentions="true" class="altmetric-embed"></div>
    <?php endif; ?>          
          
      </h4>
    </div>
    <div class="content">
      <a class="ui medium header" href="single.php?_id=<?php echo $r['_id'];?>">
            <?php echo $r['title'];?> (<?php echo $r['year']; ?>)
      </a>
    <!--List authors -->
    <div class="extra">
    <a class="ui sub header">Autores:</a>
    <?php if (!empty($r['authors'])) : ?>
        <?php foreach ($r['authors'] as $autores) : ?>
        <div class="ui label" style="color:black;">
            <i class="user icon"></i>
            <a href="result.php?authors=<?php echo $autores;?>"><?php echo $autores;?></a>
        </div>
        <?php endforeach;?>
    <?php endif; ?>
  </div>
  <div class="extra">
  <a class="ui sub header">Unidades USP:</a>
    <?php if (!empty($r['unidadeUSP'])) : ?>
    <?php $unique =  array_unique($r['unidadeUSP']); ?>
    <?php foreach ($unique as $unidadeUSP) : ?>
      <div class="ui label" style="color:black;">
          <i class="university icon"></i>
          <a href="result.php?unidadeUSP=<?php echo $unidadeUSP;?>"><?php echo $unidadeUSP;?></a>
      </div>
    <?php endforeach;?>
    <?php endif; ?>
</div>
  <div class="extra">
    <a class="ui sub header">Assuntos:</a>
    <?php if (!empty($r['subject'])) : ?>
        <?php foreach ($r['subject'] as $assunto) : ?>
        <div class="ui label" style="color:black;">
            <i class="globe icon"></i>
            <a href="result.php?subject=<?php echo $assunto;?>"><?php echo $assunto;?></a>
        </div>
        <?php endforeach;?>
    <?php endif; ?>
    <?php if (!empty($r['url'])) : ?>
        <?php foreach ($r['url'] as $url) : ?>
        <?php if ($url != '') : ?>
          <br/><br/>
        <a href="<?php echo $url;?>" target="_blank">
          <div class="ui right floated primary button">
            Acesso online
            <i class="right chevron icon"></i>
          </div>
        </a>
        <?php endif; ?>
        <?php endforeach;?>
    <?php endif; ?>
    <?php if (!empty($r['doi'])) : ?>
    <br/><br/>
    <a href="http://dx.doi.org/<?php echo $r['doi'][0];?>" target="_blank">
    <div class="ui right floated primary button">
      Acesso online
      <i class="right chevron icon"></i>
    </div></a>
    <?php endif; ?>

    <?php load_itens($r['_id']); ?>
  </div>
<!--  <div class="extra" style="color:black;">
    <h4>Como citar (< ?php echo strtoupper($_SESSION["citation_style"]); ?>)</h4>
    < ?php
    $type = get_type($r['type']);
    $author_array = array();
    foreach ($r['authors'] as $autor_citation){

      $array_authors = explode(',', $autor_citation);
      $author_array[] = '{"family":"'.$array_authors[0].'","given":"'.$array_authors[1].'"}';
    };
    $authors = implode(",",$author_array);

    if (!empty($r['ispartof'])) {
      $container = '"container-title": "'.$r['ispartof'].'",';
    } else {
      $container = "";
    };
    if (!empty($r['doi'])) {
      $doi = '"DOI": "'.$r['doi'][0].'",';
    } else {
      $doi = "";
    };

    if (!empty($r['url'])) {
      $url = '"URL": "'.$r['url'][0].'",';
    } else {
      $url = "";
    };

    if (!empty($r['publisher'])) {
      $publisher = '"publisher": "'.$r['publisher'].'",';
    } else {
      $publisher = "";
    };

    if (!empty($r['publisher-place'])) {
      $publisher_place = '"publisher-place": "'.$r['publisher-place'].'",';
    } else {
      $publisher_place = "";
    };

    $volume = "";
    $issue = "";
    $page_ispartof = "";

    if (!empty($r['ispartof_data'])) {
      foreach ($r['ispartof_data'] as $ispartof_data) {
        if (strpos($ispartof_data, 'v.') !== false) {
          $volume = '"volume": "'.str_replace("v.","",$ispartof_data).'",';
        } elseif (strpos($ispartof_data, 'n.') !== false) {
          $issue = '"issue": "'.str_replace("n.","",$ispartof_data).'",';
        } elseif (strpos($ispartof_data, 'p.') !== false) {
          $page_ispartof = '"page": "'.str_replace("p.","",$ispartof_data).'",';
        }
      }
    }

    $data = json_decode('{
                "title": "'.$r['title'].'",
                "type": "'.$type.'",
                '.$container.'
                '.$doi.'
                '.$url.'
                '.$publisher.'
                '.$publisher_place.'
                '.$volume.'
                '.$issue.'
                '.$page_ispartof.'
                "issued": {
                    "date-parts": [
                        [
                            "'.$r['year'].'"
                        ]
                    ]
                },
                "author": [
                    '.$authors.'
                ]
            }');
    $output = $citeproc->render($data, $mode);
    print_r($output)
    ?>
  </div> -->
  </div>
  </div>
<?php endforeach;?>
</div>
<?php
/* Pagination - Start */
echo '<div class="ui buttons">';
if ($page > 1) {
    echo '<form method="post" action="'.$escaped_url.'">';
    echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
    echo '<button type="submit" name="page" class="ui labeled icon button active" value="'.$prev.'">
    <i class="left chevron icon"></i>
    Anterior
    </button>';
    echo '<button class="ui button">'.$page.' de '.ceil($total / $limit).'</button>';
    if ($page * $limit < $total) {
        echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
        Próximo
        <i class="right chevron icon"></i>
        </button>';
    } else {
        echo '<button class="ui right labeled icon button disabled">
        Próximo
        <i class="right chevron icon"></i>
        </button>';
    }
    echo '</form>';
} else {
    if ($page * $limit < $total) {
        echo '<form method="post" action="'.$escaped_url.'">';
        echo '<input type="hidden" name="extra_submit_param" value="extra_submit_value">';
        echo '<button class="ui labeled icon button disabled"><i class="left chevron icon"></i>Anterior</button>';
        echo '<button class="ui button">Página: '.$page.' de '.ceil($total / $limit).'</button>';
        echo '<button type="submit" name="page" value="'.$next.'" class="ui right labeled icon button active">
        Próximo
        <i class="right chevron icon"></i>
        </button>';
        echo '</form>';
    }
}
echo '</div>';
/* Pagination - End */
?>
</div>
</div>
</div>
<?php
  include 'inc/footer.php';
?>
<script>
$('.ui.dropdown')
  .dropdown()
;
</script>
<script>
$('#date_init').calendar({
type: 'year'
});
</script>
<script>
$('#date_end').calendar({
type: 'year'
});
</script>
<script>
$('.ui.checkbox')
  .checkbox();
</script>
</body>
</html>
