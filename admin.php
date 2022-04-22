<?php
    error_reporting(0);

    $type = file_exists('admin/config.php');

    if($type){
        require_once 'admin/config.php';

        if(!file_exists($src.'/incl/lib/connection.php') OR empty($user) OR empty($userPassword)){
            unlink('/admin/config.php');
            header('Location: admin.php');
        }

        if(isset($_COOKIE['auth'])){
            $cookie = unserialize($_COOKIE['auth']);
            if($user == $cookie['user'] AND $userPassword == $cookie['password']){
                $auth = true; $edit = 0;

                if(isset($_GET['delete']) AND $_GET['delete'] == 1){
                    $delete = true;
                    if(isset($_POST['submit'])){
                        unlink('/admin/config.php');
                        header('Location: admin.php');
                    }
                } else $delete = false;

                if(isset($_GET['menu'])){
                    $menu = htmlspecialchars($_GET['menu']);
                    switch($menu){
                        case 'config':
                            require_once $src.'/config/connection.php';
                            require_once $src.'/config/dailyChests.php';
                            if(isset($_GET['edit'])){
                                $edit = htmlspecialchars($_GET['edit']);
                                switch($edit){
                                    case 1:
                                        if(isset($_POST['submit'])){
                                            if($dbHost != $_POST['dbHost'] OR $dbUser != $_POST['dbUser'] OR $dbPassword != $_POST['dbPassword'] OR $dbName != $_POST['dbName']){
                                                $dbHost = $_POST['dbHost'];
                                                $dbUser = $_POST['dbUser'];
                                                $dbPassword = $_POST['dbPassword'];
                                                $dbName = $_POST['dbName'];
                                                $file = fopen($src.'/config/connection.php', 'w');
                                                fwrite($file, "<?php\n\t".'$dbHost'." = '{$dbHost}';\n\t".'$dbUser'." = '{$dbUser}';\n\t".'$dbPassword'." = '{$dbPassword}';\n\t".'$dbName'." = '{$dbName}';\n?>");
                                                fclose($file);
                                            }
                                            header('Location: admin.php?menu=config');
                                        }
                                    break;
                                    case 2:
                                        if(isset($_POST['submit'])){
                                            if($chest1minOrbs != $_POST['chest1minOrbs'] OR $chest1maxOrbs != $_POST['chest1maxOrbs'] OR $chest1minDiamonds != $_POST['chest1minDiamonds'] OR $chest1maxDiamonds != $_POST['chest1maxDiamonds'] OR $chest1wait != $_POST['chest1wait'] OR $chest2minOrbs != $_POST['chest2minOrbs'] OR $chest2maxOrbs != $_POST['chest2maxOrbs'] OR $chest2minDiamonds != $_POST['chest2minDiamonds'] OR $chest2maxDiamonds != $_POST['chest2maxDiamonds'] OR $chest2wait != $_POST['chest2wait']){
                                                $chest1minOrbs = $_POST['chest1minOrbs'];
                                                $chest1maxOrbs = $_POST['chest1maxOrbs'];
                                                $chest1minDiamonds = $_POST['chest1minDiamonds'];
                                                $chest1maxDiamonds = $_POST['chest1maxDiamonds'];
                                                $chest1wait = $_POST['chest1wait'];
                                                $chest2minOrbs = $_POST['chest2minOrbs'];
                                                $chest2maxOrbs = $_POST['chest2maxOrbs'];
                                                $chest2minDiamonds = $_POST['chest2minDiamonds'];
                                                $chest2maxDiamonds = $_POST['chest2maxDiamonds'];
                                                $chest2wait = $_POST['chest2wait'];
                                                $file = fopen($src.'/config/dailyChests.php', 'w');
                                                fwrite($file, "<?php\n\t".'$chest1minOrbs'." = {$chest1minOrbs};\n\t".'$chest1maxOrbs'." = {$chest1maxOrbs};\n\t".'$chest1minDiamonds'." = {$chest1minDiamonds};\n\t".'$chest1maxDiamonds'." = {$chest1maxDiamonds};\n\t".'$chest1wait'." = {$chest1wait};\n\n\t".'$chest2minOrbs'." = {$chest2minOrbs};\n\t".'$chest2maxOrbs'." = {$chest2maxOrbs};\n\t".'$chest2minDiamonds'." = {$chest2minDiamonds};\n\t".'$chest2maxDiamonds'." = {$chest2maxDiamonds};\n\t".'$chest2wait'." = {$chest2wait};\n\n\t".'$chest1minShards = 1;'."\n\t".'$chest1maxShards = 6;'."\n\t".'$chest1minKeys = 1;'."\n\t".'$chest1maxKeys = 6;'."\n\n\t".'$chest2minShards = 1;'."\n\t".'$chest2maxShards = 6;'."\n\t".'$chest2minKeys = 1;'."\n\t".'$chest2maxKeys = 6;'."\n?>");
                                                fclose($file);
                                            }
                                            header('Location: admin.php?menu=config');
                                        }
                                    break;
                                }
                            }
                        break;
                        case 'quests':
                            include $src.'/incl/lib/connection.php';
                            $query = $db->prepare("SELECT * FROM quests ORDER BY name"); $query->execute(); $quests = $query->fetchAll();
                            if(isset($_GET['edit'])){
                                $edit = htmlspecialchars($_GET['edit']);
                                if(isset($_POST['submit']) AND !empty($_POST['name']) AND !empty($_POST['type']) AND $_POST['type'] >= 1 AND $_POST['type'] <= 3 AND !empty($_POST['amount']) AND $_POST['amount'] > 0 AND !empty($_POST['reward']) AND $_POST['reward'] > 0){
                                    $name = $_POST['name'];
                                    $type = $_POST['type'];
                                    $amount = $_POST['amount'];
                                    $reward = $_POST['reward'];
                                    $query = $db->prepare("UPDATE quests SET name = :name, type = :type, amount = :amount, reward = :reward WHERE ID = :id");
                                    $query->execute([':name' => $name, ':type' => $type, ':amount' => $amount, ':reward' => $reward, ':id' => $edit]);
                                    header('Location: admin.php?menu=quests');
                                }
                            } elseif(isset($_GET['drop'])){
                                $drop = htmlspecialchars($_GET['drop']);
                                $query = $db->prepare("DELETE FROM quests WHERE ID = :id"); $query->execute([':id' => $drop]);
                                header('Location: admin.php?menu=quests');
                            } elseif(isset($_GET['create']) AND $_GET['create'] == 1){
                                if(isset($_POST['submit']) AND !empty($_POST['name']) AND !empty($_POST['type']) AND $_POST['type'] >= 1 AND $_POST['type'] <= 3 AND !empty($_POST['amount']) AND $_POST['amount'] > 0 AND !empty($_POST['reward']) AND $_POST['reward'] > 0){
                                    $name = $_POST['name'];
                                    $type = $_POST['type'];
                                    $amount = $_POST['amount'];
                                    $reward = $_POST['reward'];
                                    $query = $db->prepare("SELECT count(*) FROM quests WHERE name = :name");
                                    $query->execute([':name' => $name]);
                                    if($query->fetchColumn() == 0){
                                        $query = $db->prepare("INSERT INTO quests (type, amount, reward, name) VALUES (:type, :amount, :reward, :name)");
                                        $query->execute([':name' => $name, ':type' => $type, ':amount' => $amount, ':reward' => $reward]);
                                        header('Location: admin.php?menu=quests');
                                    }
                                }
                            }
                        break;
                        case 'roles':
                            include $src.'/incl/lib/connection.php';

                            if(isset($_GET['dropuser']) AND $_GET['dropuser'] > 0){
                                $accountID = $_GET['dropuser'];
                                $query = $db->prepare("DELETE FROM roleassign WHERE accountID = :accountID");
                                $query->execute([':accountID' => $accountID]);
                                header('Location: admin.php?menu=roles');
                            } elseif(isset($_GET['drop'])){
                                $roleID = $_GET['drop'];
                                $query = $db->prepare("DELETE FROM roles WHERE roleID = :roleID");
                                $query->execute([':roleID' => $roleID]);
                                $query = $db->prepare("DELETE FROM roleassign WHERE roleID = :roleID");
                                $query->execute([':roleID' => $roleID]);
                                header('Location: admin.php?menu=roles');
                            } elseif(isset($_GET['edit'])){
                                $edit = htmlspecialchars($_GET['edit']);
                                if(isset($_POST['submit'])){
                                    $query = $db->prepare("UPDATE roles SET roleName = :roleName, commandRate = :commandRate, commandFeature = :commandFeature, commandEpic = :commandEpic, commandUnepic = :commandEpic, commandVerifycoins = :commandVerifycoins, commandDaily = :commandDaily, commandWeekly = :commandWeekly, commandDelete = :commandDelete, actionRateDifficulty = :actionRateDifficulty, actionRateStars = :actionRateStars, actionRequestMod = :actionRateStars, actionRateDemon = :actionRateDemon, actionSuggestRating = :actionSuggestRating, commentColor = :commentColor, modBadgeLevel = :modBadgeLevel WHERE roleID = :roleID");
                                    $query->execute([':roleName' => $_POST['roleName'], ':commandRate' => $_POST['commandRate'], ':commandFeature' => $_POST['commandFeature'], ':commandEpic' => $_POST['commandEpic'], ':commandVerifycoins' => $_POST['commandVerifycoins'], ':commandDaily' => $_POST['commandDaily'], ':commandWeekly' => $_POST['commandWeekly'], ':commandDelete' => $_POST['commandDelete'], ':actionRateDifficulty' => $_POST['actionRateDifficulty'], ':actionRateStars' => $_POST['actionRateStars'], ':actionRateDemon' => $_POST['actionRateDemon'], ':actionSuggestRating' => $_POST['actionSuggestRating'], ':commentColor' => $_POST['commentColor'], ':modBadgeLevel' => $_POST['modBadgeLevel'], ':roleID' => $edit]);
                                    header('Location: admin.php?menu=roles');
                                } elseif(isset($_POST['addRole'])){
                                    $query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName");
                                    $query->execute([':userName' => $_POST['nickName']]);
                                    if($query->rowCount() > 0){
                                        $accID = $query->fetchColumn();
                                        $query = $db->prepare("SELECT count(*) FROM roleassign WHERE accountID = :accountID");
                                        $query->execute([':accountID' => $accID]);
                                        if($query->fetchColumn() > 0){
                                            $query = $db->prepare("UPDATE roleassign SET roleID = :roleID WHERE accountID = :accountID");
                                            $query->execute([':roleID' => $edit, ':accountID' => $accID]);
                                        } else {
                                            $query = $db->prepare("INSERT INTO roleassign (roleID, accountID) VALUES (:roleID, :accountID)");
                                            $query->execute([':roleID' => $edit, ':accountID' => $accID]);
                                        }
                                    }
                                    header('Location: admin.php?menu=roles');
                                }
                            } elseif(isset($_GET['create']) AND $_GET['create'] == 1){
                                if(isset($_POST['submit'])){
                                    $query = $db->prepare("INSERT INTO roles (roleName, commandRate, commandFeature, commandEpic, commandUnepic, commandVerifycoins, commandDaily, commandWeekly, commandDelete, actionRateDifficulty, actionRateStars, actionRequestMod, actionRateDemon, actionSuggestRating, commentColor, modBadgeLevel) VALUES (:roleName, :commandRate, :commandFeature, :commandEpic, :commandEpic, :commandVerifycoins, :commandDaily, :commandWeekly, :commandDelete, :actionRateDifficulty, :actionRateStars, :actionRateStars, :actionRateDemon, :actionSuggestRating, :commentColor, :modBadgeLevel)");
                                    $query->execute([':roleName' => $_POST['roleName'], ':commandRate' => $_POST['commandRate'], ':commandFeature' => $_POST['commandFeature'], ':commandEpic' => $_POST['commandEpic'], ':commandVerifycoins' => $_POST['commandVerifycoins'], ':commandDaily' => $_POST['commandDaily'], ':commandWeekly' => $_POST['commandWeekly'], ':commandDelete' => $_POST['commandDelete'], ':actionRateDifficulty' => $_POST['actionRateDifficulty'], ':actionRateStars' => $_POST['actionRateStars'], ':actionRateDemon' => $_POST['actionRateDemon'], ':actionSuggestRating' => $_POST['actionSuggestRating'], ':commentColor' => $_POST['commentColor'], ':modBadgeLevel' => $_POST['modBadgeLevel']]);
                                    header('Location: admin.php?menu=roles');
                                }
                            }

                            $query = $db->prepare("SELECT * FROM roles");
                            $query->execute();
                            $roles = $query->fetchAll();
                        break;
                        case 'mappacks':
                            include $src.'/incl/lib/connection.php';

                            if(isset($_GET['create']) AND $_GET['create'] == 1){
                                if(isset($_POST['submit']) AND is_numeric($_POST['lvl1']) AND is_numeric($_POST['lvl2']) AND is_numeric($_POST['lvl3'])){
                                    $lvls = $_POST['lvl1'].','.$_POST['lvl2'].','.$_POST['lvl3'];
                                    $query = $db->prepare("SELECT sum(starStars) FROM levels WHERE levelID IN ({$lvls})"); $query->execute();
                                    $stars = round($query->fetchColumn()/3);
                                    $coins = 1;
                                    switch($stars){
                                        case 1: $diff = 0; break;
                                        case 2: $diff = 1; break;
                                        case 3: $diff = 2; break;
                                        case 4: case 5: $diff = 3; break;
                                        case 6: case 7: $diff = 4; break;
                                        case 8: case 9: $diff = 5; break;
                                        case 10: $diff = 6; $coins = 2; break;
                                        default: $diff = 0;
                                    }
                                    $query = $db->prepare("INSERT INTO mappacks (name, levels, stars, coins, difficulty, rgbcolors) VALUES (:name, :lvls, :stars, :coins, :diff, :color)");
                                    $query->execute([':name' => $_POST['name'], ':lvls' => $lvls, ':stars' => $stars, ':coins' => $coins, ':diff' => $diff, ':color' => $_POST['color']]);
                                    header('Location: admin.php?menu=mappacks');
                                }
                            } elseif(isset($_GET['edit'])){
                                $edit = htmlspecialchars($_GET['edit']);
                                if(isset($_POST['submit']) AND is_numeric($_POST['lvl1']) AND is_numeric($_POST['lvl2']) AND is_numeric($_POST['lvl3'])){
                                    $lvls = $_POST['lvl1'].','.$_POST['lvl2'].','.$_POST['lvl3'];
                                    $query = $db->prepare("SELECT sum(starStars) FROM levels WHERE levelID IN ({$lvls})"); $query->execute();
                                    $stars = round($query->fetchColumn()/3);
                                    $coins = 1;
                                    switch($stars){
                                        case 1: $diff = 0; break;
                                        case 2: $diff = 1; break;
                                        case 3: $diff = 2; break;
                                        case 4: case 5: $diff = 3; break;
                                        case 6: case 7: $diff = 4; break;
                                        case 8: case 9: $diff = 5; break;
                                        case 10: $diff = 6; $coins = 2; break;
                                        default: $diff = 0;
                                    }
                                    $query = $db->prepare("UPDATE mappacks SET name = :name, levels = :lvls, stars = :stars, coins = :coins, difficulty = :diff, rgbcolors = :color WHERE ID = :id");
                                    $query->execute([':name' => $_POST['name'], ':lvls' => $lvls, ':stars' => $stars, ':coins' => $coins, ':diff' => $diff, ':color' => $_POST['color'], ':id' => $edit]);
                                    header('Location: admin.php?menu=mappacks');
                                }
                            } elseif(isset($_GET['drop'])){
                                $id = htmlspecialchars($_GET['drop']);
                                $query = $db->prepare("DELETE FROM mappacks WHERE ID = :id");
                                $query->execute([':id' => $id]);
                                header('Location: admin.php?menu=mappacks');
                            }

                            $query = $db->prepare("SELECT * FROM mappacks ORDER BY stars"); $query->execute(); $mappacks = $query->fetchAll();
                        break;
                        case 'gauntlets':
                            include $src.'/incl/lib/connection.php'; error_reporting(E_ALL);

                            if(isset($_GET['create']) AND $_GET['create'] == 1){
                                if(isset($_POST['submit']) AND is_numeric($_POST['lvl1']) AND is_numeric($_POST['lvl2']) AND is_numeric($_POST['lvl3']) AND is_numeric($_POST['lvl4']) AND is_numeric($_POST['lvl5'])){
                                    $query = $db->prepare("SELECT * FROM gauntlets WHERE ID = :id");
                                    $query->execute([':id' => $_POST['type']]);
                                    if($query->fetchColumn() == 0){
                                        $query = $db->prepare("INSERT INTO gauntlets (ID, level1, level2, level3, level4, level5) VALUES (:id, :lvl1, :lvl2, :lvl3, :lvl4, :lvl5)");
                                        $query->execute([':id' => $_POST['type'], ':lvl1' => $_POST['lvl1'], ':lvl2' => $_POST['lvl2'], ':lvl3' => $_POST['lvl3'], ':lvl4' => $_POST['lvl4'], ':lvl5' => $_POST['lvl5']]);
                                    }
                                    header('Location: admin.php?menu=gauntlets');
                                }
                            } elseif(isset($_GET['edit'])){
                                $edit = htmlspecialchars($_GET['edit']);
                                if(isset($_POST['submit']) AND is_numeric($_POST['lvl1']) AND is_numeric($_POST['lvl2']) AND is_numeric($_POST['lvl3']) AND is_numeric($_POST['lvl4']) AND is_numeric($_POST['lvl5'])){
                                    $query = $db->prepare("SELECT * FROM gauntlets WHERE ID = :id");
                                    $query->execute([':id' => $_POST['type']]);
                                    if($query->fetchColumn() == 0){
                                        $query = $db->prepare("UPDATE gauntlets SET ID = :type, level1 = :lvl1, level2 = :lvl2, level3 = :lvl3, level4 = :lvl4, level5 = :lvl5 WHERE ID = :id");
                                        $query->execute([':type' => $_POST['type'], ':lvl1' => $_POST['lvl1'], ':lvl2' => $_POST['lvl2'], ':lvl3' => $_POST['lvl3'], ':lvl4' => $_POST['lvl4'], ':lvl5' => $_POST['lvl5'], ':id' => $edit]);
                                    }
                                    header('Location: admin.php?menu=gauntlets');
                                }
                            } elseif(isset($_GET['drop'])){
                                $id = htmlspecialchars($_GET['drop']);
                                $query = $db->prepare("DELETE FROM gauntlets WHERE ID = :id");
                                $query->execute([':id' => $id]);
                                header('Location: admin.php?menu=gauntlets');
                            }

                            $query = $db->prepare("SELECT * FROM gauntlets ORDER BY ID"); $query->execute(); $gauntlets = $query->fetchAll();
                        break;
                        default: $menu = 'home';
                    }
                } else $menu = 'home';
                if($menu === 'home'){
                    include $src.'/incl/lib/connection.php';
                    $query = $db->prepare("SELECT count(*) FROM users"); $query->execute(); $stats['users'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM accounts"); $query->execute(); $stats['accounts'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM levels"); $query->execute(); $stats['levels'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM comments"); $query->execute(); $stats['comments'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM acccomments"); $query->execute(); $stats['posts'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM messages"); $query->execute(); $stats['messages'] = $query->fetchColumn();
                    $query = $db->prepare("SELECT count(*) FROM songs"); $query->execute(); $stats['songs'] = $query->fetchColumn();
                }
            } else {
                setcookie('auth', null, time(), '/');
                header('Location: admin.php');
            }
        } else {
            if(isset($_POST['submit'])){
                $inputUser = $_POST['user'];
                $inputPassword = $_POST['password'];
                if(mb_strlen($inputUser) >= 5){
                    if(mb_strlen($inputPassword) >= 8){
                        if($user == $inputUser AND $userPassword == $inputPassword){
                            $cookie = [
                                'user' => $inputUser,
                                'password' => $inputPassword
                            ];
                            setcookie('auth', serialize($cookie), time()+(60*60*24), '/');
                            header('Location: admin.php');
                        } else $error = "Неверные данные";
                    } else $error = "Слишком короткий пароль";
                } else $error = "Слишком короткое имя пользователя";
            } else $auth = false;
        }
    } else {
        if(isset($_POST['submit'])){
            $src = $_POST['src'];
            $user = $_POST['user'];
            $userPassword = $_POST['password'];
            if(file_exists($src.'/incl/lib/connection.php')){
                if(mb_strlen($user) >= 5){
                    if(mb_strlen($userPassword) >= 8){
                        $file = fopen('admin/config.php', 'w');
                        fwrite($file, "<?php\n\t".'$src'." = '{$src}';\n\n\t".'$user'." = '{$user}';\n\t".'$userPassword'." = '{$userPassword}';\n?>");
                        fclose($file);

                        require_once $src.'/config/connection.php';
                        $connectionType = true;
                        if(empty($dbHost) AND !empty($servername)){
                            $dbHost = $servername;
                        } elseif(empty($dbHost)) $connectionType = false;
                        if(empty($dbUser) AND !empty($username)){
                            $dbUser = $username;
                        } elseif(empty($dbUser)) $connectionType = false;
                        if(empty($dbPassword) AND !empty($password)){
                            $dbPassword = $password;
                        } elseif(empty($dbPassword)) $connectionType = false;
                        if(empty($dbName) AND !empty($dbname)){
                            $dbName = $dbname;
                        } elseif(empty($dbName)) $connectionType = false;
                        if($connectionType === false){
                            $file = fopen($src.'/config/connection.php', 'w');
                            fwrite($file, "<?php\n\t".'$dbHost'." = 'localhost';\n\t".'$dbUser'." = 'root';\n\t".'$dbPassword'." = '';\n\t".'$dbName'." = 'geometrydash';\n?>");
                            fclose($file);
                            $file = fopen($src.'/incl/lib/connection.php', 'w');
                            fwrite($file, "<?php\n\terror_reporting(0);\n\tinclude dirname(__FILE__).".'"/../../config/connection.php"'.";\n\theader('Content-Type: text/html; charset=utf-8');\n\n\ttry {\n\t\t".'$db'." = new PDO(".'"mysql:host={$dbHost};dbname={$dbName};"'.", ".'$dbUser, $dbPassword'.", array(PDO::ATTR_PERSISTENT => true));\n\t\t".'$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);'."\n\t} catch(PDOException ".'$e'."){\n\t\techo 'Connection failed: '.".'$e->getMessage();'."\n\t}\n?>");
                            fclose($file);
                        }

                        header('Location: admin.php');
                    } else $error = "Слишком короткий пароль";
                } else $error = "Слишком короткое имя пользователя";
            } else $error = "Неправильный путь к файлам сервера";
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
        <link rel="stylesheet" href="admin/css/config.css?<? echo rand(0, 999999999) ?>">
        <link rel="stylesheet" href="admin/css/style.css?<? echo rand(0, 999999999) ?>">
        <title><? if($type){ if($auth){ ?>Admin • Home<? } else { ?>Admin • Auth<? } } else { ?>Admin • Install<? } ?></title>
    </head>
    <body>
        <? if($type){ ?>
            <? if($delete){ ?>
            <div class="start">
                <div id="startBlock">
                    <div id="title">ПЕРЕУСТАНОВКА</div>
                    <form action="admin.php?delete=1" id="form" method="post">
                        <p>Вы уверены что хотите переустановить панель управления?</p>
                        <input class="button" type="submit" name="submit" value="Переустановить">
                    </form>
                </div>
            </div>
            <? } else { ?>
            <? if($auth){ ?>
            <? require_once 'admin/header.php' ?>
            <div class="container">
                <? if($menu === 'home'){ ?>
                <div id="statsTitle">Статистика сервера</div>
                <div class="stats">
                    <div id="block">
                        <b>Пользователи</b>
                        <p><? echo $stats['users'] ?></p>
                    </div>
                    <div id="block">
                        <b>Аккаунты</b>
                        <p><? echo $stats['accounts'] ?></p>
                    </div>
                    <div id="block">
                        <b>Уровни</b>
                        <p><? echo $stats['levels'] ?></p>
                    </div>
                    <div id="block">
                        <b>Комментарии</b>
                        <p><? echo $stats['comments'] ?></p>
                    </div>
                    <div id="block">
                        <b>Посты</b>
                        <p><? echo $stats['posts'] ?></p>
                    </div>
                    <div id="block">
                        <b>Сообщения</b>
                        <p><? echo $stats['messages'] ?></p>
                    </div>
                    <div id="block">
                        <b>Музыка</b>
                        <p><? echo $stats['songs'] ?></p>
                    </div>
                </div>
                <div class="versions">
                    <div id="block">
                        <b>Версия 0.1</b>
                        <p>- Редактирование квестов</p>
                        <p>- Редактирование ролей</p>
                        <p>- Редактирование мап паков</p>
                        <p>- Редактирование гаунтлетов</p>
                    </div>
                    <div id="block">
                        <b>Версия 0.01</b>
                        <p>- Установка панели управления</p>
                        <p>- Вход в панель управления</p>
                        <p>- Вывод статистики сервера на главной странице</p>
                        <p>- Редактирование доступа к MySQL</p>
                        <p>- Редактирование настроек сундуков</p>
                    </div>
                </div>
                <? } elseif($menu === 'config'){ ?>
                <form class="block" action="admin.php?menu=config&edit=1" method="post">
                    <div id="title">
                        <b>Доступ к MySQL</b>
                        <? if($edit != 1){ ?>
                        <a href="admin.php?menu=config&edit=1">Изменить</a>
                        <? } else { ?>
                        <input class="button" type="submit" name="submit" value="Сохранить изменения">
                        <? } ?>
                    </div>
                    <div class="line"></div>
                    <div id="content">
                        <div id="string">
                            <p id="text">Хостинг</p>
                            <p id="text">Пользователь</p>
                            <p id="text">Пароль</p>
                            <p id="text">База данных</p>
                        </div>
                        <div id="values">
                            <? if($edit != 1){ ?>
                            <p id="text"><? echo $dbHost ?></p>
                            <p id="text"><? echo $dbUser ?></p>
                            <p id="text"><? echo $dbPassword ?></p>
                            <p id="text"><? echo $dbName ?></p>
                            <? } else { ?>
                            <div id="block"><input class="text" type="text" name="dbHost" value="<? echo $dbHost ?>" required></div>
                            <div id="block"><input class="text" type="text" name="dbUser" value="<? echo $dbUser ?>" required></div>
                            <div id="block"><input class="text" type="text" name="dbPassword" value="<? echo $dbPassword ?>" required></div>
                            <div id="block"><input class="text" type="text" name="dbName" value="<? echo $dbName ?>" required></div>
                            <? } ?>
                        </div>
                    </div>
                </form>
                <form class="block" action="admin.php?menu=config&edit=2" method="post">
                    <div id="title">
                        <b>Настройки сундуков</b>
                        <? if($edit != 2){ ?>
                        <a href="admin.php?menu=config&edit=2">Изменить</a>
                        <? } else { ?>
                        <input class="button" type="submit" name="submit" value="Сохранить изменения">
                        <? } ?>
                    </div>
                    <div class="line"></div>
                    <b id="stringTitle">Маленький сундук</b>
                    <div id="content">
                        <div id="string">
                            <p id="text">Орбы</p>
                            <p id="text">Алмазы</p>
                            <p id="text">Время ожидания (в секундах)</p>
                        </div>
                        <div id="values">
                            <? if($edit != 2){ ?>
                            <div id="block">
                                <p>от</p>
                                <p id="blockText"><? echo $chest1minOrbs ?></p>
                                <p>до</p>
                                <p id="blockText"><? echo $chest1maxOrbs ?></p>
                            </div>
                            <div id="block">
                                <p>от</p>
                                <p id="blockText"><? echo $chest1minDiamonds ?></p>
                                <p>до</p>
                                <p id="blockText"><? echo $chest1maxDiamonds ?></p>
                            </div>
                            <p id="textBlock"><? echo $chest1wait ?></p>
                            <? } else { ?>
                            <div id="block">
                                <p>от</p>
                                <input class="text" id="inputText" type="text" name="chest1minOrbs" value="<? echo $chest1minOrbs ?>" required>
                                <p>до</p>
                                <input class="text" id="inputText" type="text" name="chest1maxOrbs" value="<? echo $chest1maxOrbs ?>" required>
                            </div>
                            <div id="block">
                                <p>от</p>
                                <input class="text" id="inputText" type="text" name="chest1minDiamonds" value="<? echo $chest1minDiamonds ?>" required>
                                <p>до</p>
                                <input class="text" id="inputText" type="text" name="chest1maxDiamonds" value="<? echo $chest1maxDiamonds ?>" required>
                            </div>
                            <input class="text" id="textInput" type="text" name="chest1wait" value="<? echo $chest1wait ?>" required>
                            <? } ?>
                        </div>
                    </div>
                    <b id="stringTitle">Большой сундук</b>
                    <div id="content">
                        <div id="string">
                            <p id="text">Орбы</p>
                            <p id="text">Алмазы</p>
                            <p id="text">Время ожидания (в секундах)</p>
                        </div>
                        <div id="values">
                            <? if($edit != 2){ ?>
                            <div id="block">
                                <p>от</p>
                                <p id="blockText"><? echo $chest2minOrbs ?></p>
                                <p>до</p>
                                <p id="blockText"><? echo $chest2maxOrbs ?></p>
                            </div>
                            <div id="block">
                                <p>от</p>
                                <p id="blockText"><? echo $chest2minDiamonds ?></p>
                                <p>до</p>
                                <p id="blockText"><? echo $chest2maxDiamonds ?></p>
                            </div>
                            <p id="textBlock"><? echo $chest2wait ?></p>
                            <? } else { ?>
                            <div id="block">
                                <p>от</p>
                                <input class="text" id="inputText" type="text" name="chest2minOrbs" value="<? echo $chest2minOrbs ?>" required>
                                <p>до</p>
                                <input class="text" id="inputText" type="text" name="chest2maxOrbs" value="<? echo $chest2maxOrbs ?>" required>
                            </div>
                            <div id="block">
                                <p>от</p>
                                <input class="text" id="inputText" type="text" name="chest2minDiamonds" value="<? echo $chest2minDiamonds ?>" required>
                                <p>до</p>
                                <input class="text" id="inputText" type="text" name="chest2maxDiamonds" value="<? echo $chest2maxDiamonds ?>" required>
                            </div>
                            <input class="text" id="textInput" type="text" name="chest2wait" value="<? echo $chest2wait ?>" required>
                            <? } ?>
                        </div>
                    </div>
                </form>
                <? } elseif($menu === 'quests'){ ?>
                <div class="addQuest">
                    <b id="questsTitle">Добавить квест</b>
                    <div class="line"></div>
                    <form id="block" action="admin.php?menu=quests&create=1" method="post">
                        <input class="text" type="text" name="name" placeholder="Название квеста" required>
                        <select class="text" id="amount" name="type" required>
                            <option value="0" disabled selected>Выбрать тип</option>
                            <option value="1">Орбы</option>
                            <option value="2">Монеты</option>
                            <option value="3">Звёзды</option>
                        </select>
                        <input class="text" id="amount" type="text" placeholder="Кол-во" name="amount" required>
                        <input class="text" id="amount" type="text" name="reward" placeholder="Награда (в алмазах)" required>
                        <input class="button" type="submit" name="submit" value="Создать квест">
                    </form>
                </div>
                <div class="quests">
                <? if(count($quests) > 0){ ?>
                    <b id="questsTitle">Cписок квестов</b>
                    <div class="line"></div>
                    <?php
                        foreach($quests as $quest){
                            switch($quest['type']){
                                case 1:
                                    $type = ' орбов';
                                break;
                                case 2:
                                    $type = ' монет';
                                break;
                                case 3:
                                    $type = ' звёзд';
                                break;
                                default: $type = 'Неизвестно';
                            } 
                    ?>
                    <form id="block" action="admin.php?menu=quests&edit=<? echo $quest['ID'] ?>" method="post">
                        <? if($edit == $quest['ID']){ ?>
                        <input class="text" type="text" name="name" placeholder="Название квеста" value="<? echo $quest['name'] ?>" required>
                        <select class="text" id="amount" name="type" required>
                            <option value="1" <? if($quest['type'] == 1) echo 'selected' ?>>Орбы</option>
                            <option value="2" <? if($quest['type'] == 2) echo 'selected' ?>>Монеты</option>
                            <option value="3" <? if($quest['type'] == 3) echo 'selected' ?>>Звёзды</option>
                        </select>
                        <input class="text" id="amount" type="text" placeholder="Кол-во" name="amount" value="<? echo $quest['amount'] ?>" required>
                        <input class="text" id="amount" type="text" name="reward" placeholder="Награда (в алмазах)" value="<? echo $quest['reward'] ?>" required>
                        <input class="button" type="submit" name="submit" value="Сохранить">
                        <? } else { ?>
                        <p><? echo $quest['name'] ?></p>
                        <p id="amount"><? echo $quest['amount'] ?> <? echo $type ?></p>
                        <p>Награда: <? echo $quest['reward'] ?> алмазов</p>
                        <a href="admin.php?menu=quests&edit=<? echo $quest['ID'] ?>">Редактировать</a>
                        <a href="admin.php?menu=quests&drop=<? echo $quest['ID'] ?>">Удалить</a>
                        <? } ?>
                    </form>
                    <? } ?>
                <? } else { ?>
                    <p id="questsTitle">На сервере ещё нету квестов</p>
                <? } ?>
                </div>
                <? } elseif($menu === 'roles'){ ?>
                <form class="addRoles" action="admin.php?menu=roles&create=1" method="post">
                    <div id="title">
                        <b>Создать роль</b>
                        <input class="button" type="submit" name="submit" value="Создать">
                    </div>
                    <div class="line"></div>
                    <div id="edit">
                        <p>Название роли</p>
                        <input class="text" type="text" name="roleName" required>
                    </div>
                    <div id="edit">
                        <p>Команды</p>
                        <input class="text" type="text" name="commandRate" placeholder="rate (0 или 1)" required>
                        <input class="text" type="text" name="commandFeature" placeholder="featured (0 или 1)" required>
                        <input class="text" type="text" name="commandEpic" placeholder="epic (0 или 1)" required>
                        <input class="text" type="text" name="commandVerifycoins" placeholder="coins (0 или 1)" required>
                        <input class="text" type="text" name="commandDaily" placeholder="daily (0 или 1)" required>
                        <input class="text" type="text" name="commandWeekly" placeholder="weekly (0 или 1)" required>
                        <input class="text" type="text" name="commandDelete" placeholder="delete (0 или 1)" required>
                    </div>
                    <div id="edit">
                        <p>Оценка уровней</p>
                        <input class="text" type="text" name="actionRateDifficulty" placeholder="сложность (0 или 1)" required>
                        <input class="text" type="text" name="actionRateStars" placeholder="звёзды (0 или 1)" required>
                        <input class="text" type="text" name="actionRateDemon" placeholder="демон (0 или 1)" required>
                        <input class="text" type="text" name="actionSuggestRating" placeholder="suggest (0 или 1)" required>
                    </div>
                    <div id="edit">
                        <p>Цвет текста</p>
                        <input class="text" type="text" name="commentColor" placeholder="000,000,000 (rgb)" required>
                    </div>
                    <div id="edit">
                        <p>Значок модератора</p>
                        <input class="text" type="text" name="modBadgeLevel" placeholder="0, 1 или 2" required>
                    </div>
                </form>
                <div class="roles">
                    <? if(count($roles) > 0){ ?>
                    <? foreach($roles as $role){ ?>
                    <form id="block" action="admin.php?menu=roles&edit=<? echo $role['roleID'] ?>" method="post">
                        <div id="title">
                            <b><? echo $role['roleName'] ?>'s</b>
                            <div id="buttons">
                                <? if($edit == $role['roleID']){ ?>
                                <input class="button" type="submit" name="submit" value="Сохранить">
                                <? } else { ?>
                                <a href="admin.php?menu=roles&edit=<? echo $role['roleID'] ?>">Редактировать</a>
                                <? } ?>
                                <a href="admin.php?menu=roles&drop=<? echo $role['roleID'] ?>">Удалить</a>
                            </div>
                        </div>
                        <div class="line"></div>
                        <? if($edit == $role['roleID']){ ?>
                        <div id="edit">
                            <p>Название роли</p>
                            <input class="text" type="text" name="roleName" value="<? echo $role['roleName'] ?>" required>
                        </div>
                        <div id="edit">
                            <p>Команды</p>
                            <input class="text" type="text" name="commandRate" placeholder="rate (0 или 1)" value="<? echo $role['commandRate'] ?>" required>
                            <input class="text" type="text" name="commandFeature" placeholder="featured (0 или 1)" value="<? echo $role['commandFeature'] ?>" required>
                            <input class="text" type="text" name="commandEpic" placeholder="epic (0 или 1)" value="<? echo $role['commandEpic'] ?>" required>
                            <input class="text" type="text" name="commandVerifycoins" placeholder="coins (0 или 1)" value="<? echo $role['commandVerifycoins'] ?>" required>
                            <input class="text" type="text" name="commandDaily" placeholder="daily (0 или 1)" value="<? echo $role['commandDaily'] ?>" required>
                            <input class="text" type="text" name="commandWeekly" placeholder="weekly (0 или 1)" value="<? echo $role['commandWeekly'] ?>" required>
                            <input class="text" type="text" name="commandDelete" placeholder="delete (0 или 1)" value="<? echo $role['commandDelete'] ?>" required>
                        </div>
                        <div id="edit">
                            <p>Оценка уровней</p>
                            <input class="text" type="text" name="actionRateDifficulty" placeholder="сложность (0 или 1)" value="<? echo $role['actionRateDifficulty'] ?>" required>
                            <input class="text" type="text" name="actionRateStars" placeholder="звёзды (0 или 1)" value="<? echo $role['actionRateStars'] ?>" required>
                            <input class="text" type="text" name="actionRateDemon" placeholder="демон (0 или 1)" value="<? echo $role['actionRateDemon'] ?>" required>
                            <input class="text" type="text" name="actionSuggestRating" placeholder="suggest (0 или 1)" value="<? echo $role['actionSuggestRating'] ?>" required>
                        </div>
                        <div id="edit">
                            <p>Цвет текста</p>
                            <input class="text" type="text" name="commentColor" placeholder="000,000,000 (rgb)" value="<? echo $role['commentColor'] ?>" required>
                        </div>
                        <div id="edit">
                            <p>Значок модератора</p>
                            <input class="text" type="text" name="modBadgeLevel" placeholder="0, 1 или 2" value="<? echo $role['modBadgeLevel'] ?>" required>
                        </div>
                        <? } else { ?>
                        <div id="addUser">
                            <input class="text" type="text" name="nickName" placeholder="имя профиля" required>
                            <input class="button" type="submit" name="addRole" value="Добавить">
                        </div>
                        <?php
                            $query = $db->prepare("SELECT * FROM roleassign WHERE roleID = :roleID");
                            $query->execute([':roleID' => $role['roleID']]);
                            $accounts = $query->fetchAll();
                            foreach($accounts AS $account){
                                $query = $db->prepare("SELECT userName FROM accounts WHERE accountID = :accountID");
                                $query->execute([':accountID' => $account['accountID']]);
                                $accountName = $query->fetchColumn();
                        ?>
                        <div id="user">
                            <b id="userName"><? echo $accountName ?></b>
                            <a href="admin.php?menu=roles&dropuser=<? echo $account['accountID'] ?>">Удалить</a>
                        </div>
                        <? } ?>
                        <? } ?>
                    </form>
                    <? } ?>
                    <? } else { ?>
                    <p>Роли ещё не созданы</p>
                    <? } ?>
                </div>
                <? } elseif($menu === 'mappacks'){ ?>
                <form class="addMapPack" action="admin.php?menu=mappacks&create=1" method="post">
                    <div id="title">
                        <b>Добавить Map Pack</b>
                        <input class="button" type="submit" name="submit" value="Довавить">
                    </div>
                    <div class="line"></div>
                    <div id="block">
                        <input class="text" type="text" name="name" placeholder="Название" required>
                        <input class="text" type="text" name="color" placeholder="000,000,000 (rgb)" required>
                        <input class="text" type="tel" name="lvl1" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl2" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl3" placeholder="ID уровня" required>
                    </div>
                </form>
                <div class="mapPacks">
                    <b>Map Pack'и</b>
                    <div class="line"></div>
                    <?php
                        foreach($mappacks AS $mappack){
                            $lvls = explode(',', $mappack['levels']);
                    ?>
                    <form id="block" action="admin.php?menu=mappacks&edit=<? echo $mappack['ID'] ?>" method="post">
                        <? if($edit == $mappack['ID']){ ?>
                        <input style="flex-grow: 3" class="text" type="text" name="name" placeholder="Название" value="<? echo $mappack['name'] ?>" required>
                        <input style="flex-grow: 3" class="text" type="text" name="color" placeholder="000,000,000 (rgb)" value="<? echo $mappack['rgbcolors'] ?>" required>
                        <input class="text" type="tel" name="lvl1" placeholder="ID уровня" value="<? echo $lvls[0] ?>" required>
                        <input class="text" type="tel" name="lvl2" placeholder="ID уровня" value="<? echo $lvls[1] ?>" required>
                        <input class="text" type="tel" name="lvl3" placeholder="ID уровня" value="<? echo $lvls[2] ?>" required>
                        <input class="button" type="submit" name="submit" value="Сохранить">
                        <? } else { ?>
                        <p style="flex-grow: 3"><? echo $mappack['name'] ?></p>
                        <p style="flex-grow: 3">Цвет: <? echo $mappack['rgbcolors'] ?></p>
                        <p>ID: <? echo $lvls[0] ?></p>
                        <p>ID: <? echo $lvls[1] ?></p>
                        <p>ID: <? echo $lvls[2] ?></p>
                        <p>Звёзды: <? echo $mappack['stars'] ?></p>
                        <a href="admin.php?menu=mappacks&edit=<? echo $mappack['ID'] ?>">Редактировать</a>
                        <a href="admin.php?menu=mappacks&drop=<? echo $mappack['ID'] ?>">Удалить</a>
                        <? } ?>
                        </form>
                    <? } ?>
                </div>
                <? } elseif($menu === 'gauntlets'){ ?>
                <form class="addGauntlet" action="admin.php?menu=gauntlets&create=1" method="post">
                    <div id="title">
                        <b>Добавить Gauntlet</b>
                        <input class="button" type="submit" name="submit" value="Довавить">
                    </div>
                    <div class="line"></div>
                    <div id="block">
                        <select class="text" name="type">
                            <option value="1">Fire</option>
                            <option value="2">Ice</option>
                            <option value="3">Poison</option>
                            <option value="4">Shadow</option>
                            <option value="5">Lava</option>
                            <option value="6">Bonus</option>
                            <option value="7">Chaos</option>
                            <option value="8">Demon</option>
                            <option value="9">Time</option>
                            <option value="10">Crystal</option>
                            <option value="11">Magic</option>
                            <option value="12">Spike</option>
                            <option value="13">Monster</option>
                            <option value="14">Doom</option>
                            <option value="15">Death</option>
                        </select>
                        <input class="text" type="tel" name="lvl1" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl2" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl3" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl4" placeholder="ID уровня" required>
                        <input class="text" type="tel" name="lvl5" placeholder="ID уровня" required>
                    </div>
                </form>
                <div class="gauntlets">
                    <b>Gauntlet'ы</b>
                    <div class="line"></div>
                    <?php
                        foreach($gauntlets AS $gauntlet){
                            switch($gauntlet['ID']){
                                case 1: $gName = 'Fire'; break;
                                case 2: $gName = 'Ice'; break;
                                case 3: $gName = 'Poison'; break;
                                case 4: $gName = 'Shadow'; break;
                                case 5: $gName = 'Lava'; break;
                                case 6: $gName = 'Bonus'; break;
                                case 7: $gName = 'Chaos'; break;
                                case 8: $gName = 'Demon'; break;
                                case 9: $gName = 'Time'; break;
                                case 10: $gName = 'Crystal'; break;
                                case 11: $gName = 'Magic'; break;
                                case 12: $gName = 'Spike'; break;
                                case 13: $gName = 'Monster'; break;
                                case 14: $gName = 'Doom'; break;
                                case 15: $gName = 'Death'; break;
                                default: $gName = 'Unknown';
                            }
                    ?>
                    <form id="block" action="admin.php?menu=gauntlets&edit=<? echo $gauntlet['ID'] ?>" method="post">
                        <? if($edit == $gauntlet['ID']){ ?>
                        <select class="text" name="type">
                            <option value="1" <? if($gauntlet['ID'] == 1) echo 'selected' ?>>Fire</option>
                            <option value="2" <? if($gauntlet['ID'] == 2) echo 'selected' ?>>Ice</option>
                            <option value="3" <? if($gauntlet['ID'] == 3) echo 'selected' ?>>Poison</option>
                            <option value="4" <? if($gauntlet['ID'] == 41) echo 'selected' ?>>Shadow</option>
                            <option value="5" <? if($gauntlet['ID'] == 5) echo 'selected' ?>>Lava</option>
                            <option value="6" <? if($gauntlet['ID'] == 6) echo 'selected' ?>>Bonus</option>
                            <option value="7" <? if($gauntlet['ID'] == 7) echo 'selected' ?>>Chaos</option>
                            <option value="8" <? if($gauntlet['ID'] == 8) echo 'selected' ?>>Demon</option>
                            <option value="9" <? if($gauntlet['ID'] == 9) echo 'selected' ?>>Time</option>
                            <option value="10" <? if($gauntlet['ID'] == 10) echo 'selected' ?>>Crystal</option>
                            <option value="11" <? if($gauntlet['ID'] == 11) echo 'selected' ?>>Magic</option>
                            <option value="12" <? if($gauntlet['ID'] == 12) echo 'selected' ?>>Spike</option>
                            <option value="13" <? if($gauntlet['ID'] == 13) echo 'selected' ?>>Monster</option>
                            <option value="14" <? if($gauntlet['ID'] == 14) echo 'selected' ?>>Doom</option>
                            <option value="15" <? if($gauntlet['ID'] == 15) echo 'selected' ?>>Death</option>
                        </select>
                        <input class="text" type="tel" name="lvl1" placeholder="ID уровня" value="<? echo $gauntlet['level1'] ?>" required>
                        <input class="text" type="tel" name="lvl2" placeholder="ID уровня" value="<? echo $gauntlet['level2'] ?>" required>
                        <input class="text" type="tel" name="lvl3" placeholder="ID уровня" value="<? echo $gauntlet['level3'] ?>" required>
                        <input class="text" type="tel" name="lvl4" placeholder="ID уровня" value="<? echo $gauntlet['level4'] ?>" required>
                        <input class="text" type="tel" name="lvl5" placeholder="ID уровня" value="<? echo $gauntlet['level5'] ?>" required>
                        <input class="button" type="submit" name="submit" value="Сохранить">
                        <? } else { ?>
                        <p><? echo $gName ?></p>
                        <p>ID: <? echo $gauntlet['level1'] ?></p>
                        <p>ID: <? echo $gauntlet['level2'] ?></p>
                        <p>ID: <? echo $gauntlet['level3'] ?></p>
                        <p>ID: <? echo $gauntlet['level4'] ?></p>
                        <p>ID: <? echo $gauntlet['level5'] ?></p>
                        <a href="admin.php?menu=gauntlets&edit=<? echo $gauntlet['ID'] ?>">Редактировать</a>
                        <a href="admin.php?menu=gauntlets&drop=<? echo $gauntlet['ID'] ?>">Удалить</a>
                        <? } ?>
                    </form>
                    <? } ?>
                </div>
                <? } ?>
            </div>
            <? } else { ?>
            <div class="start">
                <div id="startBlock">
                    <div id="title">ВХОД</div>
                    <form id="form" method="post">
                        <input class="text" type="text" name="user" placeholder="Пользователь" minlength="5" required>
                        <input class="text" type="password" name="password" placeholder="Пароль" minlength="8" required>
                        <? if($error){ ?>
                        <div id="error">
                            <p>* <? echo $error ?></p>
                        </div>
                        <? } ?>
                        <input class="button" type="submit" name="submit" value="Войти">
                    </form>
                </div>
            </div>
            <? }} ?>
        <? } else { ?>
        <div class="start">
            <div id="startBlock">
                <div id="title">УСТАНОВКА</div>
                <form id="form" method="post">
                    <input class="text" type="text" name="src" placeholder="Путь к файлам сервера" required>
                    <input class="text" type="text" name="user" placeholder="Пользователь" minlength="5" required>
                    <input class="text" type="password" name="password" placeholder="Пароль" minlength="8" required>
                    <? if($error){ ?>
                    <div id="error">
                        <p>* <? echo $error ?></p>
                    </div>
                    <? } ?>
                    <input class="button" type="submit" name="submit" value="Установить">
                </form>
            </div>
        </div>
        <? } ?>
    </body>
</html>