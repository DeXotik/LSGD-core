<?php
    chdir(dirname(__FILE__));

    include "../lib/connection.php";
    error_reporting(0);

    require_once "../lib/mainLib.php";
    $gs = new mainLib();
    $ip = $gs->getIP();

    $query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
    $query->execute([':ip' => $ip]);
    if($query->fetchColumn() > 0) exit("-1 (you IP have a ban)");

    $type = $_POST["type"];
    $string = ""; $x = 0;
    $time = time();

    if($type == "top" OR $type == "creators" OR $type == "relative" OR $type == "friends"){
        if($type == "top"){
            $query = $db->prepare("SELECT * FROM users WHERE stars > 0 AND isBanned = 0 AND lastPlayed > :time AND isRegistered = 1 ORDER BY stars DESC LIMIT 100");
            $query->execute([':time' => $time-7776000]);
        } elseif($type == "creators"){
            $query = $db->prepare("SELECT * FROM users WHERE creatorPoints > 0 AND isCreatorBanned = 0 AND lastPlayed > :time AND isRegistered = 1 ORDER BY creatorPoints DESC LIMIT 100");
            $query->execute([':time' => $time-7776000]);
        } elseif($type == "relative"){
            if(!empty($_POST["accountID"])){
                require_once "../lib/GJPCheck.php";
                $GJPCheck = new GJPCheck();

                $accountID = $_POST["accountID"];
	            $gjp = $_POST["gjp"];

                if($GJPCheck->check($gjp, $accountID) != 1) exit('-1');
            } else exit('-1');

            $query = $db->prepare("SELECT * FROM users WHERE extID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $user = $query->fetch();
            $stars = $user["stars"];

            $query = $db->prepare("SELECT A.* FROM (
                (SELECT * FROM users WHERE stars <= :stars AND isBanned = 0 AND lastPlayed > :time AND isRegistered = 1 ORDER BY stars DESC LIMIT 25)
                UNION
                (SELECT * FROM users WHERE stars >= :stars AND isBanned = 0 AND lastPlayed > :time AND isRegistered = 1 ORDER BY stars ASC LIMIT 25)
            ) as A ORDER BY A.stars DESC");
            $query->execute([':stars' => $stars, ':time' => $time-7776000]);
        } elseif($type == "friends"){
            if(!empty($_POST["accountID"])){
                require_once "../lib/GJPCheck.php";
                $GJPCheck = new GJPCheck();

                $accountID = $_POST["accountID"];
	            $gjp = $_POST["gjp"];

                if($GJPCheck->check($gjp, $accountID) != 1) exit('-1');
            } else exit('-1');

            $friendList = "";
            $query = $db->prepare("SELECT * FROM friendships WHERE person1 = :accountID OR person2 = :accountID");
            $query->execute([':accountID' => $accountID]);
            $friends = $query->fetchAll();
            foreach ($friends as $friend){
                if($friend["person1"] == $accountID){
                    $friendList .= $friend["person2"].', ';
                } else {
                    $friendList .= $friend["person1"].', ';
                }
            }

	        $query = $db->prepare("SELECT * FROM users WHERE extID IN ($friendList :accountID) AND lastPlayed > :time ORDER BY stars DESC");
            $query->execute([':accountID' => $accountID, ':time' => $time-7776000]);
        }

        $result = $query->fetchAll();
        if($type == "relative" OR $type == "friends"){
            $user = $result[0];
		    $extID = $user["extID"];

            $query = $db->prepare("SET @rownum := 0;"); $query->execute();
            $query = $db->prepare("SELECT A.num FROM (
                SELECT @rownum := @rownum + 1 AS num, stars, extID FROM users WHERE isBanned = 0 AND lastPlayed > :time AND isRegistered = 1 ORDER BY stars DESC
            ) AS A WHERE extID = :extID ORDER BY stars DESC");
            $query->execute([':extID' => $extID, ':time' => $time-7776000]);
            $leaderboard = $query->fetchColumn();
            $x = $leaderboard - 1;
        }
        foreach($result as $user){
            $x++;
            if(is_numeric($user["extID"])){
                $extid = $user["extID"];
            } else {
                $extid = 0;
            }
            $string .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$x.":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"].":7:".$extid.":46:".$user["diamonds"]."|";
        }

        if($string == "") exit('-1');
        $string = substr($string, 0, -1);
        exit($string);
    } else exit('-1');
?>