<?php
class Commands {
	public function ownCommand($comment, $command, $accountID, $targetExtID){
		require_once "../lib/mainLib.php";
		$gs = new mainLib();
		$commandInComment = strtolower("!".$command);
		$commandInPerms = ucfirst(strtolower($command));
		$commandlength = strlen($commandInComment);
		if(substr($comment,0,$commandlength) == $commandInComment AND (($gs->checkPermission($accountID, "command".$commandInPerms."All") OR ($targetExtID == $accountID AND $gs->checkPermission($accountID, "command".$commandInPerms."Own"))))){
			return true;
		}
		return false;
	}
	public function doCommands($accountID, $comment, $levelID) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		$commentarray = explode(' ', $comment);
		$uploadDate = time();
		//LEVELINFO
		$query2 = $db->prepare("SELECT extID FROM levels WHERE levelID = :id");
		$query2->execute([':id' => $levelID]);
		$targetExtID = $query2->fetchColumn();



		//ADMIN COMMANDS

		if($commentarray[0] == '!rate' AND !empty($commentarray[1]) AND $gs->checkPermission($accountID, "actionRateDifficulty")){
			if(is_numeric($commentarray[1])){
				if($gs->checkPermission($accountID, "actionRateStars")){
					$query = $db->prepare("SELECT starStars, starFeatured FROM levels WHERE levelID = :levelID");
					$query->execute([':levelID' => $levelID]);
					$levelInfo = $query->fetch();
					$oldStars = $levelInfo['starStars'];
					$oldFeatured = $levelInfo['starFeatured'];
					$stars = floor($commentarray[1]); $auto = 0; $demon = 0; $featured = 0;
					if($stars < 1 OR $stars > 10) return 'temp_1_Syntax error';
					if(!empty($commentarray[2]) AND ($commentarray[2] == 0 OR $commentarray[2] == 1)) $featured = $commentarray[2];
					switch($stars){
						case 1: $diff = 50; $auto = 1; break;
						case 2: $diff = 10; break;
						case 3: $diff = 20; break;
						case 4: case 5: $diff = 30; break;
						case 6: case 7: $diff = 40; break;
						case 8: case 9: $diff = 50; break;
						case 10: $diff = 50; $demon = 1; break;
						default: $diff = 0;
					}

					$query = $db->prepare("UPDATE levels SET starDifficulty = :difficulty, starAuto = :auto, starDemon = :demon, starStars = :stars, starFeatured = :featured, starCoins = 1 WHERE levelID = :levelID");
					$query->execute([':difficulty' => $diff, ':auto' => $auto, ':demon' => $demon, ':stars' => $stars, ':featured' => $featured, ':levelID' => $levelID]);

					$userName = $gs->getAccountName($accountID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, value5, value6, value7, timestamp, account) VALUES (101, '!rate', :userName, :levelID, :stars, :featured, :oldStars, :oldFeatured, :time, :accountID)");
					$query->execute([':userName' => $userName, ':levelID' => $levelID, ':stars' => $stars, ':featured' => $featured, ':oldStars' => $oldStars, ':oldFeatured' => $oldFeatured, ':time' => $uploadDate, ':accountID' => $accountID]);

					return 'temp_1_Level succefuly rated';
				} else return 'temp_1_Error: no permission';
			} else {
				$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() == 0){
					$auto = 0; $demon = 0;
					$commentarray[1] = strtolower($commentarray[1]);
					switch($commentarray[1]){
						case 'auto': $diff = 50; $auto = 1; break;
						case 'easy': $diff = 10; break;
						case 'normal': $diff = 20; break;
						case 'hard': $diff = 30; break;
						case 'harder': $diff = 40; break;
						case 'insane': $diff = 50; break;
						case 'demon': $diff = 50; $demon = 1; break;
						default: return 'temp_1_Error: undefined difficulty';
					}

					$query = $db->prepare("UPDATE levels SET starDifficulty = :difficulty, starAuto = :auto, starDemon = :demon WHERE levelID = :levelID");
					$query->execute([':difficulty' => $diff, ':auto' => $auto, ':demon' => $demon, ':levelID' => $levelID]);

					$userName = $gs->getAccountName($accountID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, timestamp, account) VALUES (101, '!rate', :userName, :levelID, :diff, :time, :accountID)");
					$query->execute([':userName' => $userName, ':levelID' => $levelID, ':diff' => $commentarray[1], ':time' => $uploadDate, ':accountID' => $accountID]);

					return 'temp_1_Difficulty succefuly changed';
				} else return 'temp_1_Error: level have a stars';
			}
		}

		if($commentarray[0] == '!demon' AND !empty($commentarray[1]) AND $gs->checkPermission($accountID, "actionRateDemon")){
			$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			if($query->fetchColumn() > 0){
				$query = $db->prepare("SELECT starDemon FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() == 1){
					$commentarray[1] = strtolower($commentarray[1]);
					switch($commentarray[1]){
						case 'easy': $diff = 3; break;
						case 'medium': $diff = 4; break;
						case 'hard': $diff = 0; break;
						case 'insane': $diff = 5; break;
						case 'extreme': $diff = 6; break;
						default: return 'temp_1_Error: undefined difficulty';
					}

					$query = $db->prepare("UPDATE levels SET starDemonDiff = :difficulty WHERE levelID = :levelID");
					$query->execute([':difficulty' => $diff, ':levelID' => $levelID]);

					$userName = $gs->getAccountName($accountID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, timestamp, account) VALUES (102, '!demon', :userName, :levelID, :diff, :time, :accountID)");
					$query->execute([':userName' => $userName, ':levelID' => $levelID, ':diff' => $commentarray[1], ':time' => $uploadDate, ':accountID' => $accountID]);

					return 'temp_1_Demon difficulty succefuly changed';
				} else return 'temp_1_Error: level is not a demon';
			} else return "temp_1_Error: level don't have a stars";
		}

		if($commentarray[0] == '!unrate' AND $gs->checkPermission($accountID, "actionRateStars")){
			$query = $db->prepare("UPDATE levels SET starDifficulty = 0, starAuto = 0, starDemon = 0, starStars = 0, starFeatured = 0, starEpic = 0, starHall = 0, starDemonDiff = 0, starCoins = 0, rateDate = 0 WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);

			$userName = $gs->getAccountName($accountID);
			$query = $db->prepare("SELECT levelName, extID FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$levelInfo = $query->fetch();
			$name = $levelInfo['levelName'];
			$author = $gs->getAccountName($levelInfo['extID']);
			$message = "&#128100; Модератор {$userName} снял оценку с уровня \"{$name}\" by {$author}";

			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES (100, '!unrate', :userName, :levelID, :time, :accountID)");
			$query->execute([':userName' => $userName, ':levelID' => $levelID, ':time' => $uploadDate, ':accountID' => $accountID]);

			$array = [
				'peer_id' => 2000000004,
				'message' => $message
			];
			$gs->vk('messages.send', $array);

			return 'temp_1_Level succefuly unrated';
		}

		if($commentarray[0] == '!suggest' AND !empty($commentarray[1]) AND $gs->checkPermission($accountID, "actionSuggestRating")){
			$stars = floor($commentarray[1]); $auto = 0; $demon = 0; $featured = 0;
			if($stars < 1 OR $stars > 10) return 'temp_1_Syntax error';
			if(!empty($commentarray[2]) AND ($commentarray[2] == 0 OR $commentarray[2] == 1)) $featured = $commentarray[2];
			switch($stars){
				case 1: $diff = 50; $auto = 1; break;
				case 2: $diff = 10; break;
				case 3: $diff = 20; break;
				case 4: case 5: $diff = 30; break;
				case 6: case 7: $diff = 40; break;
				case 8: case 9: $diff = 50; break;
				case 10: $diff = 50; $demon = 1; break;
				default: $diff = 0;
			}

			$query = $db->prepare("SELECT count(*) FROM suggest WHERE suggestLevelId = :levelID AND suggestBy = :accountID");
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID]);
			if($query->fetchColumn() == 0){
				$difficulty = $gs->getDiffFromStars($stars);
				$query = $db->prepare("INSERT INTO suggest (suggestBy, suggestLevelId, suggestDifficulty, suggestStars, suggestAuto, suggestDemon, suggestFeatured, timestamp) VALUES (:accountID, :levelID, :difficulty, :stars, :auto, :demon, :featured, :time)");
				$query->execute([':accountID' => $accountID, ':levelID' => $levelID, ':difficulty' => $diff, ':stars' => $stars, ':auto' => $auto, ':demon' => $demon, ':featured' => $featured, ':time' => $uploadDate]);

				$query = $db->prepare("SELECT count(*) FROM suggestlevels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() == 0){
					$query = $db->prepare("INSERT INTO suggestlevels (levelID, timestamp) VALUES (:levelID, :time)");
					$query->execute([':levelID' => $levelID, ':time' => $uploadDate]);
				}

				return 'temp_1_Level succefuly sended for rating';
			} else return 'temp_1_You already sended level';
		}

		if($commentarray[0] == '!featured' AND $gs->checkPermission($accountID, "commandFeature")){
			$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			if($query->fetchColumn() > 0){
				$query = $db->prepare("SELECT starEpic FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() == 0){
					$query = $db->prepare("SELECT starFeatured FROM levels WHERE levelID = :levelID");
					$query->execute([':levelID' => $levelID]);
					$featured = $query->fetchColumn();
					if($featured == 0){ $featured = 1; } else { $featured = 0; }
					$query = $db->prepare("UPDATE levels SET starFeatured = :featured WHERE levelID = :levelID");
					$query->execute([':featured' => $featured, ':levelID' => $levelID]);

					$userName = $gs->getAccountName($accountID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, timestamp, account) VALUES (103, '!featured', :userName, :levelID, :featured, :time, :accountID)");
					$query->execute([':userName' => $userName, ':levelID' => $levelID, ':featured' => $featured, ':time' => $uploadDate, ':accountID' => $accountID]);

					return "temp_1_Featured succefuly changed";
				} else return "temp_1_Error: level have epic";
			} else return "temp_1_Error: level don't have a stars";
		}

		if($commentarray[0] == '!epic' AND $gs->checkPermission($accountID, "commandEpic")){
			$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			if($query->fetchColumn() > 0){
				$query = $db->prepare("SELECT starFeatured FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);
				if($query->fetchColumn() > 0){
					$query = $db->prepare("SELECT starEpic FROM levels WHERE levelID = :levelID");
					$query->execute([':levelID' => $levelID]);
					$epic = $query->fetchColumn();
					if($epic == 0){ $epic = 1; } else { $epic = 0; }
					$query = $db->prepare("UPDATE levels SET starEpic = :epic WHERE levelID = :levelID");
					$query->execute([':epic' => $epic, ':levelID' => $levelID]);

					$userName = $gs->getAccountName($accountID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, timestamp, account) VALUES (104, '!epic', :userName, :levelID, :epic, :time, :accountID)");
					$query->execute([':userName' => $userName, ':levelID' => $levelID, ':epic' => $epic, ':time' => $uploadDate, ':accountID' => $accountID]);

					return "temp_1_Epic succefuly changed";
				} else return "temp_1_Error: level is not a featured";
			} else return "temp_1_Error: level don't have a stars";
		}

		if($commentarray[0] == '!coins' AND $gs->checkPermission($accountID, "commandVerifycoins")){
			$query = $db->prepare("SELECT starCoins FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$coins = $query->fetchColumn();
			if($coins == 0){ $coins = 1; } else { $coins = 0; }
			$query = $db->prepare("UPDATE levels SET starCoins = :coins WHERE levelID = :levelID");
			$query->execute([':coins' => $coins, ':levelID' => $levelID]);

			$userName = $gs->getAccountName($accountID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value4, timestamp, account) VALUES (105, '!coins', :userName, :levelID, :coins, :time, :accountID)");
			$query->execute([':userName' => $userName, ':levelID' => $levelID, ':coins' => $coins, ':time' => $uploadDate, ':accountID' => $accountID]);

			return "temp_1_Coins succefuly changed";
		}

		if(substr($comment,0,6) == '!daily' AND $gs->checkPermission($accountID, "commandDaily")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 0");
				$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return 'temp_1_Level already sended to Daily';
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 0 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':tomorrow' => strtotime("tomorrow 00:00:00")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("tomorrow 00:00:00");
			}else{
				$timestamp = $query->fetchColumn() + 86400;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 0)");
			$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			
			$userName = $gs->getAccountName($accountID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value7, timestamp, account) VALUES (107, '!daily', :userName, :levelID, :send, :time, :accountID)");
			$query->execute([':userName' => $userName, ':levelID' => $levelID, ':send' => $timestamp, ':time' => $uploadDate, ':accountID' => $accountID]);

			$query = $db->prepare("SELECT levelName, extID FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$levelInfo = $query->fetch();
			$name = $levelInfo['levelName'];
			$author = $gs->getAccountName($levelInfo['extID']);
			$message = "&#9654; Уровень \"{$name}\" by {$author} был отправлен в Daily";

			$array = [
				'peer_id' => 2000000004,
				'message' => $message
			];
			$gs->vk('messages.send', $array);

			return 'temp_1_Level sended on Daily';
		}

		if(substr($comment,0,7) == '!weekly' AND $gs->checkPermission($accountID, "commandWeekly")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 1");
			$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return 'temp_1_Level already sended to Weekly';
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 1 ORDER BY timestamp DESC LIMIT 1");
				$query->execute([':tomorrow' => strtotime("next monday")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("next monday");
			}else{
				$timestamp = $query->fetchColumn() + 604800;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 1)");
			$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			
			$userName = $gs->getAccountName($accountID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, value7, timestamp, account) VALUES (108, '!weekly', :userName, :levelID, :send, :time, :accountID)");
			$query->execute([':userName' => $userName, ':levelID' => $levelID, ':send' => $timestamp, ':time' => $uploadDate, ':accountID' => $accountID]);
			
			$query = $db->prepare("SELECT levelName, extID FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$levelInfo = $query->fetch();
			$name = $levelInfo['levelName'];
			$author = $gs->getAccountName($levelInfo['extID']);
			$message = "&#9654; Уровень \"{$name}\" by {$author} был отправлен в Weekly";

			$array = [
				'peer_id' => 2000000004,
				'message' => $message
			];
			$gs->vk('messages.send', $array);
			
			return 'temp_1_Level sended on Weekly';
		}

		if($commentarray[0] == '!delete' AND $gs->checkPermission($accountID, "commandDelete")){
			$query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			if($query->fetchColumn() == 0){
				$query = $db->prepare("DELETE FROM levels WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID]);

				$userName = $gs->getAccountName($accountID);
				$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES (106, '!delete', :userName, :levelID, :time, :accountID)");
				$query->execute([':userName' => $userName, ':levelID' => $levelID, ':time' => $uploadDate, ':accountID' => $accountID]);

				return 'temp_1_Level succefuly deleted';
			} else return 'temp_1_Error: level have a stars';
		}


		
	//NON-ADMIN COMMANDS
		if($this->ownCommand($comment, "rename", $accountID, $targetExtID)){
			$name = $ep->remove(str_replace("!rename ", "", $comment));
			$query = $db->prepare("UPDATE levels SET levelName=:levelName WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':levelName' => $name]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('8', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $name, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return 'temp_1_Level name succefuly changed';
		}
		if($this->ownCommand($comment, "pass", $accountID, $targetExtID)){
			$pass = $ep->remove(str_replace("!pass ", "", $comment));
			if(is_numeric($pass)){
				$pass = sprintf("%06d", $pass);
				if($pass == "000000"){
					$pass = "";
				}
				$pass = "1".$pass;
				$query = $db->prepare("UPDATE levels SET password=:password WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID, ':password' => $pass]);
				$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('9', :value, :timestamp, :id, :levelID)");
				$query->execute([':value' => $pass, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return 'temp_1_Level password succefuly changed';
			}
		}
		if($this->ownCommand($comment, "description", $accountID, $targetExtID)){
			$desc = base64_encode($ep->remove(str_replace("!description ", "", $comment)));
			$query = $db->prepare("UPDATE levels SET levelDesc=:desc WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':desc' => $desc]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('13', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $desc, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return 'temp_1_Level description succefuly changed';
		}
		if($this->ownCommand($comment, "public", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET unlisted='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return 'temp_1_Level set to public';
		}
		if($this->ownCommand($comment, "unlist", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET unlisted='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return 'temp_1_Level set to unlisted';
		}
		return false;
	}
	public function doProfileCommands($accountID, $command){
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		if(substr($command, 0, 8) == '!discord'){
			if(substr($command, 9, 6) == "accept"){
				$query = $db->prepare("UPDATE accounts SET discordID = discordLinkReq, discordLinkReq = '0' WHERE accountID = :accountID AND discordLinkReq <> 0");
				$query->execute([':accountID' => $accountID]);
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Your link request to " . $account["userName"] . " has been accepted!");
				return true;
			}
			if(substr($command, 9, 4) == "deny"){
				$query = $db->prepare("SELECT discordLinkReq, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordLinkReq"], "Your link request to " . $account["userName"] . " has been denied!");
				$query = $db->prepare("UPDATE accounts SET discordLinkReq = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
			if(substr($command, 9, 6) == "unlink"){
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Your Discord account has been unlinked from " . $account["userName"] . "!");
				$query = $db->prepare("UPDATE accounts SET discordID = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
		}
		return false;
	}
}
?>