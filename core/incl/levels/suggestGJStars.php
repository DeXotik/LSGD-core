<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
if($stars < 1 OR $stars > 10) exit('-1');
$feature = $ep->remove($_POST["feature"]);
if($feature != 0 AND $feature != 1) exit('-1');
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if($accountID != "" AND $gjp != ""){
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp, $accountID);
	if($gjpresult == 1){
		$query = $db->prepare("SELECT levelName, starStars, levelLength, extID FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		$levelInfo = $query->fetch();
		if($gs->checkPermission($accountID, 'actionRateStars') == 1 AND $levelInfo['starStars'] == 0 AND $levelInfo['levelLength'] >= 2){
			$difficulty = $gs->getDiffFromStars($stars); $rateDate = time();
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, account, timestamp) VALUES (228, :levelID, :difficulty, :stars, :feature, :accountID, :time)");
			$query->execute([':levelID' => $levelID, ':difficulty' => $difficulty['name'], ':stars' => $stars, ':feature' => $feature, ':accountID' => $accountID, ':time' => $rateDate]);
			$query = $db->prepare("UPDATE levels SET starDifficulty = :difficulty, starAuto = :auto, starDemon = :demon, starStars = :stars, starFeatured = :feature, starCoins = 1, rateDate = :time WHERE levelID = :levelID");
			$query->execute([':difficulty' => $difficulty['diff'], ':auto' => $difficulty['auto'], ':demon' => $difficulty['demon'], ':stars' => $stars, ':feature' => $feature, ':time' => $rateDate, ':levelID' => $levelID]);

			$userName = $gs->getAccountName($accountID);
			$levelName = $levelInfo['levelName'];
			$diffName = $difficulty['name'];
			$author = $gs->getAccountName($levelInfo['extID']);
			$message = "&#128100; Модератор {$userName} оценил уровнь \"{$levelName}\" by {$author}\n&#127380; ID уровня: {$levelID}\n&#128545; Сложность: {$diffName}\n&#11088; Кол-во звёзд: {$stars}";
			if($feature == 1) $message .= "\n*&#65039;&#8419; Дополнительно: Featured";

			$array = [
				'peer_id' => 2000000004,
				'message' => $message
			];
			$gs->vk('messages.send', $array);

			exit('1');
		} elseif($gs->checkPermission($accountID, 'actionSuggestRating') == 1 AND $levelInfo['starStars'] == 0 AND $levelInfo['levelLength'] >= 2){
			$difficulty = $gs->getDiffFromStars($stars); $rateDate = time();
			$query = $db->prepare("SELECT count(*) FROM suggest WHERE suggestBy = :accountID AND suggestLevelId = :levelID");
			$query->execute([':accountID' => $accountID, ':levelID' => $levelID]);
			if($query->fetchColumn() == 0){
				$query = $db->prepare("INSERT INTO suggest (suggestBy, suggestLevelId, suggestDifficulty, suggestAuto, suggestDemon, suggestStars, suggestFeatured, timestamp) VALUES (:accountID, :levelID, :difficulty, :auto, :demon, :stars, :featured, :time)");
				$query->execute([':accountID' => $accountID, ':levelID' => $levelID, ':difficulty' => $difficulty['diff'], ':auto' => $difficulty['auto'], ':demon' => $difficulty['demon'], ':stars' => $stars, ':featured' => $feature, ':time' => $rateDate]);
			} else {
				$query = $db->prepare("UPDATE suggest SET suggestDifficulty = :difficulty, suggestAuto = :auto, suggestDemon = :demon, suggestStars = :stars, suggestFeatured = :featured, timestamp = :time WHERE suggestBy = :accountID AND suggestLevelId = :levelID");
				$query->execute([':accountID' => $accountID, ':levelID' => $levelID, ':difficulty' => $difficulty['diff'], ':auto' => $difficulty['auto'], ':demon' => $difficulty['demon'], ':stars' => $stars, ':featured' => $feature, ':time' => $rateDate]);
			}

			exit('1');
		} else exit('-1');
	} else exit('-1');
} else exit('-1');
?>