<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require_once "../misc/commands.php";
$cmds = new Commands();
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$gjp = $ep->remove($_POST["gjp"]);
$userName = $ep->remove($_POST["userName"]);
$comment = $ep->remove($_POST["comment"]);
$id = $ep->remove($_POST["accountID"]);
$userID = $mainLib->getUserID($id, $userName);
$uploadDate = time();

$ip = $mainLib->getIP();
$query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
$query->execute([':ip' => $ip]);
if($query->fetchColumn() > 0) exit("-1 (you IP have a ban)");
$query = $db->prepare("SELECT value1 FROM aactions WHERE type = 5 AND (IP = :ip OR accountID = :accountID) AND time > :time");
$query->execute([':time' => $uploadDate - 3600, ':accountID' => $id, ':ip' => $ip]);
if($query->rowCount() > 0) exit('-1');
$query = $db->prepare("SELECT value1 FROM aactions WHERE type = 5 AND (IP = :ip OR accountID = :accountID) AND time > :time");
$query->execute([':time' => $uploadDate - 86400, ':accountID' => $id, ':ip' => $ip]);
if($query->rowCount() == 0){
	$query = $db->prepare("INSERT INTO aactions (type, IP, value1, time, accountID) VALUES (5, :ip, 1, :time, :accountID)");
	$query->execute([':time' => $uploadDate, ':accountID' => $id, ':ip' => $ip]);
} else {
	if($query->fetchColumn() < 20){
		$query = $db->prepare("UPDATE aactions SET value1 = value1 + 1 WHERE type = 5 AND (IP = :ip OR accountID = :accountID) AND time > :time");
		$query->execute([':time' => $uploadDate - 86400, ':accountID' => $id, ':ip' => $ip]);
	} else {
		$userName = $gs->getAccountName($accountID);
		$query = $db->prepare("INSERT INTO bans (IP, accountID, userName) VALUES (:ip, :accountID, :userName)");
		$query->execute([':ip' => $ip, ':accountID' => $id, ':userName' => $userName]);
		exit("-1 (server banned you IP)");
	}
}

//usercheck
if($id != "" AND $comment != "" AND $GJPCheck->check($gjp,$id) == 1){
	$decodecomment = base64_decode($comment);
	if($cmds->doProfileCommands($id, $decodecomment)){
		exit("-1");
	}
	
	$query = $db->prepare("SELECT count(*) FROM acccomments WHERE userID = :userID AND timestamp = :time");
	$query->execute([':userID' => $userID, ':time' => time()-3600]);
	if($query->fetchColumn() > 0) exit('-1');

	$query = $db->prepare("INSERT INTO acccomments (userName, comment, userID, timeStamp)
										VALUES (:userName, :comment, :userID, :uploadDate)");
	$query->execute([':userName' => $userName, ':comment' => $comment, ':userID' => $userID, ':uploadDate' => $uploadDate]);
	echo 1;
}else{
	echo -1;
}
?>