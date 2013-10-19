<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');
require_once ('lib/NotifyMgr.inc');



if (!isset($_REQUEST['act'])) return;
 $curUser = UserManager::GetCurrentUser();
// if (!$curUser) return;

$act=$_REQUEST['act'];

DbManager::Connect();

switch ($act)
{
       case 'add_message':
                          if (!isset($_REQUEST['user_id'])) {echo 'err1';return;}
                          if (!isset($_REQUEST['message'])) {echo 'err2';return;}
                          if (!isset($_REQUEST['parent_id'])) {echo 'err3';return;}

                          $user_id=intval($_REQUEST['user_id']);
                          $message = htmlspecialchars($_REQUEST['message'], ENT_QUOTES);
                          $parent_id=intval($_REQUEST['parent_id']);

                          $message = iconv('utf-8','Windows-1251',$message);

                          $id=DbManager::addMessage($user_id, $message, $curUser->id, $parent_id);

                          if ($user_id!=$curUser->id)
                          NotifyMgr::notifyMail($user_id, ($parent_id==0?$id:$parent_id));

                          echo 'ok';
                          break;
                          
        case 'print_buyplan_det':
                                 if (!$curUser) return;
                                 if (is_array($_REQUEST['buyplan_det']))
                                 $list=$_REQUEST['buyplan_det'];

                                 if (sizeof($list)==0){  Header('Location:'.$_SERVER['HTTP_REFERER']);  return;}

                                 $txt=json_encode($list);
                                 echo "
                                 <script>
                                  window.open('print_buy_plan/".$curUser->id."/".$txt."','_blank');
                                  document.location.href='".$_SERVER['HTTP_REFERER']."';
                                 </script>

                                 ";

                                 break;
        case 'del_buyplan_det':
                               if (!$curUser) return;
                                                      if (is_array($_REQUEST['buyplan_det']))
                                                      $list=$_REQUEST['buyplan_det'];
                                if (sizeof($list)==0) {  Header('Location:'.$_SERVER['HTTP_REFERER']);  return;}
                                                      foreach($list as $item)
                                                      {
                                                      $buyplan = DbManager::getBuyPlanByID(intval($item));

                                            if ($buyplan->user_id!=$curUser->id) break;

                                            DbManager::DelBuyPlan(intval($item));
                                                      }

                                                      Header('Location:'.$_SERVER['HTTP_REFERER']);

                                                        break;

                case 'add_product_to_planbuy':
                                                                if (!$curUser) return;
                                                            if (!isset($_REQUEST['product_name'])) return;

                                                                if (!isset($_REQUEST['cnt'])) return;
                                                                $_REQUEST['cnt'] = str_replace(',','.',$_REQUEST['cnt']);

                                                            $cnt=floatval($_REQUEST['cnt']);



                                                       $name=htmlspecialchars($_REQUEST['product_name'], ENT_QUOTES);
                                      $name = iconv('utf-8','Windows-1251',$name);

                                      if ($name=='') return;


                                                           DbManager::AddProductToBuyPlan($curUser->id,$name, $cnt);

                                                                break;
                case 'set_planbuy_shop':
                                                                if (!$curUser) return;
                                                            if (!isset($_REQUEST['shop_id'])) return;
                                                            $id=intval($_REQUEST['shop_id']);
                                                 DbManager::setUserParams($curUser->id, 'inplan_shop', $id);
                                                 echo 'ok';
                                                                break;
                case 'change_city':
                                                        if (!isset($_REQUEST['city_id'])) return;
                                                        $_SESSION['def_city_id']=intval($_REQUEST['city_id']);
                                                        echo 'ok';
                                                        break;
        case 'del_check':
                                             if (!$curUser) return;
                         if (!isset($_REQUEST['id'])) return;

                         $id=intval($_REQUEST['id']);

                         DbManager::DelCheck($id);

                         echo 'ok';

                         break;
        case 'restore_check':
                                        if (!$curUser) return;
                         if (!isset($_REQUEST['id'])) return;

                         $id=intval($_REQUEST['id']);

                         DbManager::RestoreCheck($id);

                         echo 'ok';

                         break;

        case 'add_friend':
                                        if (!$curUser) return;
                          if (!isset($_REQUEST['login'])) return;

                          $login = htmlspecialchars($_REQUEST['login'], ENT_QUOTES);
                          if (preg_match('/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i', $_REQUEST['login']))
                          {
                              $email = $login;
                              $User =  DbManager::GetUserByEmail($email);
                              if ($User)
                              {
                                      $login=$User->login;

                              }
                              else
                              {
                               $flag=DbManager::AddInvite($email);

                               echo($flag);
                               return;
                              }
                          }

                          $login = htmlspecialchars($login, ENT_QUOTES);

                          $flag=DbManager::AddFriend($login);

                          echo $flag;

                          break;
        case 'del_friend':
                                        if (!$curUser) return;
                          if (!isset($_REQUEST['id'])) return;
                          $id = intval($_REQUEST['id']);
                          $flag=DbManager::DelFriendship($id);

                          echo $flag;

                          break;
        case 'decline_friend':
                                        if (!$curUser) return;
                          if (!isset($_REQUEST['id'])) return;
                          $id = intval($_REQUEST['id']);
                          $flag=DbManager::DeclineFriendship($id);

                          echo $flag;

                          break;

        case 'approve_friend':
                                        if (!$curUser) return;
                          if (!isset($_REQUEST['id'])) return;
                          $id = intval($_REQUEST['id']);
                          $flag=DbManager::ApproveFriendship($id);

                          echo $flag;

                          break;
        case 'isProductExist':
                                        if (!$curUser) return;
                              if (!isset($_REQUEST['name'])) return;
                              $name=htmlspecialchars($_REQUEST['name'], ENT_QUOTES);

                              if ($name=='') {echo('false');return;}


                              $name = iconv('utf-8','Windows-1251',$name);

                              $Product = DbManager::getProductByName($name);

                              if (!$Product) echo('no');
                              else echo('yes');

                              break;
        case 'UpdateProduct':
                                        if (!$curUser) return;
                             if (!isset($_REQUEST['id'])) return;
                             if (!isset($_REQUEST['name'])) return;
                             if (!isset($_REQUEST['catname'])) return;

                             $id = intval($_REQUEST['id']);
                             $name=htmlspecialchars($_REQUEST['name'], ENT_QUOTES);
                             $catname=htmlspecialchars($_REQUEST['catname'], ENT_QUOTES);
                             if ($name=='') return('false');

                              $name = iconv('utf-8','Windows-1251',$name);
                              $catname = iconv('utf-8','Windows-1251',$catname);
                              $catid='null';
                              if ($catname!=-1)
                              {

                              $tags = split(', ', $catname);

                              foreach($tags as $tag)
                              DbManager::processProductTag($tag);


                              }

                              $ProductOld = DbManager::getProductByID($id);
                              $ProductNew = DbManager::getProductByName($name);
                              $isMerge=false;
                              if (($ProductOld->name!=$name) && ($ProductNew!=false) && ($ProductNew->id!=$id))
                              {

                                                             DbManager::MergeProducts($id, $ProductNew->id);
                                                             $id = $ProductNew->id;
                                                             $name = $ProductNew->name;
                                                             $isMerge=true;
                              }


                              if (!$isMerge)
                              {
                              $ProductOld->name = $name;
                              $ProductOld->tags=$catname;
                              DbManager::SaveProduct($ProductOld);
                              }


                              $name=html_entity_decode($name, ENT_QUOTES);
                              $name = str_replace('\"','"',$name);

                              echo $id.'###'.$name;

                             break;
        case 'SaveReport':
                                        if (!$curUser) return;
                              if (!isset($_REQUEST['typeid'])) return;
                              $typeid=intval($_REQUEST['typeid']);

                              if (!isset($_REQUEST['shemaid'])) return;
                              $shemaid=intval($_REQUEST['shemaid']);

                             $id=-1;
                             if (isset($_REQUEST['id']))
                             $id = intval($_REQUEST['id']);
                             $name='';


                             if (!isset($_SESSION['memRepProd'][$shemaid])) return;

                             if (isset($_REQUEST['name']))
                             {
                             $name=htmlspecialchars($_REQUEST['name'], ENT_QUOTES);
                             if ($name=='') return('false');
                             $name = iconv('utf-8','Windows-1251',$name);
                             }

                             if (($id==-1) && ($name!=''))
                             {
                             $id=DbManager::AddReport($name, $typeid);
                             }

//                               unset($_SESSION['memRepProd'][$shemaid]);

                             foreach($_SESSION['memRepProd'][$shemaid] as $product)
                             {


                             if (($product->Deleted==1) && ($product->detid!=''))
                             {
                                  DbManager::DelReportDet($product->detid);
                             }

                             if (!isset($product->detid))
                             {

                                  DbManager::AddReportDet($id,$product->id);
                             }
                             }

                             unset($_SESSION['memRepProd'][$shemaid]);
                             echo $id;


                             break;
       case 'DelReport':
                                        if (!$curUser) return;
                                                if (!isset($_REQUEST['shemaid'])) return;
                              $shemaid=intval($_REQUEST['shemaid']);

                              if ($shemaid!=-1)
                              DbManager::DelReport($shemaid);

                              unset($_SESSION['memRepProd'][$shemaid]);

                         break;

       case 'AddComment':
                                        if (!$curUser) return;
                         $shop_id=-1;
                         $product_id=-1;

                         if (!isset($_REQUEST['comment'])) return;
                         $comment = htmlspecialchars($_REQUEST['comment'], ENT_QUOTES);
                         $comment = iconv('utf-8','Windows-1251',$comment);
                         if (isset($_REQUEST['shop_id']))
                         {
                         $shop_id=intval($_REQUEST['shop_id']);
                         }

                         if (isset($_REQUEST['product_id']))
                         {
                         $product_id=intval($_REQUEST['product_id']);
                         }
                         if ($product_id==-1 && $shop_id==-1) return;

                        $city_id=UserManager::getUserCity();

                        DbManager::AddComment($product_id, $shop_id, $city_id, $comment);


                         break;

       case 'DelComment':
                                        if (!$curUser) return;
                          if (!isset($_REQUEST['id'])) return;
                          $id=intval($_REQUEST['id']);
                          DbManager::DelComment($id);

                         break;

}


?>
