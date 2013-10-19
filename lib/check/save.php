<?
session_start();
header('Content-type: text/html; charset=windows-1251');
include_once("../../config/common.php");
require_once ('../DBManager.inc');




if (isset($_REQUEST['act']))
$act = $_REQUEST['act'];

DbManager::Connect();


$User = UserManager::GetCurrentUser();

if (!$User) return;


$debug=false;
if (isset($_REQUEST['debug']))
$debug=true;
                         if (isset($_REQUEST['shop_name']))
                         $shop_name = htmlspecialchars($_REQUEST['shop_name'],ENT_QUOTES);

                   if(!$debug) $shop_name = iconv('utf-8','Windows-1251',$shop_name);


                         if ($shop_name=='') {echo 'Не указан магазин';return;}

                         if (isset($_REQUEST['city_id']) )
                         $city_id= intval($_REQUEST['city_id']);
                         else return;

                         if (isset($_REQUEST['street']) )
                         $street = htmlspecialchars($_REQUEST['street'], ENT_QUOTES);
                         else return;

                         if (isset($_REQUEST['create_dtm']) )
                         {
                         $create_dtm =  date('Y-m-d h:i:s', strtotime(htmlspecialchars($_REQUEST['create_dtm'], ENT_QUOTES)));
                         }
                         else
                         $create_dtm = date('Y-m-d h:i:s');


                         if ($street=='') {echo 'Не указана улица';return;}

                         if(!$debug)  $street = iconv('utf-8','Windows-1251',$street);

                         if (isset($_REQUEST['house_n']))
                         $house_n = htmlspecialchars($_REQUEST['house_n'], ENT_QUOTES);
                         if ($house_n=='') $house_n='';

                         if(!$debug)  $house_n = iconv('utf-8','Windows-1251',$house_n);

                         $street=trim($street);

                         $street_id =DbManager::ProcessStreet($street, $city_id);

                         $shop_name=trim($shop_name);
                         $shop_id=DbManager::ProcessShop($shop_name, $street_id, $house_n);

                         $itmybuy=0;

                         if (isset($_REQUEST['itmybuy']) )
                         $itmybuy = ($_REQUEST['itmybuy']=='true')?1:0;

switch ($act)
        {
                case 'add_check':

                         $head_id=DbManager::AddCheckHead($shop_id, $itmybuy, $create_dtm);
                         if (!isset($_SESSION['in_memory_products'])) {echo 'Не выбраны продукты';return;}
                         if (sizeof($_SESSION['in_memory_products'])==0) {echo 'Не выбраны продукты';return;}

                         $i=0;
                         foreach($_SESSION['in_memory_products'] as $goods)
                         {
                         if ($goods->del==1) continue;
                                  $goods->name=trim($goods->name);
                          $product_id=DbManager::ProcessProduct($goods->name);

                          if ($goods->barcode!='')
                          {
                          $barcode_id =DbManager::ProcessBarCode($goods->barcode);
                          $barcode_link_id =DbManager::ProcessLinkBarCode($product_id, $barcode_id);
                          }

                          if ($goods->cnt==0) $goods->cnt=1;
                          DbManager::AddChkItem($head_id,$product_id,$goods->price, $goods->cnt);
                          $i++;

                         }

                         unset($_SESSION['in_memory_products']);
                          echo $head_id;
                         break;

                case 'edit_check':

                                  if (!isset($_REQUEST['id'])) return;
                                  $id=intval($_REQUEST['id']);


                                  $check=DbManager::getCheckHead($id);
                                  if($check->owner_id!=$User->id) return;
                                  $check->ShopId=$shop_id;
                                  $check->RealBuy = $itmybuy;
                                  $check->create_dtm=$create_dtm;

                                  $head_id = $id;
                         DbManager::UpdateCheckHead($check);

                         $i=0;
                         foreach($_SESSION['in_memory_products'] as $goods)
                         {
                         $goods->name=trim($goods->name);
                         if (($goods->del==1) && ($goods->id!=''))
                         {
                              DbManager::DeleteChkDet($goods->id);
                         }

                         if (!isset($goods->id) && ($goods->del!=1))
                         {
                         if ($goods->cnt==0) $goods->cnt=1;
                          $product_id=DbManager::ProcessProduct($goods->name);
                          DbManager::AddChkItem($head_id,$product_id,$goods->price, $goods->cnt);
                         }

                                 $i++;
                         }

                         unset($_SESSION['in_memory_products']);

                                  echo $head_id;
                         break;
        }

         //  Header('Location: '.$_SERVER['HTTP_REFERER']);


?>
