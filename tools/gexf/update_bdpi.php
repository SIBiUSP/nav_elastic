<?php
include ('../../inc/config.php');
//echo $_GET["search"];
$get_gexf = file_get_contents(''.$url_base.'/tools/gexf.php?'.$_SERVER["QUERY_STRING"].'');
$sha1 = sha1($_SERVER["QUERY_STRING"]);
file_put_contents('./data/bdpi-'.$sha1.'.gexf', $get_gexf);

?>
