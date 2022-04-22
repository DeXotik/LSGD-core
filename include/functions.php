<?php
    class Functions{
        public function checkPassword($accountID, $password){
            include 'database.php';
            $query = $db->prepare("SELECT password FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $hash = $query->fetchColumn();
            return password_verify($password, $hash);
        }
        public function getAccountID($userName){
            include 'database.php';
            $query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName");
            $query->execute([':userName' => $userName]);
            if($query->rowCount() > 0){
                return $query->fetchColumn();
            } else return false;
        }
        public function online($accountID){
            include 'database.php';
            $query = $db->prepare("UPDATE users SET lastPlayed = :time WHERE extID = :accountID");
            if($query->execute([':time' => time(), ':accountID' => $accountID])){
                return true;
            } else return false;
        }
        public function ckeckOnline($accountID){
            include 'database.php';
            $query = $db->prepare("SELECT lastPlayed FROM users WHERE extID = :accountID");
            $query->execute([':accountID' => $accountID]);
            if($query->rowCount() > 0){
                $lastPlayed = $query->fetchColumn();
                $lastPlayed = time() - $lastPlayed;
                if($lastPlayed < 60){
                    return 'в сети';
                } elseif($lastPlayed < 3600){
                    $rounded = floor($lastPlayed/60); $string = substr($rounded, -1);
                    if($string == 1){
                        return 'был '.$rounded.' минуту назад';
                    } elseif($string >= 2 AND $string <= 4){
                        return 'был '.$rounded.' минуты назад';
                    } else {
                        return 'был '.$rounded.' минут назад';
                    }
                } elseif($lastPlayed < 86400){
                    $rounded = floor($lastPlayed/3600); $string = substr($rounded, -1);
                    if($string == 1){
                        return 'был '.$rounded.' час назад';
                    } elseif($string >= 2 AND $string <= 4){
                        return 'был '.$rounded.' часа назад';
                    } else {
                        return 'был'.$rounded.' часов назад';
                    }
                } elseif($lastPlayed < 604800){
                    $rounded = floor($lastPlayed/86400); $string = substr($rounded, -1);
                    if($string == 1){
                        return 'был '.$rounded.' день назад';
                    } elseif($string >= 2 AND $string <= 4){
                        return 'был '.$rounded.' дня назад';
                    } else {
                        return 'был '.$rounded.' дней назад';
                    }
                } elseif($lastPlayed < 2628000){
                    $rounded = floor($lastPlayed/604800); $string = substr($rounded, -1);
                    if($string == 1){
                        return 'был '.$rounded.' неделю назад';
                    } elseif($string >= 2 AND $string <= 4){
                        return 'был '.$rounded.' недели назад';
                    } else {
                        return 'был '.$rounded.' недель назад';
                    }
                } elseif($lastPlayed < 31536000){
                    $rounded = floor($lastPlayed/2628000);
                    if( $rounded == 1){
                        return 'был '.$rounded.' месяц назад';
                    } elseif( $rounded >= 2 AND  $rounded <= 4){
                        return 'был '.$rounded.' месяца назад';
                    } else {
                        return 'был '.$rounded.' месяцев назад';
                    }
                } else {
                    $rounded = floor($lastPlayed/31536000); $string = substr($rounded, -1);
                    if($string == 1){
                        return 'был '.$rounded.' год назад';
                    } elseif($string >= 2 AND $string <= 4){
                        return 'был '.$rounded.' года назад';
                    } else {
                        return 'был '.$rounded.' год назад';
                    }
                }
            } else return 'не в сети';
        }
    }
?>