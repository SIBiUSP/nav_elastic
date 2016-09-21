<?php

// if( ($_SERVER["REDIRECT_URL"] == '/') || ($_SERVER["REDIRECT_URL"] == '/share/') || empty($_SERVER["REDIRECT_URL"]) ){

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();

// Apaga todas as variáveis da sessão
if(!empty($_SESSION['oauthuserdata'])){
  unset($_SESSION['oauthuserdata']);
}

// Finalmente, destrói a sessão
session_destroy();

header('Location: ../');

