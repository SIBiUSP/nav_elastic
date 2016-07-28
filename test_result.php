<!DOCTYPE html>
<?php 
    include('inc/functions.php');

    $result_get = analisa_get($_GET);
    $page = $result_get['page'];
    print_r($page);
    echo "<br/>";
    print_r($result_get['get']);
    echo "<br/>";
    print_r($result_get['new_get']);
    echo "<br/>";
    print_r($result_get['filter']);
    echo "<br/>";
    print_r($result_get['search_term']);
    echo "<br/>";
    print_r($result_get['query_complete']);
    echo "<br/>";
    print_r($result_get['query_aggregate']);
?>