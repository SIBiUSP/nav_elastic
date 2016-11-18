<?php

/* Load libraries for PHP composer */ 
require (__DIR__.'/vendor/autoload.php'); 

use Gettext\Translations;

//import from a .po file:
$translations = Translations::fromPoFile('Locale/pt_BR/LC_MESSAGES/messages.po');

//export to a php array:
unlink('Locale/pt_BR/LC_MESSAGES/pt_BR.php');
$translations->toPhpArrayFile('Locale/pt_BR/LC_MESSAGES/pt_BR.php');

//and to a .mo file
//$translations->toMoFile('Locale/pt_BR/LC_MESSAGES/messages.mo');

echo 'Tradução PT_BR atualizada<br/>';

echo '<a href="index.php">Voltar</a>';

?>