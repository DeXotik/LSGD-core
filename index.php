<?php
    include 'include/config.php';

    $stats = json_decode(file_get_contents($api.'/get.stats'));
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
        <meta property="og:image" content="img/bg.png" />
        <meta property="og:image:secure_url" content="img/bg.png" />
        <meta property="og:image:type" content="image/png" />
        <meta property="og:image:width" content="1590px" />
        <meta property="og:image:height" content="400px" />
        <meta property="og:image:alt" content="bg.png" />
        <link id="themeLink" rel="stylesheet" href="css/<? echo $theme ?>.css?<? echo $v ?>">
        <link rel="stylesheet" href="css/config.css?<? echo $v ?>">
        <link rel="stylesheet" href="css/index.css?<? echo $v ?>">
        <link rel="shortcut icon" href="img/favicon.ico?<? echo $v ?>" type="image/x-icon">
        <title>LSGD • Home</title>
        <? require_once 'include/script.php' ?>
    </head>
    <body>
        <header>
            <a href="/">LSGD</a>
            <div id="theme" onclick="changeTheme()"><div></div></div>
        </header>
        <div class="container">
            <div class="main">
                <b id="title">Добро пожаловать на LSGD</b>
                <div>
                    <div id="desc">
                        <p><b>LSGD</b> - один из лучших приватных серверов по игре Geometry Dash. В чём же плюсы нашего сервера? Очень просто. У нас более защищённое ядро, чем у других серверов, приятный и удобный сайт, а также ламповое комьюнити.</p>
                    </div>
                    <div id="download">
                        <b>Скачать LSGD</b>
                        <div class="button" onclick="openDownload()">Скачать</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="stats">
                <div id="block">
                    <b>Игроки</b>
                    <p><? echo $stats->users ?></p>
                </div>
                <div id="block">
                    <b>Аккаунты</b>
                    <p><? echo $stats->accounts ?></p>
                </div>
                <div id="block">
                    <b>Уровни</b>
                    <p><? echo $stats->levels ?></p>
                </div>
                <div id="block">
                    <b>Комментарии</b>
                    <p><? echo $stats->comments ?></p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="features">
                <div>
                    <b>Войди в аккаунт для новых возможностей</b>
                    <p>В будущем на сайте</p>
                </div>
                <img id="user" src="img/icons/user.png" alt="user.png">
            </div>
        </div>
        <div class="container">
            <div class="features">
                <img id="api" src="img/icons/api.png" alt="api.png">
                <div>
                    <b>LSGD API</b>
                    <p>Это метод взаимодействия с нашим приватным сервером. С помощью него можно взаимодействовать с аккаунтом, выводить статистику о игроке, уровне, сервере, а также много других возможностей.</p>
                    <a href="https://api.lsgd.tk">Больше о LSGD API</a>
                </div>
            </div>
        </div>
        <footer>
            <div id="info">
                <div id="infoBlock">
                    <p>Создатель сервера:</p>
                    <p>Владелец сервера:</p>
                    <p>Музыка в меню игры:</p>
                    <p>Музыка в режиме практики:</p>
                </div>
                <div id="infoBlock">
                    <p>TopLoox</p>
                    <p>DeXotik</p>
                    <p>Teminite - Unstoppable</p>
                    <p>REZZ & deadmau5 - Hypnocurrency</p>
                </div>
                <div id="links">
                    <a href="https://vk.com/lsgd_server"><img src="img/icons/vk.png" alt="vk.png"></a>
                    <a href="https://discord.gg/FwZjRqbxT3"><img src="img/icons/discord.png" alt="discord.png"></a>
                </div>
            </div>
            <p id="author"><a href="https://vk.com/dexotik">DeXotik</a> &#169; 2020 - 2021</p>
        </footer>
        <div class="download hide" id="downloadBlock">
            <div id="block">
                <div id="title">
                    <b>Скачать LSGD</b>
                    <p id="close" onclick="closeDownload()">&#215;</p>
                </div>
                <div id="buttons">
                    <a href="download/LSGD Installer.exe" download><div id="windows"><img src="img/icons/windows.png" alt="windows.png"></div></a>
                    <a href="download/lsgd.apk" download><div id="android"><img src="img/icons/android.png" alt="android.png"></div></a>
                </div>
            </div>
        </div>
    </body>
</html>