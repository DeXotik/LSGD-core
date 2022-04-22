<hr><?php
	include "../../incl/lib/connection.php";
	echo "Autoban started<br>";

	$maxStars = 190 + 100; // Максимальное кол-во звёзд во всех официальных уровнях + 100 в запас, в случае чего
	$maxDemons = 3 + 10; // Максимальное кол-во демонов во всех официальных уровнях + 10 в запас, в случае чего
	$maxCoins = 63 + 10; // Максимальное кол-во монеток во всех официальных уровнях + 10 в запас, в случае чего
	$maxUserCoins = 20; // 20 в запас, в случае чего
	$maxDiamonds = 1000000; // Максимльное кол-во алмазов на сервере

	$query = $db->prepare("SELECT sum(starStars) FROM levels"); $query->execute();
	$maxStars += $query->fetchColumn();
	$query = $db->prepare("SELECT levelID FROM dailyfeatures"); $query->execute();
	$dailyLevels = $query->fetchAll();
	if(count($dailyLevels) > 0){
		$levels = '';
		foreach($dailyLevels AS $level){
			$levels .= $level['levelID'].',';
		}
		$levels = substr($levels, 0, -1);
		$query = $db->prepare("SELECT sum(starStars) FROM levels WHERE levelID IN ({$levels})"); $query->execute();
		$maxStars += $query->fetchColumn();
	}
	$query = $db->prepare("SELECT sum(stars) FROM mappacks"); $query->execute();
	$maxStars += $query->fetchColumn();
	$query = $db->prepare("SELECT level1, level2, level3, level4, level5 FROM gauntlets"); $query->execute();
	$gauntletLevels = $query->fetchAll();
	if(count($gauntletLevels) > 0){
		$gLevels = '';
		foreach($gauntletLevels AS $level){
			$gLevels .= $level['level1'].','.$level['level2'].','.$level['level3'].','.$level['level4'].','.$level['level5'].',';
		}
		$gLevels = substr($gLevels, 0, -1);
		$query = $db->prepare("SELECT sum(starStars) FROM levels WHERE levelID IN ({$gLevels})"); $query->execute();
		$maxStars += $query->fetchColumn();
	}
	echo 'MAX stars: '.$maxStars.'<br>';

	$query = $db->prepare("SELECT count(*) FROM levels WHERE starDemon = 1"); $query->execute();
	$maxDemons += $query->fetchColumn();
	if(count($dailyLevels) > 0){
		$query = $db->prepare("SELECT count(*) FROM levels WHERE levelID IN ({$levels}) AND starDemon = 1"); $query->execute();
		$maxDemons += $query->fetchColumn();
	}
	echo 'MAX demons: '.$maxDemons.'<br>';

	$query = $db->prepare("SELECT sum(coins) FROM mappacks"); $query->execute();
	$maxCoins += $query->fetchColumn();
	echo 'MAX coins: '.$maxCoins.'<br>';

	$query = $db->prepare("SELECT sum(coins) FROM levels WHERE starCoins = 1"); $query->execute();
	$maxUserCoins += $query->fetchColumn();
	if(count($dailyLevels) > 0){
		$query = $db->prepare("SELECT sum(coins) FROM levels WHERE levelID IN ({$levels}) AND starCoins = 1"); $query->execute();
		$maxUserCoins += $query->fetchColumn();
	}
	if(count($gauntletLevels) > 0){
		$query = $db->prepare("SELECT sum(coins) FROM levels WHERE levelID IN ({$gLevels}) AND starCoins = 1"); $query->execute();
		$maxUserCoins += $query->fetchColumn();
	}
	echo 'MAX user coins: '.$maxUserCoins.'<br>';

	echo 'MAX diamonds: '.$maxDiamonds.'<br>';

	$query = $db->prepare("SELECT userID, IP FROM users WHERE stars > :stars OR demons > :demons OR coins > :coins OR userCoins > :userCoins OR diamonds > :diamonds");
	$query->execute([':stars' => $maxStars, ':demons' => $maxDemons, ':coins' => $maxCoins, ':userCoins' => $maxUserCoins, ':diamonds' => $maxDiamonds]);
	$users = $query->fetchAll();
	foreach($users AS $user){
		$query = $db->prepare("UPDATE users SET isBanned = 1, isCreatorBanned = 1 WHERE userID = :userID OR IP = :ip");
		$query->execute([':userID' => $user['userID'], ':ip' => $user['IP']]);
		$query = $db->prepare("SELECT count(*) FROM bans WHERE IP = :ip");
		$query->execute([':ip' => $user['IP']]);
		if($query->fetchColumn() == 0){
			$query = $db->prepare("INSERT INTO bans (IP) VALUES (:ip)");
			$query->execute([':ip' => $user['IP']]);
		}
	}

	$count = count($users);
	echo "{$count} users have a ban<br>";
	echo "Autoban finished";
?><hr>