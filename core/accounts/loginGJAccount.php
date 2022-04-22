<?php
	chdir(dirname(__FILE__));

	include "../functions/connection.php";
    include "../config/security.php";
    require_once "../functions/functions.php";
    $f = new Functions();
    
    $userName = $_POST['userName']; $userName = $f->checkEmpty($userName); if($userName == false) exit('-1');
    $password = $_POST['password']; $password = $f->checkEmpty($password); if($password == false) exit('-1');
    $ip = $f->getIP();

    // Проврка бана на IP
    if($checkBanIP){
        if($f->checkBanIP($ip) == false) exit('-1');
    }

    // Ограничение на кол-во попыток
    if($f->checkAttempts(1002, $ip)){
        $f->editAttempts(1002, $ip);
    } else {
        exit('-1');
    }

    // Поиск профиля
    $query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName");
	$query->execute([':userName' => $userName]);
    $accountID = $query->fetchColumn();
	if($accountID > 0){
        // Проверка пароля
        if($f->checkPassword($accountID, $password)){
            $query = $db->prepare("SELECT registered FROM accounts WHERE userName = :userName");
	        $query->execute([':userName' => $userName]);
            if($query->fetchColumn() > 0){
                // Проверка в таблице пользователей
                $query = $db->prepare("SELECT count(*) FROM users WHERE extID = :accountID");
                $query->execute([':accountID' => $accountID]);
                if($query->fetchColumn() > 0){
                    $query = $db->prepare("SELECT userID FROM users WHERE extID = :accountID");
                    $query->execute([':accountID' => $accountID]);
                    $userID = $query->fetchColumn();
                } else {
                    $query = $db->prepare("INSERT INTO users (isRegistered, extID, userName, IP) VALUES (1, :accountID, :userName, :ip)");
                    $query->execute([':accountID' => $accountID, ':userName' => $userName, ':ip' => $ip]);
                    $userID = $db->lastInsertId();
                }
                
                // Вход в аккаунт
                $query = $db->prepare("INSERT INTO aactions (type, IP, time, accountID) VALUES (2, :ip, :time, :accountID)");
                $query->execute([':ip' => $ip, ':time' => time(), ':accountID' => $accountID]);
                exit($accountID.','.$userID);
            } else exit('-12');
        } else {
            exit('-1');
        }
    } else {
        exit('-12');
    }
?>