<?php
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/generateHash.php";
$generateHash = new generateHash();
$gauntletstring = "";
$string = "";
$query = $db->prepare("SELECT * FROM gauntlets");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$gauntlet){
	$lvls = $gauntlet["level1"].",".$gauntlet["level2"].",".$gauntlet["level3"].",".$gauntlet["level4"].",".$gauntlet["level5"];
	$gauntletstring .= "1:".$gauntlet["ID"].":3:".$lvls."|";
	$string .= $gauntlet["ID"].$lvls;
}
$gauntletstring = substr($gauntletstring, 0, -1);
echo $gauntletstring;
echo "#".$generateHash->genSolo2($string);
?>