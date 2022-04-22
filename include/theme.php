<?php
    if(!empty($_GET['theme'])){
        $theme = $_GET['theme'];
        if($theme == 'light' OR $theme == 'dark'){
            setcookie('theme', $theme, time()+(60*60*24*7), '/');
        }
    }
?>