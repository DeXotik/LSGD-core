<?php
    echo "Start deleting not registered accounts...<br>";
    include '../../incl/lib/connection.php';

    $time = time()-86400;
    $query = $db->prepare("SELECT count(*) FROM accounts WHERE registerDate < :time AND registered = 0");
    $query->execute([':time' => $time]);
    $count = $query->fetchColumn();
    $query = $db->prepare("DELETE FROM accounts WHERE registerDate < :time AND registered = 0");
    $query->execute([':time' => $time]);
    echo "{$count} accounts deleted<hr>";
?>