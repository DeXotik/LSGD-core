<?php
    error_reporting(0);

    $type = file_exists('config.php');

    if($type){
        require_once 'config.php';

        if(!file_exists('../'.$src.'/incl/lib/connection.php') OR empty($user) OR empty($userPassword)){
            unlink('/admin/config.php');
            header('Location: /admin.php');
        }

        if(isset($_COOKIE['auth'])){
            $cookie = unserialize($_COOKIE['auth']);
            if($user == $cookie['user'] AND $userPassword == $cookie['password']){
                $auth = true;
                if(isset($_GET['change'])){
                    $change = htmlspecialchars($_GET['change']);
                    switch($change){
                        case 1:
                            if(!empty($_POST['submit']) AND !empty($_POST['user']) AND $user != $_POST['user'] AND mb_strlen($_POST['user']) >= 5){
                                $user = $_POST['user'];

                                $file = fopen('config.php', 'w');
                                fwrite($file, "<?php\n\t".'$src'." = '{$src}';\n\n\t".'$user'." = '{$user}';\n\t".'$userPassword'." = '{$userPassword}';\n?>");
                                fclose($file);

                                header('Location: /admin.php');
                            }
                        break;
                        case 2:
                            if(!empty($_POST['submit']) AND !empty($_POST['password']) AND $userPassword != $_POST['password'] AND mb_strlen($_POST['password']) >= 8){
                                $userPassword = $_POST['password'];

                                $file = fopen('config.php', 'w');
                                fwrite($file, "<?php\n\t".'$src'." = '{$src}';\n\n\t".'$user'." = '{$user}';\n\t".'$userPassword'." = '{$userPassword}';\n?>");
                                fclose($file);

                                header('Location: /admin.php');
                            }
                        break;
                        case 3:
                            if(!empty($_POST['submit']) AND !empty($_POST['src']) AND $src != $_POST['src']){
                                $src = $_POST['src'];

                                if(file_exists('../'.$src.'/incl/lib/connection.php')){
                                    $file = fopen('config.php', 'w');
                                    fwrite($file, "<?php\n\t".'$src'." = '{$src}';\n\n\t".'$user'." = '{$user}';\n\t".'$userPassword'." = '{$userPassword}';\n?>");
                                    fclose($file);
                                    header('Location: /admin.php');
                                }
                            }
                        break;
                    }
                }
            } else {
                setcookie('auth', null, time(), '/');
                header('Location: /admin.php');
            }
        } else header('Location: /admin.php');
    } else header('Location: /admin.php');
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
        <link rel="stylesheet" href="css/config.css?<? echo rand(0, 999999999) ?>">
        <link rel="stylesheet" href="css/settings.css?<? echo rand(0, 999999999) ?>">
        <title>Admin • Settings</title>
    </head>
    <body>
        <? require_once 'header.php' ?>
        <div class="container">
            <div class="settingsTitle">
                <b id="settingsTitle">Настройки</b>
                <a href="/admin.php?delete=1">Перустановить панель управления</a>
            </div>
            <div class="line"></div>
            <div class="user">
                <img src="img/user.png" alt="user.png">
                <div id="userInfo">
                    <form id="block" action="settings.php?change=1" method="post">
                        <p id="type">Пользовтель</p>
                        <? if($change != 1){ ?>
                        <p><? echo $user ?></p>
                        <a href="settings.php?change=1">Изменить</a>
                        <? } else { ?>
                        <input class="text" type="text" name="user" value="<? echo $user ?>" minlength="5" required>
                        <input class="button" type="submit" name="submit" value="Сохранить">
                        <? } ?>
                    </form>
                    <form id="block" action="settings.php?change=2" method="post">
                        <p id="type">Пароль</p>
                        <? if($change != 2){ ?>
                        <p><? echo $userPassword ?></p>
                        <a href="settings.php?change=2">Изменить</a>
                        <? } else { ?>
                        <input class="text" type="text" name="password" value="<? echo $userPassword ?>" minlength="8" required>
                        <input class="button" type="submit" name="submit" value="Сохранить">
                        <? } ?>
                    </form>
                    <div id="block"><a href="/admin/unauth.php">Выйти</a></div>
                </div>
            </div>
            <b id="settingsTitle">Файлы сервера</b>
            <div class="line"></div>
            <div class="files">
                <form id="block" action="settings.php?change=3" method="post">
                    <p id="type">Путь к файлам сервера</p>
                    <? if($change != 3){ ?>
                    <p><? echo $src ?></p>
                    <a href="settings.php?change=3">Изменить</a>
                    <? } else { ?>
                    <input class="text" type="text" name="src" value="<? echo $src ?>" minlength="5" required>
                    <input class="button" type="submit" name="submit" value="Сохранить">
                    <? } ?>
                </form>
            </div>
        </div>
    </body>
</html>