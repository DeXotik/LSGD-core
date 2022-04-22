<?php
    error_reporting(0);
    include dirname(__FILE__)."/../../config/connection.php";
    @header('Content-Type: text/html; charset=utf-8');
    
    try {
        $db = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword, array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e){
        echo 'Connection failed: '.$e->getMessage();
    }
?>