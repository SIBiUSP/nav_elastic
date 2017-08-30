<?php
/*
*      config.ws.php
*
*      Copyright 2015 diego <tematres@r020.com.ar>
*
*      This program is free software; you can redistribute it and/or modify
*      it under the terms of the GNU General Public License as published by
*      the Free Software Foundation; either version 2 of the License, or
*      (at your option) any later version.
*
*      This program is distributed in the hope that it will be useful,
*      but WITHOUT ANY WARRANTY; without even the implied warranty of
*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*      GNU General Public License for more details.
*
*      You should have received a copy of the GNU General Public License
*      along with this program; if not, write to the Free Software
*      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
*      MA 02110-1301, USA.

********************************************************************************************
CONFIGURATION
***************************************************************************************
*/
$URL_BASE='';
$lang_tematres='';
$CFG["ENCODE"]='UTF-8';
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
  ini_set("display_errors", 1);
// lang :
$lang_tematres = "pt_BR" ;


/*
 * Servers configuration
 */

$CFG_VOCABS[1]["ALIAS"]="Vocabulário Controlado do SIBiUSP";
$CFG_VOCABS[1]["URL_BASE"]="http://vocab.sibi.usp.br/pt-br/services.php";
$CFG_VOCABS[1]["ALPHA"]=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

/*fetch params*/
session_start();


/*  In almost cases, you don't need to touch nothing here!!
 *  Absolute path to the directory where are located /common/include. 
 */

date_default_timezone_set('America/Buenos_Aires');

if ( !defined('WEBTHES_ABSPATH') )
	/** Use this for version of PHP < 5.3 */
	define('WEBTHES_ABSPATH', dirname(__FILE__).'/');

if ( !defined('WEBTHES_PATH') )
	/** Use this for version of PHP < 5.3 */
	define('WEBTHES_PATH', '');

	require_once("common/lang/$lang_tematres.php") ;
	require_once('common/vocabularyservices.php');

	if ((!isset($_SESSION['_PARAMS'])) || ($_GET["lc"]==1))
	{
		$_SESSION['_PARAMS']["target_x"] = $_GET["tx"];
		$_SESSION['_PARAMS']["vocab_id"] = loadVocabularyID($_GET["v"]);
		$_SESSION['_PARAMS']["URL_BASE"] = $CFG_VOCABS[$_SESSION['_PARAMS']["vocab_id"]]["URL_BASE" ];
	}

	$URL_BASE=$_SESSION['_PARAMS']["URL_BASE"];

	//$CFG_URL_PARAM["fetchTerm"]='term/';
	$CFG_URL_PARAM["fetchTerm"]='index.php?task=fetchTerm&amp;arg=';
	$CFG_URL_PARAM["URIfetchTerm"]='fetchTerm/';
	$CFG_URL_PARAM["search"]='index.php?task=search&amp;arg=';
	$CFG_URL_PARAM["letter"]='index.php?task=letter&amp;arg=';
	$CFG_URL_PARAM["url_site"]=getURLbase();	
	
	//search strings with more than x chars
	$CFG["MIN_CHAR_SEARCH"]=2;

	//enables params for task
	$CFG["ENABLE_TASK"]=array('fetchTerm','letter','fetchLast','search','fetchVocabularyData');
		


?>