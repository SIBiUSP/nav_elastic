<?php
    // Set directory to ROOT
    chdir('../');
    // Include essencial files
    include 'inc/config.php';
    
    use Gettext\Translations;
    //import from a .po file:
    $translations = Translations::fromPoFile('Locale/en_US/LC_MESSAGES/messages.po');
    //export to a php array:
    unlink('Locale/en_US/LC_MESSAGES/en.php');
    $translations->toPhpArrayFile('Locale/en_US/LC_MESSAGES/en.php');
    //and to a .mo file
    //$translations->toMoFile('Locale/en_US/LC_MESSAGES/messages.mo');
    echo 'Tradução EN_US atualizada<br/>';
    echo '<a href="index.php">Voltar</a>';
?>