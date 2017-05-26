<?php
	ini_set('display_errors', 0);
	//exit('1223');
	$serverName = "127.0.0.1";
	$uid = "bc_beta1";
	$pwd = "xy21919799@2016!";
	$db  = "ttkvod";
	 
	$db = new PDO ("mssql:host=localhost,1433;dbname=ttkvod","bc_beta1","xy21919799@2016!");
	print_r($db);

?>