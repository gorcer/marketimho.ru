<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

DbManager::Connect();

$curUser = UserManager::GetCurrentUser();

if (!$curUser) return;

if (!isset($_REQUEST['shemaid'])) return;
$shemaid =$_REQUEST['shemaid'];

if (!isset($_SESSION['memRepProd'][$shemaid])) return;

$prod_list='(';
$i=0;

//var_dump($_SESSION['memRepProd'][$shemaid]);
foreach($_SESSION['memRepProd'][$shemaid] as $product)
{
if ($product->Deleted==1) continue;

$i++;
if ($i!=1)  $prod_list.=', ';

 $prod_list.=$product->id;
}
$prod_list.=')';



if ($i==0) return;

   $BuyList = DbManager::getBuyList_byProdList($prod_list, $curUser->id);
   $txt='[';

         $i=0;
   foreach($BuyList as $buy)
    {
    $i++;
    $buy->dtm = strtotime($buy->dtm) * 1000;
    if ($i!=1)  $txt.=', ';

    $txt.='['.$buy->dtm.','.$buy->price.']';
    }

      $txt.=']';

      echo $txt;

?>
