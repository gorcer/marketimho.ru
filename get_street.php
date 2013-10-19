<?
header('Content-type: text/html; charset=cp1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

if (!isset($_REQUEST['shop_id'])) return;
//$_REQUEST['shop_id'] = iconv('utf-8','Windows-1251',$_REQUEST['shop_name']);
//$shop_name = htmlspecialchars($_REQUEST['shop_name'], ENT_QUOTES);
$shop_id =intval($_REQUEST['shop_id']);

DbManager::Connect();
$Shop = DbManager::getShopById($shop_id);

echo $Shop->StreetName.'###'.$Shop->house_n;
?>