<?php
    include dirname(__FILE__)."/../config/connection.php";

    try {
        $db = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword, array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e){
        echo 'Connection failed: '.$e->getMessage();
    }
?>