<?php

    /* Configurações iniciais */
    
    $branch = "X";
    $branch_abrev = "X";

    /* Sessão */
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    /* Exibir erros */ 
    ini_set('display_errors', 1); 
    ini_set('display_startup_errors', 1); 
    error_reporting(E_ALL); 

    /* Endereço do server, sem http:// */ 
    $server = 'X'; 
    $hosts = [
        'X' 
    ]; 

    /* Variáveis de configuração */

    $index = "X";
    $type = "X";

    /* Load libraries for PHP composer */ 
    require (__DIR__.'/../vendor/autoload.php'); 
    /* Load Elasticsearch Client */ 
    $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build(); 

    /* API Key - Elsevier */ 
    $api_elsevier = 'X'; 

    $req_url = 'https://uspdigital.usp.br/wsusuario/oauth/request_token'; 
    $authurl = 'https://uspdigital.usp.br/wsusuario/oauth/authorize'; 
    $acc_url = 'https://uspdigital.usp.br/wsusuario/oauth/access_token'; 
    $api_url = 'https://uspdigital.usp.br/wsusuario/oauth/usuariousp'; 
    $callback_id = 'X'; 
    $conskey = 'X'; 
    $conssec = 'X';
    /*
      $_SESSION['oauthuserdata']->
        loginUsuario: Identificador do usuário nos sistemas USP.
        nomeUsuario: Nome do usuário.
        tipoUsuario: âIâ usuário interno que possui vinculo com a USP como: docente, funcionário ou aluno.
                     âEâ - usuário externo que não possui vinculo com a USP.
        emailPrincipalUsuario: Principal email do usuário.
        emailAlternativoUsuario: Email alternativo do usuÃ¡rio.
        emailUspUsuario: Email cadastrado na USP para o usuÃ¡rio. Retornado somente para usuÃ¡rios do tipo = âIâ
        numeroTelefoneFormatado: NÃºmero de telefone para contato formatado.
        wsuserid: Hash relativo a sessÃ£o do usuÃ¡rio para acessar WS.
        vinculo: Vinculos ativos do usuário. Lista de vínculos.  Retornado somente para usuÃ¡rios do tipo = âIâ
        tipoVinculo: Indica o tipo de vínculo da pessoa com a USP: ALUNOGR. ALUNOPOS, SERVIDOR, INSCRITOGR, etc.
        codigoSetor: CÃ³digo que identifica o setor do organograma da USP, ao qual a Pessoa pertence, preenchido conforme o tipo de vínculo.
        nomeAbreviadoSetor: Nome abreviado do setor.
        nomeSetor: Nome completo do setor.
        codigoUnidade: CÃ³digo da unidade.
        siglaUnidade: Sigla da unidade.
        nomeUnidade: Nome da unidade.
    */

    /* Definição de idioma */

    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        if (empty($_SESSION['localeToUse'])) {
            $_SESSION['localeToUse'] = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
    }
    else {
        if (empty($_SESSION['localeToUse'])) {
            $_SESSION['localeToUse'] = Locale::getDefault();
        }
    }

    if (!empty($_GET['locale'])) {
        $_SESSION['localeToUse'] = $_GET["locale"];
    } 
    
    
    use Gettext\Translator;

    //Create the translator instance
    $t = new Translator();
    
    if ($_SESSION['localeToUse'] == 'pt_BR') {
        $t->loadTranslations(__DIR__.'/../Locale/pt_BR/LC_MESSAGES/pt_BR.php');
    } else {
        $t->loadTranslations(__DIR__.'/../Locale/en_US/LC_MESSAGES/en.php');
    }


?>
