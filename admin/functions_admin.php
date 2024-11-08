<?php
/**
 ** Gerenciamento de Usuários
 **/
global $path_users;
global $path_bibliotecas;
global $path_unidades;
$path_users = __DIR__."/../inc/staff.txt";
$path_bibliotecas = __DIR__."/../inc/bibliotecas.txt";
$path_unidades = __DIR__."/../inc/unidades.txt";

function get_content($path, $type = 'usuarios'){
        $content = array();
	$order = 'unidade';
        $arquivo = fopen($path,'r');
        if ($arquivo == false) die('O arquivo não existe.');
        while(true) {
                $linha = fgets($arquivo);
                if ($linha==null) break;
                $contenttemp = explode(";", $linha);
		if($type == 'usuarios'){
	                array_push($content, ["nusp" => $contenttemp[0], "nome" => $contenttemp[1], "unidade" => $contenttemp[2] ]);
			$order = "unidade";
		}
		elseif($type == 'bibliotecas'){
	                array_push($content, ["sigla_biblioteca" => $contenttemp[0], "nome_biblioteca" => $contenttemp[1] ]);
			$order = "sigla";
		}
		elseif($type == 'unidades'){
	                array_push($content, ["sigla_unidade" => $contenttemp[0], "nome_unidade" => $contenttemp[1] ]);
			$order = "sigla";
		}
        }
        fclose($arquivo);
        $content = order_content($content, $type, $order);
        return $content;
}

function order_content($content, $type, $order = "nome"){
        foreach ($content as $key => $row) {
		switch($type){
			case 'usuarios':
                		$nusp[$key]  = $row['nusp'];
		                $nome[$key] = $row['nome'];
        		        $unidade[$key] = $row['unidade'];
				break;
			case 'bibliotecas':
				$sigla[$key] = $row['sigla_biblioteca'];
	        	        $nome[$key] = $row['nome_biblioteca'];
				break;
			case 'unidades':
				$sigla[$key] = $row['sigla_unidade'];
	        	        $nome[$key] = $row['nome_unidade'];
				break;
			default:
				break;
	        }
	}
	switch($order){
        	case 'nome':
               		array_multisort($nome, SORT_ASC, SORT_STRING, $content);
			break;
	        case 'unidade':
        	        array_multisort($unidade, SORT_ASC, SORT_STRING, $nome, SORT_ASC, SORT_STRING, $content);
			break;
	        case 'sigla':
        	        array_multisort($sigla, SORT_ASC, SORT_STRING, $nome, SORT_ASC, SORT_STRING, $content);
			break;
	}
        return $content;
}

function grava_arquivo($conteudo, $path_arquivo){
        $arquivo = fopen($path_arquivo,'w+');
	if ($arquivo == false) die('O arquivo não existe.');
	$conteudo_txt = '';
	foreach($conteudo as $registro){
		$conteudo_txt .= monta_registro($registro);
        }
	fwrite($arquivo, $conteudo_txt);
        fclose($arquivo);
}

function monta_registro($registro){
	if(is_array($registro)){
	        foreach($registro as $item => $valor){
	                $conteudo_txt .= $valor . ";";
                }
                return substr($conteudo_txt,0,-1);
        } else {
                return $valor;
        }
}

function set_content($path, $type){
        $content = get_content($path, $type);
	$order = '';
	if($type == 'usuarios'){
	        array_push($content, ["nusp" => $_POST["nusp"], "nome" => $_POST["nome"], "unidade" => $_POST["unidade"].PHP_EOL ]);
		$order = "unidade";
	}
	elseif($type == 'bibliotecas'){
	        array_push($content, ["sigla_biblioteca" => $_POST["sigla_biblioteca"], "nome_biblioteca" => $_POST["nome_biblioteca"].PHP_EOL ]);
		$order = "sigla";
	}
	elseif($type == 'unidades'){
	       array_push($content, ["sigla_unidade" => $_POST["sigla_unidade"], "nome_unidade" => $_POST["nome_unidade"].PHP_EOL ]);
		$order = "sigla";
	}
        $content = order_content($content, $type, $order);
        grava_arquivo($content, $path);
}

function remove_content($path, $type, $valor, $campo){
        $content = get_content($path, $type);
        for ($i = 0; $i < sizeof($content); $i++) {
                if($content[$i][$campo] == $valor){
                        unset($content[$i]);
                }
        }
        grava_arquivo($content, $path);
}
