<?
    include '../lib/connection.php';
    require_once '../../functions/functions.php';
    $f = new Functions();

    $ip = $f->getIP();

    $query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
    $query->execute([':ip' => $ip]);
    if($query->fetchColumn() > 0) exit('Простите, но ваш IP забанен');

    if(!empty($_GET['token'])){
        $token = $_GET['token'];
        $query = $db->prepare("SELECT registered FROM accounts WHERE token = :token");
        $query->execute([':token' => $token]);
        if($query->rowCount() > 0){
            if($query->fetchColumn() == 0){
                $query = $db->prepare("UPDATE accounts SET registered = 1, registerDate = :time WHERE token = :token");
                $query->execute([':time' => time(), ':token' => $token]);
                echo 'Почта успешно подтверждена';
            } else echo 'Почта уже была подтверждена';
        } else echo 'Нету аккаунта для подтверждения почты';
    }
?>