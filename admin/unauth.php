<?php
    setcookie('auth', null, time(), '/');
    header('Location: /admin.php');
?>