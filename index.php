<html>
    <head>
        <title>Tema do SIBiUSP</title>
        <?php include('inc/meta-header.php'); ?>
    </head>
    <body>
        <?php include('inc/barrausp.php'); ?>
        <div class="ui main container">
            <?php include('inc/header.php'); ?>
            <?php include('inc/navbar.php'); ?>
            <div id="main">

              <?php
              $ch = curl_init();
              $method = "POST";
              $url = "http://localhost/sibi/producao/_search";

              $qry = '{
                "size": 0,
                "aggs": {
                  "group_by_state": {
                    "terms": {
                      "field": "unidadeUSP"
                    }
                  }
                }
              }
              ';

              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_PORT, 9200);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
              curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);

              $result = curl_exec($ch);
              curl_close($ch);
              $data = json_decode($result, TRUE);
              var_dump($data);

              print_r($data["aggregations"]);

              ?>

            </div>
        </div>
        <?php include('inc/footer.php'); ?>
    </body>
</html>
