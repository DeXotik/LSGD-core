<?php

chdir(dirname(__FILE__));
echo "Please wait...<br>";

include '../../incl/lib/connection.php';

$query = $db->prepare("SELECT userID FROM levels GROUP BY userID");
$query->execute();
$users = $query->fetchAll();
foreach($users as $user){
	$query = $db->prepare("SELECT levelID, starFeatured, starEpic FROM levels WHERE userID = :userID AND starStars > 0");
	$query->execute([':userID' => $user['userID']]);
	$levels = $query->fetchAll();
	if(count($levels) > 0){
		$creatorPoints = 0;
		foreach($levels as $level){
			$creatorPoints++;
			if($level['starFeatured'] > 0){
				$creatorPoints++;
			}
			if($level['starEpic'] > 0){
				$creatorPoints++;
			}
		}
		$query = $db->prepare("UPDATE users SET creatorPoints = :creatorPoints WHERE userID = :userID");
		$query->execute([':creatorPoints' => $creatorPoints, ':userID' => $user['userID']]);
		echo "Creator points ({$creatorPoints}) fixed for userID: {$user['userID']}<br>";
	}
}

echo "<hr>";

?>
