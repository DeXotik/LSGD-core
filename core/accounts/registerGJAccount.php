<?php
	chdir(dirname(__FILE__));
		
	include "../functions/connection.php";
	include "../config/security.php";
	require_once "../functions/functions.php";
	$f = new Functions();

	include '../config/phpmailer.php';
    if(empty($emailHost)) exit('-1');
    if(empty($emailSecure)) exit('-1');
    if(empty($emailPort)) exit('-1');
    if(empty($emailUser)) exit('-1');
    if(empty($emailPassword)) exit('-1');
    if(empty($confirmFolder)) exit('-1');
    if(empty($subject)) exit('-1');

    require '../incl/phpmailer/PHPMailer.php';
    require '../incl/phpmailer/SMTP.php';
    require '../incl/phpmailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

	$userName = $_POST['userName']; $userName = $f->checkEmpty($userName); if($userName == false) exit('-1');
	$password = $_POST['password']; $password = $f->checkEmpty($password); if($password == false) exit('-1');
	$email = $_POST['email']; $email = $f->checkEmpty($email); if($email == false) exit('-1');
	$ip = $f->getIP();

	// Проврка бана на IP
	if($checkBanIP){
		if($f->checkBanIP($ip) == false) exit('-1');
	}

	// Ограничение на кол-во попыток
	if($f->checkAttempts(1001, $ip)){
		$f->editAttempts(1001, $ip);
	} else {
		exit('-1');
	}

	// Проврка лимита аккаунтов на IP
	if($limitAccounts){
		$query = $db->prepare("SELECT count(*) FROM aactions WHERE type = 1 AND IP = :ip");
		$query->execute([':ip' => $ip]);
		if($query->fetchColumn() >= $maxAccounts) exit('-1');
	}

	// Проверка имени пользователя
	$query = $db->prepare("SELECT count(*) FROM accounts WHERE userName = :userName");
	$query->execute([':userName' => $userName]);
	if($query->fetchColumn() > 0) exit('-2');

	// Проверка почты пользователя
	$query = $db->prepare("SELECT count(*) FROM accounts WHERE email = :email");
	$query->execute([':email' => $email]);
	if($query->fetchColumn() > 0) exit('-3');

	$token = $f->genString(32);

    $body = "
    <!DOCTYPE html>
    <html lang=\"ru\">
        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <title>LSGD</title>
            <style type=\"text/css\">
                *{ padding: 0; margin: 0; border: 0; }
                *, *:before, *:after{ -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; }
                :focus, :active{ outline: none; }
                a:focus,a:active{ outline: none; }
                nav, footer, header, aside{ display: block; }
                html, body{
                    min-height: 100%;
                    width: 100%;
                    font-size: 100%;
                    line-height: 1;
                    font-size: 15px;
                    -ms-text-size-adjust: 100%;
                    -moz-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%;
                }
                input, button, textarea{ font-family:inherit; }
                input::-ms-clear{ display: none; }
                button{ cursor: pointer; }
                button::-moz-focus-inner{ padding: 0; border: 0; }
                a, a:visited{ text-decoration: none; }
                a:hover{ text-decoration: none; }
                ul li, ol li{ list-style: none; }
                img{ vertical-align: top; }
                h1, h2, h3, h4, h5, h6{ font-size: inherit; font-weight: 400; }

                header{
                    width: 100%;
                    background: #2a5cff;
                    padding: 10px 20px;
                }
    
                header p{
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 25px;
                    color: #f4f4f4;
                }
    
                .activate{
                    width: 100%;
                    background: #373737;
                    padding: 20px;
                }
    
                .activate__title{
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 20px;
                    color: #f4f4f4;
                }
    
                .activate__subtitle{
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 15px;
                    color: #cecece;
                }
    
                .activate__subtitle a{
                    color: #ffb6b6;
                }
    
                .activate__subtitle a:visited{
                    color: #b9ffc3;
                }
    
                .activate__subtitle a:hover{
                    color: #efffb7;
                }
            </style>
        </head>
        <body>
            <header>
                <p>$subject</p>
            </header>
            <div class=\"activate\">
                <p class=\"activate__title\">Здавствуйте $userName.</p>
                <p class=\"activate__subtitle\">Что бы подтвердить регистрацию на нашем серевере, нажмите на <a href=\"$confirmFolder/incl/phpmailer/confirmEmail.php?token=$token\">подтвердить почту</a></p>
            </div>
        </body>
    </html>
    ";

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $emailHost;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = $emailSecure;
    $mail->Port = $emailPort;
    $mail->Username = $emailUser;
    $mail->Password = $emailPassword;
    $mail->Subject = $subject;
    $mail->setFrom($emailUser);
    $mail->isHTML(true);
    $mail->Body = $body;
    $mail->addAddress($email);
    if($mail->Send()){
        $send = true;
    } else $send = false;
    $mail->smtpClose();

    if($send){
        // Регистрация
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = $db->prepare("INSERT INTO accounts (userName, password, email, token, registerDate, secret, saveData, saveKey) VALUES (:userName, :password, :email, :token, :time, '', '', '')");
        $query->execute([':userName' => $userName, ':password' => $hash, ':email' => $email, ':token'=> $token, ':time' => time()]);
        
        $accountID = $db->lastInsertId();
        $query = $db->prepare("INSERT INTO aactions (type, IP, time, accountID) VALUES (1, :ip, :time, :accountID)");
        $query->execute([':ip' => $ip, ':time' => time(), ':accountID' => $accountID]);
        exit('1');
    } else exit('-6');
?>