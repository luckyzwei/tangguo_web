<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
ini_set("date.timezone","PRC");
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT . '../application/ttkvod/');

set_include_path(ROOT . '../library/'
	.PATH_SEPARATOR . APP_PATH
	.PATH_SEPARATOR . get_include_path());
require_once 'Lamb/Loader.php';
$loader = Lamb_Loader::getInstance();
$loader->registerNamespaces('Ttkvod');
//registry
$aCfg = require('config.inc.php');
Lamb_Registry::set(CONFIG, $aCfg);
ob_start();				
Lamb_App::getInstance()->setControllorPath($aCfg['controllor_path'])
					   ->setViewRuntimePath($aCfg['view_runtime_path'])
					   ->setErrorHandler(new Ttkvod_ErrorHandler)
					   //->setRouter(new Lamb_App_NormalRouter)
					   ->setDbCallback('Ttkvod_Db_Factory::singleInstance')
					   ->setSqlHelper(new Lamb_Mssql_Sql_Helper)
					   ->run();
?>