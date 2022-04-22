<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
$gjp =  $ep->remove($_POST["gjp"]);
$gameVersion =  $ep->remove($_POST["gameVersion"]);
$binaryVersion =  $ep->remove($_POST["binaryVersion"]);
$secret =  $ep->remove($_POST["secret"]);
$subject =  $ep->remove($_POST["subject"]);
$toAccountID =  $ep->number($_POST["toAccountID"]);
$body =  $ep->remove($_POST["body"]);
$accID =  $ep->number($_POST["accountID"]);
if($accID == $toAccountID){
	exit("-1");
}

$ip = $gs->getIP();
$query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
$query->execute([':ip' => $ip]);
if($query->fetchColumn() > 0) exit("-1 (you IP have a ban)");

$query3 = "SELECT userName FROM users WHERE extID = :accID ORDER BY userName DESC";
$query3 = $db->prepare($query3);
$query3->execute([':accID' => $accID]);
$userName = $query3->fetchColumn();
//continuing the accounts system
$id = $ep->remove($_POST["accountID"]);
$register = 1;
$userID = $gs->getUserID($id);
$uploadDate = time();

$blocked = $db->query("SELECT ID FROM `blocks` WHERE person1 = $toAccountID AND person2 = $accID")->fetchAll(PDO::FETCH_COLUMN);
$mSOnly = $db->query("SELECT mS FROM `accounts` WHERE accountID = $toAccountID AND mS > 0")->fetchAll(PDO::FETCH_COLUMN);
$friend = $db->query("SELECT ID FROM `friendships` WHERE (person1 = $accID AND person2 = $toAccountID) || (person2 = $accID AND person1 = $toAccountID)")->fetchAll(PDO::FETCH_COLUMN);

$query = $db->prepare("SELECT value1 FROM aactions WHERE type = 4 AND (IP = :ip OR accountID = :accountID) AND time > :time");
$query->execute([':time' => $uploadDate - 86400, ':accountID' => $accID, ':ip' => $ip]);
if($query->rowCount() == 0){
	$query = $db->prepare("INSERT INTO aactions (type, IP, value1, time, accountID) VALUES (4, :ip, 1, :time, :accountID)");
	$query->execute([':time' => $uploadDate, ':accountID' => $accID, ':ip' => $ip]);
} else {
	if($query->fetchColumn() < 20){
		$query = $db->prepare("UPDATE aactions SET value1 = value1 + 1 WHERE type = 4 AND (IP = :ip OR accountID = :accountID) AND time > :time");
		$query->execute([':time' => $uploadDate - 86400, ':accountID' => $accID, ':ip' => $ip]);
	} else {
		$userName = $gs->getAccountName($accountID);
		$query = $db->prepare("INSERT INTO bans (IP, accountID, userName) VALUES (:ip, :accountID, :userName)");
		$query->execute([':ip' => $ip, ':accountID' => $accID, ':userName' => $userName]);
		exit("-1 (server banned you IP)");
	}
}

$query = $db->prepare("INSERT INTO messages (subject, body, accID, userID, userName, toAccountID, secret, timestamp)
VALUES (:subject, :body, :accID, :userID, :userName, :toAccountID, :secret, :uploadDate)");

$GJPCheck = new GJPCheck();
$gjpresult = $GJPCheck->check($gjp,$id);
if (!empty($mSOnly[0]) and $mSOnly[0] == 2) {
    echo -1;
} else {
    if ($gjpresult == 1 and empty($blocked[0]) and (empty($mSOnly[0]) || !empty($friend[0]))) {
        $query->execute([':subject' => $subject, ':body' => $body, ':accID' => $id, ':userID' => $userID, ':userName' => $userName, ':toAccountID' => $toAccountID, ':secret' => $secret, ':uploadDate' => $uploadDate]);
        $query = $db->prepare("INSERT INTO aactions (type, IP, time, accountID) VALUES (4, :ip, :time, :accountID)");
        $query->execute([':ip' => $ip, ':time' => time(), ':accountID' => $accID]);
        echo 1;
    } else {
        echo -1;
    }
}
?>