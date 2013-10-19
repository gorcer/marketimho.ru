<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

if (!isset($_REQUEST['page'])) return;

DbManager::Connect();


$page=$_REQUEST['page'];

$letters = '';

if (isset($_REQUEST['q']))
$letters = $_REQUEST['q'];
$letters = iconv('utf-8','Windows-1251',$letters);
$letters = htmlspecialchars($letters, ENT_QUOTES);
switch ($page)
        {
        case 'SelectProduct_byShop':

												$shop_id=-1;
                                               if (isset($_REQUEST['shop_id']))
                                               $shop_id=intval($_REQUEST['shop_id']);

												if ($shop_id!=-1)
        									   $Products=DbManager::getProductList_byShop($letters, $shop_id);
        									   else
        									   $Products=DbManager::getProductList($letters, $shop_id);

                                                  if ($Products==false) return;
                                                foreach($Products as $Product)
                                                {
                                                $Product->name=html_entity_decode($Product->name, ENT_QUOTES);
//                                                $Product->name=iconv('utf-8','Windows-1251',$Product->name);
                                                echo $Product->name.'|'.$Product->id.'
                                                ';
                                                }
                                                break;

        case 'SelectShop':
                                                $city_id=UserManager::getUserCity();
                                                $Shops=DbManager::getShopList($letters, $city_id);
                                                foreach($Shops as $shop)
                                                {
                                                 $shop->name=html_entity_decode($shop->name, ENT_QUOTES);
                                                echo $shop->name.'|'.$shop->id.'|'.$shop->name.' (ул.'.$shop->StreetName.' '.$shop->house_n.')
                                                ';
                                                }
                                                break;

        case 'SelectProductByCode':             $Products=DbManager::getProductListByBarCode($letters);

                                                if ($Products==false) return;
                                                foreach($Products as $Product)
                                                {
                                                $Product->name=html_entity_decode($Product->name, ENT_QUOTES);
//                                                $Product->name=iconv('utf-8','Windows-1251',$Product->name);
                                                echo $Product->code.'|'.$Product->name.'|'.$Product->id.'
                                                ';
                                                }

                                                break;

        case 'SelectProduct':
                                                $Products=DbManager::getProductList($letters);
                                                  if ($Products==false) return;
                                                foreach($Products as $Product)
                                                {
                                                $Product->name=html_entity_decode($Product->name, ENT_QUOTES);
//                                                $Product->name=iconv('utf-8','Windows-1251',$Product->name);
                                                echo $Product->name.'|'.$Product->id.'
                                                ';
                                                }
                                                break;

        case 'SelectProductCat':
                                                $ProductsCat=DbManager::getProductCatList($letters);
                                                  if ($ProductsCat==false) return;
                                                foreach($ProductsCat as $ProductCat)
                                                {
                                                $ProductCat->name=html_entity_decode($ProductCat->name, ENT_QUOTES);
//                                                $Product->name=iconv('utf-8','Windows-1251',$Product->name);
                                                echo $ProductCat->name.'|'.$ProductCat->id.'
                                                ';
                                                }
                                                break;

       case 'SelectStreet':
                                               $city_id = intval($_REQUEST['city_id']);


                                               if (($city_id=='') && ($city=='')) return;
                                               $StreetList = DbManager::getStreetByMask($letters, $city_id);
                                               //echo '0###';print_r($_REQUEST);echo '|';
                                               //    echo '0###'.$_SERVER['REQUEST_URI'].'|';
                                                if ($StreetList==false) return;
                                               foreach($StreetList as $street)
                                               {
                                                     echo $street->name.'|'. $street->id.'
                                                     ';
                                               }
                                                break;
  case 'SelectCity':

                                               $CityList = DbManager::getCityList($letters);
                                               //echo '0###';print_r($_REQUEST);echo '|';
                                               //    echo '0###'.$_SERVER['REQUEST_URI'].'|';
                                               if ($CityList==false) return;
                                               foreach($CityList as $city)
                                               {
                                                     echo $city->name.'|'. $city->id.'|'.$city->RegionName.'|
                                                     ';
                                               }
                                                break;

  case 'SelectUser':

                                               $UserList = DbManager::getUserList($letters);
                                               //echo '0###';print_r($_REQUEST);echo '|';
                                               //    echo '0###'.$_SERVER['REQUEST_URI'].'|';
                                               if ($UserList==false) return;
                                               foreach($UserList as $user)
                                               {
                                                     echo $user->login.'|'. $user->id.'|'.$user->name.'|
                                                     ';
                                               }
                                                break;
        }



?>
