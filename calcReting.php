<?
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

$cfg = Config::get_Instance();
$path = $cfg->mainPath;

chdir($path);
DbManager::Connect();

$i=DbManager::calcRating();

echo 'Пересчитан рейтинг для '.$i.' магазинов.';
?>