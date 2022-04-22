<?php
    class Functions{
        public function remove($string){
            return trim(explode(")", str_replace("\0", "", explode("#", explode("~", explode("|", explode(":", trim(htmlspecialchars($string,ENT_QUOTES)))[0])[0])[0])[0]))[0]);
        }
        public function number($string){
            return preg_replace("/[^0-9]/", '', $string);
        }
        public function string($string){
            return preg_replace("/[^a-zA-Z0-9]/", '', $string);
        }
        public function checkEmpty($string){
            $string = trim($string);
            if($string == ''){
                return false;
            } else {
                return $string;
            }
        }
        public function getIP(){
            if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){ 
                return $_SERVER['HTTP_CF_CONNECTING_IP'];
            } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND ipInRange::ipv4_in_range($_SERVER['REMOTE_ADDR'], '127.0.0.0/8')){
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }
        public function checkBanIP($ip){
            include "connection.php";
            $query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
            $query->execute([':ip' => $ip]);
            if($query->fetchColumn() > 0){
                return false;
            } else {
                return true;
            }
        }
        public function checkAttempts($type, $ip){
            include "connection.php";
            include "../config/security.php";
            $query = $db->prepare("SELECT value1 FROM aactions WHERE type = :type AND IP = :ip AND time > :time");
            $query->execute([':type' => $type, ':ip' => $ip, ':time' => time()-86400]);
            if($query->rowCount() > 0){
                if($query->fetchColumn() < $maxAttempts){
                    return true;
                } else {
                    $query = $db->prepare("INSERT INTO bans(IP) VALUES (:ip)");
                    $query->execute([':ip' => $ip]);
                    return false;
                }
            } else {
                return true;
            }
        }
        public function editAttempts($type, $ip){
            include "connection.php";
            $query = $db->prepare("SELECT value1 FROM aactions WHERE type = :type AND IP = :ip AND time > :time");
            $query->execute([':type' => $type, ':ip' => $ip, ':time' => time()-86400]);
            if($query->rowCount() > 0){
                $attempt = $query->fetchColumn(); $attempt = $attempt + 1;
                $query = $db->prepare("UPDATE aactions SET value1 = :attempt WHERE type = :type AND IP = :ip");
                $query->execute([':attempt' => $attempt, ':type' => $type, ':ip' => $ip]);
            } else {
                $query = $db->prepare("INSERT INTO aactions (type, IP, value1, time) VALUES (:type, :ip, 1, :time)");
                $query->execute([':type' => $type, ':ip' => $ip, ':time' => time()]);
            }
            return true;
        }
        public function encodePassword($password){
            return password_hash($password, PASSWORD_DEFAULT);
        }
        public function checkPassword($accountID, $password){
            include "connection.php";
            $query = $db->prepare("SELECT password FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $hash = $query->fetchColumn();
            return password_verify($password, $hash);
        }
        public function checkGJP($accountID, $gjp){
            include "connection.php";
            $gjpDecode = str_replace('_', '/', $gjp);
		    $gjpDecode = str_replace('-', '+', $gjpDecode);
            $gjpDecode = base64_decode($gjpDecode);
            $key = array_map('ord', str_split(37526));
            $plaintext = array_map('ord', str_split($gjpDecode));
            $keysize = count($key);
            $input_size = count($plaintext);
            $cipher = '';
            for ($i = 0; $i < $input_size; $i++){
                $cipher .= chr($plaintext[$i] ^ $key[$i % $keysize]);
            }
            $password = $cipher;
            $query = $db->prepare("SELECT password FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $hash = $query->fetchColumn();
            return password_verify($password, $hash);
        }
        public function getAccountID($userName){
            include "connection.php";
            $query = $db->prepare("SELECT accountID FROM accounts WHERE userName LIKE :userName");
            $query->execute([':userName' => $userName]);
            return $query->fetchColumn();
        }
        public function getUserName($accountID){
            include "connection.php";
            $query = $db->prepare("SELECT userName FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            return $query->fetchColumn();
        }
        public function getUserCapability($accountID, $string){
            include "connection.php";
            $query = $db->prepare("SELECT roles.{$string} FROM roles INNER JOIN roleassign ON roles.roleID = roleassign.roleID WHERE roleassign.accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            if($query->rowCount() > 0){
                return $query->fetchColumn();
            } else {
                return 0;
            }
        }
        public function online($accountID){
            include "connection.php";
            $query = $db->prepare("UPDATE users SET lastPlayed = :time WHERE extID = :accountID");
            if($query->execute([':time' => time(), ':accountID' => $accountID])){
                return true;
            } else return false;
        }
        public function ckeckOnline($accountID){
            include "connection.php";
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
        public function genString($length = 10){
            $values = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789';
            $string = '';
            for($i = 0; $i < $length; $i++){
                $string .= $values[rand(0, mb_strlen($values)-1)];
            }
            return $string;
        }
        public function vk($method, $array){
            $config = [
                "access_token" => "",
                "v" => "5.126",
                "random_id" => rand(1, 999999999)
            ];
    
            $params = http_build_query(array_merge($array, $config));
    
            return json_decode(file_get_contents('https://api.vk.com/method/'.$method.'?'.$params));
        }
    }
?>