<?php
    chdir(dirname(__FILE__));
    echo "Fix roles...<br>";
    include "../../incl/lib/connection.php";
    require_once "../../incl/lib/mainLib.php";
    $gs = new mainLib();

    $time = time();

    $query = $db->prepare("SELECT count(*) FROM roleassign WHERE endTime < :time");
    $query->execute([':time' => $time]);
    $query = $query->fetchColumn();
    if($query > 0){
        echo 'Fixed roles for '.$query.' accounts<br>';

        $query = $db->prepare("SELECT accountID FROM roleassign WHERE endTime < :time");
        $query->execute([':time' => $time]);
        $accounts = $query->fetchAll();
        foreach($accounts AS $account){
            $userName = $gs->getAccountName($account['accountID']);
            $message = "Закончился срок действия привилегии у игрока {$userName}";

            $array = [
				'peer_id' => 2000000004,
				'message' => $message
			];
			$gs->vk('messages.send', $array);
        }

        $query = $db->prepare("DELETE FROM roleassign WHERE endTime < :time");
        $query->execute([':time' => $time]);
    }

    echo 'Done<br>';
?>