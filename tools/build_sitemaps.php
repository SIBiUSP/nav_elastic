<?php

//$file = "sitemap.txt";

//header('Content-type: text/tab-separated-values; charset=utf-8');
//header("Content-Disposition: attachment; filename=$file");

$dir = "../sitemaps";
$filename = "sitemap.txt";

// Set directory to ROOT
chdir('../');
// Include essencial files
include 'inc/config.php';
include 'inc/functions.php';

//$query["query"]["bool"]["filter"]["term"]["unidadeUSP.keyword"] = "IFSC";

$params = [];
$params["index"] = $index;
$params["type"] = $type;
$params["size"] = 10000;
$params["scroll"] = "240s";
$params["_source"] = ["_id"];
$params["body"] = $query;

$cursor = $client->search($params);
$total = $cursor["hits"]["total"];

$i = 0;

foreach ($cursor["hits"]["hits"] as $r) {
    unset($fields);
    $fields[] = $url_base . "/item/" . $r['_id'];
    $content[] = implode("\t", $fields);
    unset($fields);
}

while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
    $scroll_id = $cursor['_scroll_id'];
    $cursor = $client->scroll(
        [
        "scroll_id" => $scroll_id,
        "scroll" => "240s"
        ]
    );

    foreach ($cursor["hits"]["hits"] as $r) {
        unset($fields);

        $fields[] = $url_base . "/item/" . $r['_id'];
        $content[] = implode("\t", $fields);
        $contentField = implode("\n", $content);

        if(0 === ($i % $params["size"])) {
           $myfile = fopen("/var/www/html/bdpi/sitemaps/sitemap".$i.".txt", "w") or die("Unable to open file!");
           fwrite($myfile, "\n". $contentField);
           fclose($myfile);
           unset($content);
        } elseif (count($cursor['hits']['hits']) < $params["size"] && count($content) + 1 == count($cursor['hits']['hits'])) {
          $myfile = fopen("/var/www/html/bdpi/sitemaps/sitemap".$i.".txt", "w") or die("Unable to open file!");
          fwrite($myfile, "\n". $contentField);
          fclose($myfile);
          unset($content);
        }
        $i++;


        unset($fields);



    }
}
