<?php
$bitrixDir = realpath($argv[1]);
if(!$bitrixDir){
    throw new Exception("Bitrix not found in {$argv[1]}");
}

$_SERVER['DOCUMENT_ROOT'] = $bitrixDir;

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);
require_once $bitrixDir."/bitrix/modules/main/include/prolog_before.php";
require __DIR__.'/MigrationBuilder.php';

$saveTo = $argv[2];

$builder = new \Bitrix\Migration\MigrationBuilder();
$builder->saveToFile($saveTo);