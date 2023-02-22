<?php

$options = getopt('b:m:s::c::');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bitrixDir = realpath($options['b']);
$migrationPath = $options['m'];
if(!$bitrixDir){
    throw new Exception("Bitrix not found in {$argv[1]}");
}

if(!$migrationPath){
    throw new Exception("Migration {$argv[2]} not found");
}


$GLOBALS['SKIP_MIGRATION'] = key_exists('s', $options) ? (is_array($options['s']) ? $options['s'] : [$options['s']]) : [];
$GLOBALS['CONCRETE_MIGRATIONS'] = key_exists('c', $options) ? (is_array($options['c']) ? $options['c'] : [$options['c']]) : null;
$_SERVER['DOCUMENT_ROOT'] = $bitrixDir;

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);
require_once $bitrixDir."/bitrix/modules/main/include/prolog_before.php";
require __DIR__.'/EntityManager.php';
require __DIR__.'/MigrationLogger.php';

$connection = \Bitrix\Main\Application::getConnection();
try {
    $connection->startTransaction();
    require $migrationPath;
    $connection->commitTransaction();
}catch (Exception $ex){
    $connection->rollbackTransaction();
    throw $ex;
}