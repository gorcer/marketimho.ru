<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

DbManager::Connect();

$curUser = UserManager::GetCurrentUser();

if (!$curUser) return;


if (!isset($_REQUEST['act'])) return;
if (!isset($_REQUEST['typeid'])) return;

$act =$_REQUEST['act'];
$shemaid =$_REQUEST['shemaid'];

if (!isset($_SESSION['memRepProd']))
$_SESSION['memRepProd'] = array();


if (!isset($_SESSION['memRepProd'][$shemaid]))
$_SESSION['memRepProd'][$shemaid] = array();


switch ($act)
{
 case 'add':
                if (isset($_REQUEST['prname']))
                {
                $prname = iconv('utf-8','Windows-1251',$_REQUEST['prname']);
                $prname = htmlspecialchars($prname, ENT_QUOTES);
                if ($prname=='') break;

                $Product = DbManager::getProductByName($prname);
                $Product->Deleted = 0;
                if ($Product!='')
                $_SESSION['memRepProd'][$shemaid][]=$Product;
                }

                break;
 case 'del':
                if (isset($_REQUEST['id']))
                {
                $id = intval($_REQUEST['id']);
                $_SESSION['memRepProd'][$shemaid][$id]->Deleted = 1;
               // unset($_SESSION['memRepProd'][$typeid][$id]);
                }
                break;
 }


?>

<table cellpadding=0 cellspacing=3 border=0 class='txt' width='50%' style='padding-top:10px;padding-bottom:10px;'>
<?
$i=0;
//unset($_SESSION['memRepProd']);
//var_dump($_SESSION['memRepProd']);
foreach($_SESSION['memRepProd'][$shemaid] as $key=>$product)
{
if ($product->Deleted==1) continue;

$i++;
?>
<tr>
 <td  style='background-color:#FFFBF0;' width='25px' height='25px' align='center' valign='center'><?=$i ?></td>
 <td><?=$product->name ?></td>
 <td><a href='javascript:DelProd(<?=$key ?>)'>[удалить]</a></td>
</tr>
<?

}


?>
</table>
<?





?>