<?php
$bitrixDir = realpath($argv[1]);
$migrationPath = realpath($argv[2]);
if(!$bitrixDir){
    throw new Exception("Bitrix not found in {$argv[1]}");
}

if(!$migrationPath){
    throw new Exception("Migration {$argv[2]} not found");
}

$_SERVER['DOCUMENT_ROOT'] = $bitrixDir;

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);
require_once $bitrixDir."/bitrix/modules/main/include/prolog_before.php";
require __DIR__.'/EntityManager.php';

$connection = \Bitrix\Main\Application::getConnection();
try {
    $connection->startTransaction();
    require $migrationPath;
    $connection->commitTransaction();
}catch (Exception $ex){
    $connection->rollbackTransaction();
}