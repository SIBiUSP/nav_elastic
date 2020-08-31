<?php
/**
 ** Gerenciamento de Usuários
 **/
$url_users = "{$_SERVER['DOCUMENT_ROOT']}/inc/staff.txt";
function get_users(){
        $users = array();
        $arquivo = fopen('../inc/staff.txt','r');
        if ($arquivo == false) die('O arquivo não existe.');
        while(true) {
                $linha = fgets($arquivo);
                if ($linha==null) break;
                $usertemp = explode(";", $linha);
                array_push($users, ["nusp" => $usertemp[0], "nome" => $usertemp[1], "unidade" => $usertemp[2] ]);
        }
        fclose($arquivo);
        $users = order_users($users);
        return $users;
}

function order_users($users, $order = "unidade"){
        foreach ($users as $key => $row) {
                $nusp[$key]  = $row['nusp'];
                $nome[$key] = $row['nome'];
                $unidade[$key] = $row['unidade'];
        }
        if($order == "nome"){
                array_multisort($nome, SORT_ASC, $users);
        } else {
                array_multisort($unidade, SORT_ASC, $nome, SORT_ASC, $users);
        }
        return $users;
}

function grava_arquivo($users){
        $arquivo = fopen('../inc/staff.txt','w+');
	if ($arquivo == false) die('O arquivo não existe.');
	$users_txt = '';
	foreach($users as $user){
                $users_txt .= $user["nusp"]. ";" . $user["nome"] . ";" . $user["unidade"];
        }
	fwrite($arquivo, $users_txt);
        fclose($arquivo);
}

function set_user(){
        $users = get_users();
        array_push($users, ["nusp" => $_POST["nusp"], "nome" => $_POST["nome"], "unidade" => $_POST["unidade"].PHP_EOL ]);
        $users = order_users($users);
        grava_arquivo($users);
}

function remove_user($nusp){
        $users = get_users();
        for ($i = 0; $i < sizeof($users); $i++) {
                if($users[$i]["nusp"] == $nusp){
                        unset($users[$i]);
                }
        }
        grava_arquivo($users);
}

/*function get_staffUsers(){
        $users = get_users();
        foreach ($users as $user){
                $staffUsers[] = $user["nusp"];
        }
        return $staffUsers;
}*/
