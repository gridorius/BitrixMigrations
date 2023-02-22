<?php
$options = getopt('b:o:t::');
$bitrixDir = realpath($options['b']);
$saveTo = $options['o'];
$GLOBALS['MIGRATE_TABLES'] = key_exists('t', $options) ? (is_array($options['t']) ? $options['t'] : [$options['t']]) : [];

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


$builder = new \Bitrix\Migration\MigrationBuilder();
$builder->saveToFile($saveTo);