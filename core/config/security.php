<?php
    // true - включить, false - выключить
        
    $onlyRegistered = false;

    $checkBanIP = true;

    $maxAttempts = 20;

    $limitAccounts = true;
    $maxAccounts = 3;
    $sendEmail = false;
    $encryption = false; // Шифрофание данных сохранений
    
    $cloudSaveEncryption = 0; //0 = password string replacement, 1 = cloud save encryption (password dependant)
    $sessionGrants = 0; //0 = GJP check is done every time; 1 = GJP check is done once per hour; drastically improves performance, slightly descreases security
?>