<?php
    header('Content-type: text/html; charset=utf-8');
    error_reporting(0);

    include 'database.php';
    require_once 'functions.php';
    $f = new Functions();

    $api = 'https://api.lsgd.tk';
    $v = 'v=1.0200';
    
    if(!empty($_COOKIE['theme'])){
        $theme = $_COOKIE['theme'];
    } else $theme = 'light';
?>