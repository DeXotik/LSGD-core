<?php
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
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if($accountID > 0 AND $gjp != ""){
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp, $accountID);
	if($gjpresult == 1){
		$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		if($query->fetchColumn() == 0){
			$query = $db->prepare("SELECT count(*) FROM actions WHERE type = 228 AND value = :levelID AND account = :accountID");
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID]);
			if($query->fetchColumn() == 0){
				$stars = floor($stars);
				$auto = 0; $demon = 0;
				switch($stars){
					case 1: $diff = 50; $auto = 1; break;
					case 2: $diff = 10; break;
					case 3: $diff = 20; break;
					case 4: case 5: $diff = 30; break;
					case 6: case 7: $diff = 40; break;
					case 8: case 9: case 10: $diff = 50; break;
					default: $diff = 0;
				}

				$query = $db->prepare("SELECT starDifficulty FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() == 0 AND $gs->checkPermission($accountID, 'actionRateDifficulty') == 1){
					$query = $db->prepare("UPDATE levels SET starDifficulty = :difficulty, starAuto = :auto, starDemon = :demon WHERE levelID = :levelID");
					$query->execute([':difficulty' => $diff, ':auto' => $auto, ':demon' => $demon, ':levelID' => $levelID]);
				}

				$difficulty = $gs->getDiffFromStars($stars); $difficulty = $difficulty['name'];
				$query = $db->prepare("INSERT INTO actions (type, value, value2, value3, account, timestamp) VALUES (228, :levelID, :difficulty, :stars, :accountID, :time)");
				$query->execute([':levelID' => $levelID, ':difficulty' => $difficulty, ':stars' => $stars, ':accountID' => $accountID, ':time' => time()]);

				$query = $db->prepare("SELECT count(*) AS count, sum(value3) AS sum FROM actions WHERE type = 228 AND value = :levelID");
				$query->execute([':levelID' => $levelID]);
				$query = $query->fetch();
				if($query['count'] >= 3){
					$stars = round($query['sum']/$query['count']);
					switch($stars){
						case 1: $diff = 50; $auto = 1; break;
						case 2: $diff = 10; break;
						case 3: $diff = 20; break;
						case 4: case 5: $diff = 30; break;
						case 6: case 7: $diff = 40; break;
						case 8: case 9: case 10: $diff = 50; break;
						default: $diff = 0;
					}

					$query = $db->prepare("UPDATE levels SET starDifficulty = :difficulty, starAuto = :auto, starDemon = :demon WHERE levelID = :levelID");
					$query->execute([':difficulty' => $diff, ':auto' => $auto, ':demon' => $demon, ':levelID' => $levelID]);
				}

				echo '1';
			} else exit('1');
		} else exit('1');
	} else exit('-1');
} else exit('-1');