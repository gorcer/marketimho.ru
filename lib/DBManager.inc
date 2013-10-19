<?

$cfg = Config::get_Instance();
$path = $cfg->mainPath;

//include_once($path."lib/dbaccessor.php");
//include_once($path."users/user.php");
include_once ($path."lib/f.inc");
include_once($path."lib/FileMgr.inc");
include_once($path."lib/LogMgr.inc");
include_once($path."lib/HTMLMgr.inc");
include_once($path."lib/uMemoryMgr.inc");
include_once($path."users/usermanager.php");
include_once($path."lib/MailMgr.inc");

class DbManager
{

                function DbManager()
                {
                        $this->Connect();
                }

        function Connect()
        {
           static $connect = null;

            $config = Config::get_Instance();
                        $dbInfo = $config->get_BdInfo();

                        $connect =
                                mysql_connect($dbInfo->get_HostName(), $dbInfo->get_UserName(), $dbInfo->get_Password())
                                        or die("По техническим причинам сайт временно не работает.");
                                mysql_query("SET NAMES 'cp1251'");
                                mysql_select_db($dbInfo->get_Name())
                                or die("Could not select database '".$dbInfo->get_Name()."'");
        }

        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new DbManager();
                return $instance;
        }

                function getShopByName_AndAdress($name, $street_id, $house_n='null')
                {
                        $row=sqr_obj('
                        select *
                        from chk_shop
                        where
                             name like "'.$name.'"
                             and street_id='.$street_id.'
                             and house_n="'.$house_n.'"
                        ');
                        return($row);
                }

                function CreateShop($name, $street_id, $house_n='null')
                {
                        eqr('insert into chk_shop(name, street_id, house_n) values("'.$name.'", '.$street_id.', "'.$house_n.'")');
                        $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'chk_shop');
                        return($id);
                }




                function getProductTagByName($name)
                {
                        $row=sqr_obj("
                        select * from chk_product_tag
                        where name like '".$name."'
                        ");

                        return($row);
                }

                function CreateProductTag($name)
                {
                        eqr('insert into chk_product_tag(name) values ("'.$name.'")');
                        $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'chk_product_tag');
                        return($id);
                }


        function processProductTag($tag)
        {
          $Tag =  DbManager::getProductTagByName($tag);

                if ($Tag==false)
                $id=DbManager::CreateProductTag($tag);
                else
                $id=$Tag->id;

                return($id);
        }

        function processProductCat($name)
        {
                $Cat =  DbManager::getProductCatByName($name);

                if ($Cat==false)
                $id=DbManager::CreateProductCat($name);
                else
                $id=$Cat->id;

                return($id);
        }

        function ProcessShop($name, $street_id, $house_n)
        {
         $Shop = DbManager::getShopByName_AndAdress($name, $street_id, $house_n);

         if ($Shop===false)
         {
                 $id=DbManager::CreateShop($name, $street_id, $house_n);
         }
         else $id=$Shop->id;



         return($id);
        }

        function AddCheckHead($shop_id, $itmybuy, $create_dtm)
        {
        if (!($user=UserManager::GetCurrentUser())) return;
        eqr('insert into chk_check_head (RealBuy,create_dtm, shop_id, owner_id) values('.$itmybuy.',"'.$create_dtm.'",'.$shop_id.','.$user->id.')');

                        $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'chk_check_head');
                        return($id);
        }

                function getProductByName($name)
                {
                        $row=sqr_obj('select * from chk_products where name = "'.$name.'"');
                        return($row);
                }

                function searchTags($name)
                {
                         $tags = split(' ', $name);
                         $txt='';
                         foreach($tags as $tag)
                         {
                           $item = DbManager::getProductTagByName($tag);
                           if ($item!=false)
                           {
                             if ($txt!='') $txt.=', ';
                             $txt.=$tag;
                           }
                         }

                         return($txt);

                }

                function CreateProduct($name)
                {
                        $tags = DbManager::searchTags($name);
                        eqr('insert into chk_products(name, tags) values("'.$name.'", "'.$tags.'")');
                        $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'chk_products');
                        return($id);
                }


           function getBarCodeByCode($code)
                {
                        $row=sqr_obj('select * from chk_barcode where code = "'.$code.'"');
                        return($row);
                }

              function CreateBarCode($code)
                {
                        eqr('insert into chk_barcode(code) values("'.$code.'")');
                        $id=mysql_insert_id();
                        return($id);
                }

        function ProcessBarCode($code)
        {
         $BarCode = DbManager::getBarCodeByCode($code);

         if ($BarCode==false) $id=DbManager::CreateBarCode($code);
         else $id=$BarCode->id;

         return($id);
        }

        function getLinkBarCode($product_id, $barcode_id)
        {
           $row=sqr_obj('select * from chk_product_barcode where product_id = "'.$product_id.'" and barcode_id="'.$barcode_id.'"');
           return($row);
        }

              function createLinkBarCode($product_id, $barcode_id)
                {
                        eqr('insert into chk_product_barcode(product_id, barcode_id) values('.$product_id.','.$barcode_id.')');
                        $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'chk_barcode_link','(p:'.$product_id.',b:'.$barcode_id.')');
                        return($id);
                }

        function ProcessLinkBarCode($product_id, $barcode_id)
        {
            $link=DbManager::getLinkBarCode($product_id, $barcode_id);

            if ($link==false)
            $link_id=DbManager::createLinkBarCode($product_id, $barcode_id);
            else
            $link_id=$link->id;

            return($link_id);
        }

        function ProcessProduct($name)
        {
             $Product = DbManager::getProductByName($name);

         if ($Product==false) $id=DbManager::CreateProduct($name);
         else $id=$Product->id;

         return($id);
        }

        function AddChkItem($head_id,$product_id,$goods_price, $cnt)
        {

        $goods_price = str_replace(',','.',$goods_price);
           eqr('insert into chk_check_det(OnePrice, cnt,product_id, price, head_id) values('.($goods_price/$cnt).','.$cnt.','.$product_id.',"'.$goods_price.'",'.$head_id.')');
           $id=mysql_insert_id();
           return($id);
        }

        function getShopByName($name)
        {
         $row=sqr_obj("
         select s.id, s.name, st.name as StreetName, s.house_n
         from
         chk_shop s
         inner join aimw_street st on st.id=s.street_id
         where s.name like '".$name."'
         ");

         return($row);
        }

        function getShopByID($id)
        {
        $row=sqr_obj("
         select s.id, s.name, st.name as StreetName, s.house_n, c.name as city_name
         from
         chk_shop s
         inner join aimw_street st on st.id=s.street_id
         inner join aimw_city c on c.id=st.city_id
         where s.id = '".$id."'
         ");

         return($row);
        }

        function getShopList($txt='', $city_id=-1)
        {
            if ($city_id==-1)
            $city_id=UserManager::getUserCity();

         $qt="
         select distinct s.id, s.name, st.name as StreetName, s.house_n
         from chk_shop s
         inner join aimw_street st on st.id=s.street_id
         inner join chk_check_head h on h.shop_id=s.id and h.DelMark=0
         where ";

         if ($txt!='') $qt.="s.name like '%".$txt."%' and ";

         $qt.="
          st.city_id=".$city_id."
          order by s.name
         ";

         $q=sqr_list($qt);

                $arr=array();
                $i=0;
         while(($row=mysql_fetch_object($q))!=false)
         {
         $i++;
          $arr[]=$row;
         }



         return($arr);
        }

        function getProductListByBarCode($txt)
        {
        $list=sqr_listObj("
         select p.*,b.code
         from
         chk_products p
         inner join chk_product_barcode pb on pb.product_id=p.id
         inner join chk_barcode b on b.id=pb.barcode_id
         where b.code like '%".$txt."%'
         order by name
         ");

         return($list);
        }

        function getProductList($txt)
        {
         $q=sqr_list("
         select * from chk_products
         where name like '%".$txt."%'
         order by name
         ");

                $arr=array();
         while(($row=mysql_fetch_object($q))!=false)
         {
          $arr[]=$row;
         }

         return($arr);
        }


          function getProductList_byShop($txt, $shop_id)
        {
         $list=sqr_listObj("
             select distinct p.*
             from chk_check_head h
             inner join chk_check_det d on d.head_id=h.id
             inner join chk_products p on p.id=d.product_id
             where
             h.DelMark=0 and
             h.shop_id=".$shop_id." and
             p.name like '%".$txt."%'
             group by p.name, p.id
         ");

         return($list);
        }

        function UpdateCheckHead($check)
        {
                eqr("
                update chk_check_head
                set
                   create_dtm='".$check->create_dtm."',
                   shop_id='".$check->ShopId."',
                   RealBuy = '".$check->RealBuy."'
                where
                id=".$check->id."
                ");

                TLogMgr::add($check->id, 'upd', 'chk_check_head');
        }

        function getCheckHead($id)
        {
          $Check = sqr_obj('
          select h.id, h.create_dtm, sh.Name as ShopName, sh.id as ShopId, s.id as StreetID, s.name as StreetName, sh.house_n,
          h.owner_id

          from chk_check_head h
          inner join chk_shop sh on sh.id=h.shop_id
          left outer join aimw_street s on s.id=sh.street_id
          where h.id='.$id
          );
          return($Check);
        }

        function DeleteChkDet($id)
        {
          TLogMgr::add($id, 'del', 'chk_check_det');

                eqr("
                delete from chk_check_det where id=".$id."
                ");

        }
      /*
        function RecalcProductStat($product_id, $city_id)
        {

         $list = sqr_obj("
              select min(d.OnePrice) as minprice, max(d.OnePrice) as maxprice
              from
              chk_check_det d
              inner join (
              select max(h.create_dtm) as dtm, d.product_id
              from chk_check_head h
              inner join chk_check_det d on d.head_id=h.id

              inner join chk_shop s on s.id=h.shop_id
              inner join aimw_street st on st.id=s.street_id

              where
              d.product_id=".$product_id." and
              st.city_id=".$city_id."
              group by d.product_id) as tt
                  on tt.dtm=h.create_dtm and tt.product_id=d.product_id
         ");

         foreach ($list as $item)
         {
                 $max_shop = sqr_obj('
                                 select
                                 h.shop_id
                                 from
                                 chk_check_head h
                                 inner join chk_check_det d on d.head_id=h.id
                                 where
                                 d.product_id='.$product_id.'
                                 and d.OnePrice='.$item->maxprice.'
                                  ');

                 $min_shop = sqr_obj('
                                 select
                                 h.shop_id
                                 from
                                 chk_check_head h
                                 inner join chk_check_det d on d.head_id=h.id
                                 where
                                 d.product_id='.$product_id.'
                                 and d.OnePrice='.$item->minprice.'
                                  ');


         }


        }       */

        function getCheckDetails($check_id)
        {
           $list = sqr_listObj('
          select d.id, d.cnt, d.price, p.name as name, d.OnePrice, p.id as ProductID
          from chk_check_det d
          inner join chk_products p on p.id=d.product_id
          where d.head_id='.$check_id.'
          ');

          return($list);
        }

        function getCheckInfo($id)
        {
        $User = UserManager::GetCurrentUser();

          $Check = sqr_obj('
          select h.RealBuy, h.id, h.create_dtm, sh.Name as ShopName, sh.id as ShopId, s.id as StreetID, s.name as StreetName, sh.house_n
          from chk_check_head h
          inner join chk_shop sh on sh.id=h.shop_id
          inner join aimw_street s on s.id=sh.street_id
          where h.id='.$id
          );

          $Check->list =  array();

          $q = sqr_list('
          select d.id, d.cnt, d.price, p.name as name, d.OnePrice, p.id as ProductID
          from chk_check_det d
          inner join chk_products p on p.id=d.product_id
          where d.head_id='.$id.'
          ');

          $CP = DbManager::getShopCPlist($Check->ShopId, false);

          while (($row=mysql_fetch_object($q))!=false)
          {
          if ($CP!=false)
          $row->MinPriceCP = DbManager::getMinPrice_byCP($CP,$row->OnePrice);

               $row->del=0;
              $analiz = sqr_obj('
                                 select min(d.OnePrice) as min_price, max(d.OnePrice) as max_price
                                 from chk_check_det d
                                 inner join chk_check_head h on h.id=d.head_id
                                 inner join chk_v_actual_det vd on vd.detid=d.id and vd.user_id='.$User->id.'
                                 where
                                 d.product_id='.$row->ProductID.'
                                 and h.DelMark=0
                                 and d.OnePrice>0
              ');
              $row->analiz=$analiz;

              $Check->list[]=$row;
              }

          return $Check;

        }

        function getMinPrice_byCP($CPList, $Price)
        {
        $MainCP=false;


         foreach($CPList as $cp)
         {
          if ($cp->cp=='') continue;

//                 $min_price = $Price/(1+$cp->cp);
                 $min_price = $Price;
                                    /*
                 Mark($cp->min_price);
                 Mark($cp->max_price);
                 Mark($cp->cp);
                 Mark('min_price: '.$min_price);
                                                     */
                 if ($cp->min_price<$min_price && $cp->max_price>$min_price)
                 {

                 // Mark('min_price: '.$min_price);

                         $MainCP=$cp->cp;/*
                  Mark($MainCP);
                  Mark('-');   */
                         break;
                 }


         }

         return($MainCP);

        }


        function getStreetByMask($mask, $city_id)
        {
        $arr=array();

        $qt='
        select s.id, s.name
        from aimw_street s
        where
        s.city_id='.$city_id.'
        and
        s.name like "%'.$mask.'%"';

        $q=sqr_list($qt);

        while($row=mysql_fetch_object($q))
         {

                 $arr[]=$row;
         }
          return($arr);

        }

        function addStreet($street_name, $city_id)
        {
                eqr('insert into aimw_street(name, city_id) values("'.$street_name.'", '.$city_id.')');
                $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'street');
                return($id);
        }

          function processStreet($street_name, $city_id)
        {

           $street = sqr_obj('select id from aimw_street where city_id="'.$city_id.'" and name like "'.$street_name.'"');

           if ($street==false)
           {
           $id=DbManager::addStreet($street_name, $city_id);
           }
           else
           $id=$street->id;

           return($id);
          }


          function getCheckList_byUser($user_id)
          {
                  $q=sqr_list("
                  select ch.id, DATE_FORMAT(ch.create_dtm, '%d.%m.%Y') as create_dtm, count(d.id) as det_cnt, sum(d.price) as sum, s.name as ShopName, s.id as ShopId
                  from chk_check_head ch
                  inner join chk_check_det d on d.head_id=ch.id
                  inner join chk_shop s on s.id=ch.shop_id
                  where ch.owner_id=".$user_id."
                  and ch.DelMark<>1
                  group by ch.id, ch.create_dtm, s.name
                  order by ch.create_dtm desc
                  ");

                $arr=array();
                 while($row=mysql_fetch_object($q))
                  {

                          $arr[]=$row;
                  }
                  return($arr);

          }


          function RestoreCheck($id)
          {
          $User = UserManager::GetCurrentUser();

             TLogMgr::add($id, 'restore', 'chk_check_head');
                  eqr("
                     update chk_check_head set DelMark=0 where id='".$id."' and owner_id=".$User->id."
                  ");

          }

          function DelCheck($id)
          {
          $User = UserManager::GetCurrentUser();

             TLogMgr::add($id, 'del', 'chk_check_head');
                  eqr("
                     update chk_check_head set DelMark=1 where id='".$id."' and owner_id=".$User->id."
                  ");

                  $dets = DbManager::getCheckDetails($id);
                  foreach($dets as $det)
                  {
                          DbManager::recalcActuality($det->ProductID);
                  }

          }

          function getCityByID($id)
          {


                  $row=sqr_obj("
                  select  c.id, c.name, r.name as RegionName, r.id as RegionId
                   from aimw_city c
                   inner join aim_dic_region r on r.id=c.regionID
                   where c.id= ".$id."
                  ");

                  return($row);
          }

          function getCityByName($name)
          {


                  $row=sqr_obj("
                  select  c.id, c.name, r.name as RegionName, r.id as RegionId
                   from aimw_city c
                   inner join aim_dic_region r on r.id=c.regionID
                   where c.name like '".$name."'
                  ");

                  return($row);
          }

          function getUserList($txt)
          {
                    $q=sqr_list("
                    select id, name, login
                    from aim_users
                    where login like '%".$txt."%'
                    ");

        $arr=array();

         while($row=mysql_fetch_object($q))
          {
                 $arr[]=$row;
          }
          return($arr);

          }

          function getCityList($txt)
          {


                    $q=sqr_list("
                    select c.id, c.name, r.name as RegionName
                    from aimw_city c
                    inner join aim_dic_region r on r.id=c.regionID
                    where c.name like '%".$txt."%'
                    ");
                       $arr=array();
        while($row=mysql_fetch_object($q))
         {

                 $arr[]=$row;
         }
          return($arr);
          }

          function getFriendShip($id)
          {
                  $row=sqr_obj('select id, user_id, friend_id, status from chk_friends where id='.$id);
                  return($row);
          }

          function ApproveFriendship($id)
          {
               $FriendShip = DbManager::getFriendShip($id);
               $User = UserManager::GetCurrentUser();
               if ($FriendShip->friend_id!=$User->id) return;
               if ($FriendShip->status=='friend') return;

               $friend = DbManager::getUserByID($FriendShip->user_id);

               eqr('update chk_friends set status="friend" where id='.$id);
               TLogMgr::add($id, 'approve', 'chk_friends',$User->login.'=>'.$friend->login);

               eqr('insert into chk_friends (status, user_id, friend_id, create_dtm) values("friend",'.$User->id.','.$FriendShip->user_id.',"'.date('Y-m-d H:i:s').'")');
               $id=mysql_insert_id();
               TLogMgr::add($id, 'approve', 'chk_friends',$friend->login.'=>'.$User->login);

//               DbManager::RecalcUserFriends($User->id);

               MailMgr::Notify_ApproveFriendship($User, $friend);

               return('ok');
          }

          function DeclineFriendship($id)
          {
               $FriendShip = DbManager::getFriendShip($id);
               $User = UserManager::GetCurrentUser();
               if ($FriendShip->friend_id!=$User->id) return('not friend');

               eqr('update chk_friends set status="decline" where id='.$id);
               TLogMgr::add($id, 'decline', 'chk_friends');

               $friend = DbManager::getUserByID($FriendShip->user_id);

               MailMgr::Notify_DeclineFriendship($User, $friend);


               return('ok');
          }


          function DelFriendship($id)
          {
                  $FriendShip = DbManager::getFriendShip($id);
                  $User = UserManager::GetCurrentUser();

                  if ($FriendShip->user_id!=$User->id) return;

                  eqr('delete from chk_friends where id='.$id);
                  TLogMgr::add($id, 'del', 'chk_friends');

                  if ($FriendShip->status=='friend')
                  {
                          eqr('update chk_friends set status="deleted", create_dtm="'.date('Y-m-d H:i:s').'" where friend_id='.$User->id.' and user_id='.$FriendShip->friend_id);
                          TLogMgr::add($FriendShip->id, 'delete_link', 'chk_friends');
                  }


                  return('ok');
          }

          function getFriendClaimList($uid)
          {
                  $q = sqr_list("
                  select f.id as friendship_id, u.id, u.login, u.name, 'claim' as FriendStatus
                  from chk_friends f
                  inner join aim_users u on u.id=f.user_id
                  where
                  f.status='wait'
                  and
                  f.friend_id=".$uid."
                  ");

                    $arr=array();
                    while($row=mysql_fetch_object($q))
                    {
                     $arr[]=$row;
                    }
                    return($arr);

          }

          function getNewFriendCnt($uid)
          {
                  $row=sqr("
                   select count(f.id) as cnt
                  from chk_friends f
                  where
                  f.status='wait'
                  and
                  f.friend_id=".$uid."
                  ");

                  return($row['cnt']);
          }

          function getFriendsList($uid)
          {
          $q = sqr_list('
          select f.id as friendship_id, u.id, u.login, u.name, f.status as FriendStatus, f2.status inFriendStatus, f.email
          from chk_friends f
          left outer join aim_users u on f.friend_id=u.id
          left outer join chk_friends f2 on f2.user_id=u.id and f.friend_id=f.user_id
          where
          f.user_id='.$uid.'
          order by f.create_dtm desc
          ');

          $arr=array();
          while($row=mysql_fetch_object($q))
          {
           $arr[]=$row;
          }
          return($arr);
          }


          function getUsetStatistic($uid)
          {
          $row=sqr_obj("
          select count(distinct d.product_id) as DetCnt, count(distinct sh.id) as ShopCnt
          from chk_check_head h
          inner join chk_check_det d on d.head_id=h.id
          inner join chk_shop sh on sh.id=h.shop_id
          where h.owner_id=".$uid."
          and h.DelMark=0
          ");

          return($row);
          }

          function getShopListByCity($city_id, $sort)
          {

                        switch ($sort)
                        {

                                case 'by_lines': $s='lines_cnt';break;
                                case 'by_rating': $s='s.rating desc';break;
                                default:
                                                $s='s.name';break;
                        }



                  $q=sqr_list('
                   select distinct s.house_n, s.rating,s.old_rating, s.name as ShopName, st.name as StreetName, s.id as ShopId, count(distinct d.product_id) as lines_cnt, count(distinct c.id) as comment_cnt
                   from aimw_street st
                   inner join chk_shop s on s.street_id=st.id
                   inner join chk_check_head h on h.shop_id=s.id
                   inner join chk_check_det d on d.head_id=h.id
                   left outer join chk_comment c on c.shop_id=s.id
                   where
                   st.city_id='.$city_id.'
                   and h.DelMark=0
                   group by
                   s.house_n, s.rating, s.name, st.name, s.id
                   order by '.$s.'
                  ');

                  $arr=array();
                  while(($row=mysql_fetch_object($q))!=false)
                  {
                   $arr[]=$row;
                  }

                  return($arr);
          }

          function AddInvite($email)
          {
           $User = UserManager::GetCurrentUser();
           if ($User->email==$email) return('Нельзя добавлять себя в друзья.');

           $row=sqr('select id, status from chk_friends where user_id='.$User->id.' and email="'.$email.'"');
             if ($row!=false)
             { return('Приглашение уже выслано.'); }

              eqr('insert into chk_friends (status, email, user_id, friend_id, create_dtm) values("emailed","'.$email.'",'.$User->id.',-1,"'.date('Y-m-d H:i:s').'")');
              $id=mysql_insert_id();
              TLogMgr::add($id, 'add-invite', 'chk_friends',$User->login.'=>'.$email);
              MailMgr::InviteUser($email);

              return('ok');

          }

          function AddFriend($login)
          {
             $User = UserManager::GetCurrentUser();
             $friend=DbManager::GetUserByLogin($login);

             if (!isset($friend->id)) return('Пользователь с таким логином не найден.');
             if ($User->id==$friend->id) return('Нельзя добавлять себя в друзья.');


             $row=sqr_obj('select id, status from chk_friends where user_id='.$User->id.' and friend_id='.$friend->id);
             if (is_object($row))
             {
             if ($row->status!='decline')
             { return('Пользователь уже есть в списке друзей.'); }
             else
             {
               eqr('delete from chk_friends where id='.$row->id);
               TLogMgr::add($id, 'replace(del)', 'chk_friends',$User->login.'=>'.$friend->login);
             }
             }

              eqr('insert into chk_friends (user_id, friend_id, create_dtm) values('.$User->id.','.$friend->id.',"'.date('Y-m-d H:i:s').'")');
              $id=mysql_insert_id();
              TLogMgr::add($id, 'add', 'chk_friends',$User->login.'=>'.$friend->login);

//              DbManager::RecalcUserFriends($User->id);

              MailMgr::Notify_NewFriendship($User, $friend);

              return('ok');
          }


                        function getLastSale($product_id, $shop_id, $uid)
                        {
                        $q='
                         select d.OnePrice as price,DATE_FORMAT(h.create_dtm, "%d.%m.%Y") as dtm, u.login as OwnerLogin, d.id as detid
                         from chk_check_head h
                         inner join chk_check_det d on d.head_id=h.id
                         inner join aim_users u on u.id=h.owner_id
                         where
                         h.DelMark=0
                         and
                         h.shop_id='.$shop_id.'
                         and
                         d.product_id='.$product_id.'
                         and
                          (
                         ';


                                           if ($uid!=-1)
                                           $q.=
                           '
                         h.owner_id='.$uid.'
                                 or h.owner_id in (
                                      select friend_id
                                      from chk_friends
                                      where
                                      user_id='.$uid.'
                                      and status="friend"
                                      )
                                 or';

                         $q.='
                          h.owner_id in (
                                          select userID from
                                          aim_users_params
                                          where
                                          name="openPrices" and paramValue=1
                                        )
                                )
                         order by h.create_dtm desc
                         limit 2
                         ';

//echo $q.'<br/><br/>';

                         $list=sqr_listObj($q);


                         $row=$list[0];

                         if (isset($list[1]))
                         {
                         $row->PrevPrice=$list[1]->price;
                         }



                         return($row);
                        }


            function getProductsByShop($id)
            {
             $User = UserManager::GetCurrentUser();
             $items = array();

             $q=sqr_list('
             select distinct p.name as PrName, p.id as ProductID, count(c.id) as comment_cnt
             from chk_check_head h
             inner join chk_check_det d on d.head_id=h.id
             inner join chk_products p on p.id=d.product_id
             left outer join chk_comment c on c.product_id=p.id and (c.shop_id=h.shop_id or c.shop_id=-1)
             where
             h.DelMark=0 and
             h.shop_id='.$id.'
             group by p.name, p.id
             order by p.name
             ');


             while(($row=mysql_fetch_object($q))!=false)
             {

              if (!$User)
              $uid=-1;
              else
              $uid= $User->id;

              $ls=DbManager::getLastSale($row->ProductID, $id, $uid);

              if (isset($ls))
              {
              $row->price = $ls->price;
              $row->dtm = $ls->dtm;

               if (isset($ls->PrevPrice))
               $row->PrevPrice =$ls->PrevPrice;

              }
              else
              {
                      $row->price = 'n/a';
                      $row->dtm = 'n/a';
              }

              $items[] =$row;
             }
                 return ($items);
            }

            function getShopInfo($id)
            {

             $shop = DbManager::getShopByID($id);
             $User = UserManager::GetCurrentUser();


             $shop->items = DbManager::getProductsByShop($id);

             return($shop);
                  }


                 function getProductByID($id)
                 {
                         $row=sqr_obj('
                         select p.*
                         from chk_products p
                         where p.id='.$id.'
                         ');
                         return($row);
                 }

                 function getLastSaleOwner($product_id, $shop_id)
                 {
                         $row=sqr_obj('
                         select u.login
                         from chk_check_det d
                         inner join chk_check_head h on h.id=d.head_id
                         inner join aim_users u on u.id=h.owner_id
                         where h.DelMark=0
                         and d.product_id='.$product_id.'
                         and h.shop_id='.$shop_id.'
                         order by d.id desc
                         limit 1
                         ');

                         return($row->login);
                 }

                  function getProductInfo($id)
                  {
                     $User = UserManager::GetCurrentUser();

                     $product=DbManager::getProductByID($id);
                     $city_id=UserManager::getUserCity();

                     $q=sqr_list('select distinct  s.id as ShopId, s.name as ShopName, count(c.id) as comment_cnt
                     from chk_check_det d
                     inner join chk_check_head h on h.id=d.head_id
                     inner join chk_shop s on s.id=h.shop_id
                     inner join aimw_street st on st.id=s.street_id
                     left outer join chk_comment c on c.shop_id=s.id
                     where d.product_id='.$id.'
                     and st.city_id='.$city_id.'
                     group by s.id, s.name
                     ');


                     $product->items=array();

                     while(($row=mysql_fetch_object($q))!=false)
                     {
                                           $ls=false;

                                                          if (!$User)
                                              $uid=-1;
                                              else
                                              $uid= $User->id;


                            $ls=DbManager::getLastSale($id, $row->ShopId, $uid);
                            if ($ls)
                            {
                            $row->price = $ls->price;
                            $row->dtm = $ls->dtm;
                            $row->owner = $ls->OwnerLogin;
                            }
                            else
                            {
                                    $row->price = 'n/a';
                                    $row->dtm = 'n/a';
                             $row->owner=DbManager::getLastSaleOwner($id, $row->ShopId);
                            }

                            $product->items[]=$row;
                     }


                     function mysort($a, $b)
                     {
                       if ($a->price == $b->price) {
                        return 0;
                            }
                            return ($a->price < $b->price) ? -1 : 1;
                         }

                         usort( $product->items,'mysort');


                     return($product);
                    }


                    function calcRating()
                    {

                          $q=sqr_list('
select tt2.shop_id, sum(tt2.sm) as sm
from
(
select h.shop_id, avg((1-(d.OnePrice/d2.OnePrice)) * (d.OnePrice/tt.avgPrice)) as sm
                                  from chk_check_head h
                                  inner join chk_check_det d on d.head_id=h.id
                                  inner join chk_check_det d2 on d2.product_id=d.product_id
                                  inner join chk_check_head h2 on h2.id=d2.head_id

                                                                        inner join (
                                                                        select avg(d3.OnePrice) as avgPrice, h3.shop_id
                                                                        from
                                                                        chk_check_head h3
                                                                        inner join chk_check_det d3 on d3.head_id=h3.id

                                                                        where h3.delmark=0
                                                                        and d3.id in (select detid from chk_actual_det)
                                                                        group by  h3.shop_id
                                                                        ) as tt on tt.shop_id=h.shop_id

                                  inner join chk_shop s on s.id=h.shop_id
                                  inner join chk_shop s2 on s2.id=h2.shop_id

                                  inner join aimw_street st on st.id=s.street_id
                                  inner join aimw_street st2 on st2.id=s2.street_id


                                  where st.city_id=st2.city_id and
                                                                        h2.delmark=0 and h.delmark=0
                                                                        and
                                                                        d.id in (select detid from chk_actual_det)
                                                                        and
                                                                        d2.id in (select detid from chk_actual_det)

                                                                        group by h.shop_id, d.product_id) as tt2

                                                                        group by tt2.shop_id


                          ');

                          $i=0;
                          while(($row=mysql_fetch_object($q))!=false)
                          {    $i++;
                          $row->sm=round($row->sm, 2);
                              eqr('update chk_shop set old_rating=rating, rating='.$row->sm.' where id='.$row->shop_id.' and rating<>'.$row->sm);
                          }

                          return($i);
                    }

                    function getBuyList_byProdList($product_id_list, $user_id)
                    {


                               $q=sqr_list("
                     select DATE_FORMAT(h.create_dtm, '%Y-%m-%d') as dtm, sum(d.price) as price
                     from chk_check_head h
                     inner join chk_check_det d on d.head_id=h.id
                     where
                     d.product_id in ".$product_id_list."
                     and
                     h.owner_id=".$user_id."
                     and h.RealBuy=1
                     group by dtm
                     order by h.create_dtm
                     ");

                     $arr=array();

                     while(($row=mysql_fetch_object($q))!=false)
                     {
                             $arr[]=$row;
                     }

                     return($arr);

                    }

                    function getBuyList($product_id, $user_id)
                    {
                     $q=sqr_list("
                     select DATE_FORMAT(h.create_dtm, '%Y-%m-%d') as dtm, sum(d.price) as price
                     from chk_check_head h
                     inner join chk_check_det d on d.head_id=h.id
                     where
                     d.product_id=".$product_id."
                     and
                     h.owner_id=".$user_id."
                     and h.RealBuy=1
                     group by dtm
                     order by h.create_dtm
                     ");

                     $arr=array();

                     while(($row=mysql_fetch_object($q))!=false)
                     {
                             $arr[]=$row;
                     }

                     return($arr);

                    }


                    function MergeProducts($old_id, $new_id)
                    {
                    if ($old_id==$new_id) return;

                    $Product_old = DbManager::getProductByID($old_id);
                    $Product_new = DbManager::getProductByID($new_id);

                       eqr('
                       update chk_check_det set product_id='.$new_id.'
                       where product_id='.$old_id.'
                       ');

                       eqr('
                       delete from chk_products where id='.$old_id.'
                       ');

                       TLogMgr::add($new_id, 'merge', 'chk_products','('.$old_id.') '.$Product_old->name.'=>'.'('.$new_id.') '.$Product_new->name);

                    }

                    function SaveProduct($product)
                    {
                    $Product_old = DbManager::getProductByID($product->id);

                            eqr('
                            update chk_products
                            set
                               name = "'.$product->name.'",
                               tags = "'.$product->tags.'"
                              where
                              id='.$product->id.'
                            ');

                         TLogMgr::add($product->id, 'upd', 'chk_products',$Product_old->name.'=>'.$product->name);

                    }


                    function getProductCatalog($city_id=163)
                    {
//                      $User = UserManager::GetCurrentUser();
//                      if (!$User)
//                      $User->id =-1;

                  //    $city_id=UserManager::getUserCity();

                 //  $city_id=UserManager::getUserCity();

                        // echo $city_id;
                    $list = sqr_listObj('
                    select p.id, p.name, tt.price as price
                    from chk_products p
                    inner join chk_check_det d on d.product_id=p.id
                    inner join chk_check_head h on h.id=d.head_id and h.DelMark=0
                    inner join
                         (
                         select d.product_id, min(d.OnePrice) as price
                         from chk_check_det d
                         inner join chk_v_actual_det vd on vd.detid=d.id
                         inner join chk_check_head h on h.id=d.head_id

                     inner join chk_shop s on s.id=h.shop_id
                     inner join aimw_street st on st.id=s.street_id
                     where st.city_id='.$city_id.'
                          and h.DelMark=0
                          group by  d.product_id

                         ) as tt on tt.product_id=p.id
                    group by p.id, p.name
                    order by p.name
                    ');

                    return ($list);
                    }

                    function AddReport($name, $type_id)
                    {
                            $User = UserManager::GetCurrentUser();

                            eqr('
                            insert into chk_reports (name, owner_id, create_dtm, type_id)
                            values("'.$name.'", '.$User->id.',"'.date('Y-m-d H:i:s').'",'.$type_id.')
                            ');

                            $id=mysql_insert_id();

                            TLogMgr::add($id, 'add', 'chk_report');

                            return($id);

                    }

                    function DelReportDet($detid)
                    {
                     eqr('
                     delete from chk_report_product where id='.$detid.'
                     ');
                    }

                    function AddReportDet($head_id, $product_id)
                    {
                    eqr('
                    insert into chk_report_product(head_id, product_id) values('.$head_id.', '.$product_id.')
                    ');
                    }

                    function getShemaList($uid)
                    {
                            $list = sqr_listObj("
                            select id, name from
                            chk_reports
                            where owner_id=".$uid."
                            ");

                            return($list);
                    }

                    function getShemaDetails($id)
                    {
                            $list=sqr_listObj("
                            select d.id as detid, d.product_id as id, p.name
                            from chk_report_product d
                            inner join chk_products p on p.id=d.product_id
                            where
                            d.head_id=".$id."
                            ");
                            return($list);
                    }


                    function DelReport($id)
                    {
                            eqr('
                            delete from chk_report_product
                            where head_id='.$id.'
                            ');

                            eqr('
                            delete from chk_reports
                            where id='.$id.'
                            ');

                             TLogMgr::add($id, 'del', 'chk_report');


                    }

                      function getTagsByWords($q)
                     {
                             $list = sqr_listObj("
                              select id, name
                              from chk_product_tag
                              where name like '".$q."%'
                             ");


                             return($list);
                     }





                     function getUserTop($n)
                     {
                     $city_id=UserManager::getUserCity();


                             $list=sqr_listObj("

                             select u.login, count(distinct d.product_id) as det_cnt,count(distinct f.id) as fr_cnt,  count(distinct d.product_id)+(count(distinct f.id)*2) as val
                             from
                             aim_users u
                             inner join chk_check_head c on c.owner_id=u.id
                             inner join chk_check_det d on d.head_id=c.id
                             inner join chk_shop s on s.id=c.shop_id
                             inner join aimw_street st on st.id=s.street_id
                             left outer join chk_friends f on  f.user_id=u.id and f.status='friend'
                             where
                             c.DelMark=0
                             and
                             st.city_id=".$city_id."
                             group by u.login
                             order by val desc
                             limit ".$n."

                             ");

                             return($list);
                     }

                                function getShopCPlist($id, $byMin=false)
                                {
                                if ($id=='') return;
                                $User = UserManager::GetCurrentUser();
                                if (!$User)
                                $User->id =-1;

                                if (!$byMin)
                                $sql='tt.OnePrice>pp.min_price and tt.OnePrice<=pp.max_price';
                                else
                                $sql='tt.min_price>pp.min_price and tt.min_price<=pp.max_price';

                                $list = sqr_listObj("
                            select pp2.min_price, pp2.max_price, tt2.cp
                                                from
                                                chk_price_politics pp2
                                                left outer join
                                                (
                                                select pp.min_price, pp.max_price,
                                                avg(tt.OnePrice/tt.min_price-1) as cp
                                                from
                                                (
                                                select distinct min(d2.OnePrice) as min_price,d1.OnePrice, d2.product_id
                                                from chk_check_det d2
                                                inner join chk_check_det d1 on d1.product_id=d2.product_id
                                                inner join chk_actual_det vd on vd.detid=d1.id and vd.user_id=".$User->id."
                                                inner join chk_actual_det vd2 on vd2.detid=d2.id and vd2.user_id=".$User->id."

                                                inner join chk_check_head h1 on h1.id=d1.head_id
                                                where
                                                h1.shop_id=".$id."
                                                and h1.DelMark=0
                                                group by d1.Price, d1.product_id
                                                ) as tt
                                                inner join chk_price_politics pp on ".$sql."
                                                where tt.OnePrice<>tt.min_price
                                                group by pp.min_price, pp.max_price
                                                having count(tt.product_id)>2
                                                ) as tt2 on tt2.min_price=pp2.min_price and tt2.max_price=pp2.max_price
                                                order by pp2.min_price, pp2.max_price
                                ");

                   return($list);
                                }













    /*

                        Users
                */
                function AddUser($user)
                {
                        $conf = uniqid();
                        if (
                         (!isset($user->RegionID))
                         || ($user->RegionID==''))
                         $user->RegionID=-1;


                                    eqr("insert into aim_users (create_dtm, name, login,pass,guid,sendNews,phone,email,city,filename,sex,birthDate,regionID, city_id)
                                    values ('".date("Y-m-d H:i:s")."', '".$user->name."', '".$user->login."', '".$user->pass."','".$conf."','".$user->SendNews."','".$user->phone."','".$user->email."','".$user->city->name."','".$user->filename."','".$user->Sex."','".$user->BirthDate."','".$user->RegionID."',".$user->city->id.") ");
                                    $id=mysql_insert_id();


                                    $byinvite='';
                                    $q=sqr_list('select id from chk_friends where email="'.$user->email.'"');
                                     while($row=mysql_fetch_object($q))
                                     {
                                    eqr('update chk_friends set friend_id='.$id.', status="wait", email="" where id='.$row->id);
                                    $byinvite='by-invite';
                                    }
                                    TLogMgr::add($id, 'add', 'aim_users','marketimho-'.$byinvite);

                        return $conf;
                 }

                function IsUserExists($user)
                {
                        $user = sqr_obj("select id from aim_users where login = '".$user->login."' or email = '".$user->email."' limit 1");
                        if ($user){
                                return true;
                        }
                        return false;
                }

                function UpdateUser($user)
                {

                if (is_object($user->city))
                {
                       $city_id=$user->city->id;
                       $city_name=$user->city->name;
                }
                else
                {
                   $city_id=$user->city_id;
                   $city_name=$user->city;

                }

                eqr("update aim_users set name = '".$user->name."',login = '".$user->login."',pass = '".$user->pass."',isAdmin = ".$user->isAdmin.",isEditor = ".$user->isEditor.",isActive = ".$user->isActive.",city = '".$city_name."', city_id = '".$city_id."', filename = '".$user->filename."',sendNews = ".$user->sendNews.",phone = '".$user->phone."',email = '".$user->email."',sex = '".$user->sex."',birthDate = '".$user->birthDate."',regionID='".$user->regionID."' where id=".intval($user->id));
                }


                                function setUserParams($uid, $name, $value)
                {
                        $row=sqr_obj('select id from aim_users_params where name="'.$name.'" and userID='.$uid);

                        if ($row==false)
                        eqr('insert into aim_users_params (name, userID, paramValue) values("'.$name.'", '.$uid.', "'.$value.'")');
                        else
                        eqr('update aim_users_params set paramValue="'.$value.'" where name="'.$name.'" and userID='.$uid);

                }

                function getUserParams($uid)
                {
                $list=array();
                if ($uid=='') return($list);

                        $list=sqr_listAssoc('
                        select name, paramValue
                        from aim_users_params
                        where userID='.$uid.'
                        ');
                         $arr=array();
                        foreach($list as $item)
                        {
                         $arr[$item['name']] = $item['paramValue'];
                        }


                        return($arr);
                }

                function ActivateUser($login,$conf)
                {
                        $user = DbManager::GetUserByGUID($conf);
                        if (isset($user->id)&&$user->isBlocked==0) {
                                $user->isActive=1;
                                DbManager::UpdateUser($user);
                                return true;
                        }
                        return false;
                }


                function recalcActual()
                {
                 eqr('
                 delete from chk_actual_det
                 ');

                 eqr('
                 insert into `chk_actual_det`
                 select * from chk_v_actual_det
                 ');

                }

                function processDB()
                {

                $pagename='process_db.html';
                 if (!is_cached ($pagename, 43200*2))
                 {

                eqr('delete from chk_check_head where id not in (select head_id from chk_check_det)');
                $txt='<br/>Lost chk_head deleted:'.mysql_affected_rows();

                eqr('delete from chk_check_head where shop_id not in (select id from chk_shop)');
                $txt='<br/>Lost chk_head deleted:'.mysql_affected_rows();

                eqr('delete from chk_check_det where product_id not in (select id from chk_products)');
                $txt.='<br/>Lost chk_det deleted (product):'.mysql_affected_rows();

                eqr('delete from chk_check_det where head_id not in (select id from chk_check_head)');
                $txt.='<br/>Lost chk_det deleted (head):'.mysql_affected_rows();

                eqr('delete from chk_products where id not in (select product_id from chk_check_det)');
                $txt.='<br/>Lost chk_det deleted (head):'.mysql_affected_rows();

                eqr('delete from chk_shop where id not in (select shop_id from chk_check_head)');
                $txt.='<br/>Lost chk_det deleted (head):'.mysql_affected_rows();

                DbManager::recalcActual();
                $txt.='<br/>Recalc Actual Details:'.mysql_affected_rows();


                $i=DbManager::calcRating();
                $txt.='<br/>Пересчитан рейтинг для '.$i.' магазинов.';




                cache_file ($pagename, $txt);
                }

                }


                function RecalcUserFriends($User_id)
                {
                if ($User_id=='') return;
                 $list = DbManager::getFriendsList($User_id);

                 $i=0;
                 $txt='('.$User_id;
                 foreach($list as  $item)
                 {
                 if ($item->id=='') continue;
                 if ($item->FriendStatus!='friend') continue;
                 $txt.=', ';

                  $txt.=$item->id;

                  $i++;
                 }
                 $txt.=')';

                 $txt='"'.$txt.'"';

                 eqr('update aim_users set Friends_id = '.$txt.' where id='.$User_id);

                }

                function GetUserByGUID($guid)
                {
            $result = sqr_obj("select * from aim_users where guid = '$guid'");
            $result->options = DbManager::getUserParams($result->id);
             return $result;
        }

                function GetUserByLoginPass($login,$pass)
                {
                        $result = sqr_obj("select * from aim_users where login = '$login' and pass='$pass' and isActive=1");
                        $result->options = DbManager::getUserParams($result->id);

                        DbManager::processDB();

                        return $result;
                }

                function GetUserByID($id)
                {
                        $result = sqr_obj("select * from aim_users where id = '$id'");
                        $result->options = DbManager::getUserParams($result->id);

                       DbManager::processDB();


                       return $result;
                }

                function GetUserByLogin($login)
                {
                        $result = sqr_obj("select * from aim_users where login = '$login'");
                        $result->options = DbManager::getUserParams($result->id);

                       DbManager::processDB();

                       return $result;
                }

                function GetUserByEmail($email)
                {
                        $result = sqr_obj("select * from aim_users where email = '$email'");
                        $result->options = DbManager::getUserParams($result->id);

                        DbManager::processDB();


                        return $result;
                }


                function getLastNews($n=10)
                {
                $city_id=UserManager::getUserCity();

                $list=sqr_listObj('

                select distinct tt.obj_name, tt.name, tt.id
                from (
                      select
                      distinct l.obj_name, p.name, p.id, l.create_dtm
                      from aimw_log l
                      inner join chk_products p on p.id=l.obj_id
                      inner join chk_check_det d on d.product_id=p.id
                      inner join chk_check_head h on h.id=d.head_id
                      inner join chk_shop s on s.id=h.shop_id
                      inner join aimw_street st on st.id=s.street_id
                      where l.obj_name="chk_products" and l.action_name="add"
                      and st.city_id='.$city_id.'

                      union all

                      select
                      distinct l.obj_name, s.name, s.id, l.create_dtm
                      from aimw_log l
                      inner join chk_shop s on s.id=l.obj_id
                      inner join aimw_street st on st.id=s.street_id
                      where l.obj_name="chk_shop"
                      and st.city_id='.$city_id.' and l.action_name="add"
                      ) as tt
                      order by tt.create_dtm desc
                      limit '.$n.'

                ');

                return($list);
                }

                function getProductPriceDinamic($product_id, $shop_id)
                {
                  $User = UserManager::GetCurrentUser();

                  $list=sqr_listObj("
                     select distinct DATE_FORMAT(h.create_dtm, '%Y-%m-%d') as dtm,DATE_FORMAT(h.create_dtm, '%d.%m.%Y') as dtm2, d.OnePrice as price, u.login
                     from chk_check_head h
                     inner join aim_users u on u.id=h.owner_id
                     inner join chk_check_det d on d.head_id=h.id
                     inner join chk_v_det_by_user vd on vd.id=d.id and vd.user_id=".$User->id."
                     where
                     d.product_id=".$product_id." and h.shop_id=".$shop_id."
                     order by h.create_dtm
                     ");


                  return($list);
                }


                function getCommentList($shop_id, $product_id, $nav_city_id)
                {
                $where='';
                if ($shop_id!=-1)
                $where.='c.shop_id='.$shop_id;

                if ($shop_id!=-1 && $product_id!=-1)
                $where.=' and ';


                if ($product_id!=-1)
                $where.='c.product_id = '.$product_id;

                if ($product_id!=-1 && $shop_id==-1)
                $where.=' and c.city_id='.$nav_city_id;



                 $list = sqr_listObj('
                  select c.description,  DATE_FORMAT(c.create_dtm, "%d.%m.%Y %h:%i:%s") as create_dtm, u.login, c.owner_id, c.id,
                         s.name as shop_name, c.shop_id,
                         p.name as product_name, c.product_id
                  from chk_comment c
                  inner join aim_users u on u.id=c.owner_id
                  left outer join chk_shop s on s.id=c.shop_id
                  left outer join chk_products p on p.id=c.product_id
                  where
                     '.$where.'
                     order by id desc
                 ');

                 return($list);
                }


                function getCommentByID($id)
                {
                 $row = sqr_obj('
                 select * from chk_comment where id='.$id.'
                 ');

                 return($row);
                }

                function AddComment($product_id, $shop_id, $city_id, $comment)
                {
                $User = UserManager::GetCurrentUser();

                eqr('insert into chk_comment(create_dtm, owner_id, product_id, shop_id, city_id, description)
                     values("'.date('Y-m-d H:i:s').'", '.$User->id.','.$product_id.', '.$shop_id.', '.$city_id.', "'.$comment.'")
                ');
                $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'chk_comment');
                }

                function DelComment($id)
                {
                $comment = DbManager::getCommentByID($id);
                $User = UserManager::GetCurrentUser();

                if ($User->id!=$comment->owner_id) return;

                eqr('
                delete from chk_comment where id='.$id.'
                ');

                TLogMgr::add($id, 'del', 'chk_comment');
                }

                function getUserPlan_Products($User, $data=false)
                {
                $city_id = UserManager::getUserCity();

                   $q="
                   select pl.id, pl.product_name as name, pl.product_id, pl.cnt
                   from chk_user_plan pl
                   where
                   pl.user_id=".$User->id."
                   ";

                   if ($data!=false)
                   {
                   $q.=' and pl.id in (';
                   $i=0;
                     foreach($data as $item)
                     {
                     if ($i>0) $q.=',';
                      $q.=$item;
                      $i++;
                     }
                     $q.=')';
                   }


                   $list= sqr_listObj($q);


                                $shop=-1;
                                if (isset($User->options['inplan_shop']))
                                $shop=$User->options['inplan_shop'];

                                $i=0;

                        $curUser = UserManager::GetCurrentUser();

                        $cuid=-1;
                        if ($curUser)
                        $cuid=$curUser->id;

                                foreach ($list as $item)
                                {
                                 $ls=array();
                                         if ($shop!=-1)
                                         {
                                                                if ($item->product_id!=-1)
                                                                {
                                                                         $ls=DbManager::getLastSale($item->product_id, $shop, $cuid);
                                                                         $list[$i]->price = $ls->price;
                                                                }
                                                                else
                                                                $list[$i]->price = 'n/a';
                                         }
                                         else
                                         {
                                          if ($item->product_id!=-1)
                                          {
                                          $row=sqr_obj("
                                          select d.product_id, min(d.OnePrice) as min_price,max(d.OnePrice) as max_price
                                          from chk_check_det d
                                          inner join chk_v_actual_det vd on vd.detid=d.id
                                          inner join chk_check_head h on h.id=d.head_id
                                          inner join chk_shop s on s.id=h.shop_id
                                          inner join aimw_street st on st.id=s.street_id
                                          where st.city_id=".$city_id."
                                          and d.product_id=".$item->product_id."
                                          and h.DelMark=0
                                          group by  d.product_id
                                          ");

                                          $list[$i]->min_price = $row->min_price;
                                          $list[$i]->max_price = $row->max_price;
                                          }



                                         }
                                         $i++;
                                }

                return($list);
                }

                function AddProductToBuyPlan($uid, $product, $cnt)
                {
                 $Product = DbManager::getProductByName($product);
                 $pid=-1;

                                  if ($Product)
                 $pid = $Product->id;


                 eqr('insert into chk_user_plan (product_id, user_id, cnt, product_name)
                                 values('.$pid.', '.$uid.','.$cnt.',"'.$product.'")
                 ');

                }

                function getBuyPlanByID($id)
                {
                 $row=sqr_obj('select * from chk_user_plan where id='.$id);
                        return($row);
                }

                function DelBuyPlan($id)
                {
                        eqr('delete from chk_user_plan where id='.$id);
                }


        function getUserMessages($uid)
        {

                $list = sqr_listObj("
                        select count(mp.id) as  parent_view, m.isView2, m.user_id, m.user2_id, m.id, u2.login as user_name2, u.login as user_name, DATE_FORMAT(m.dtm,'%d.%m.%y %h:%i:%s') as dtm, m.isView, SUBSTRING(m.message,1,56) as message
                        from
                        aim_message m
                        inner join aim_users u on u.id=m.user_id
                        inner join aim_users u2 on u2.id=m.user2_id
                        left outer join aim_message mp on mp.parent_id=m.id  and ((mp.isView=0 and mp.user_id=".$uid.") or (mp.isView2=0 and mp.user2_id=".$uid."))
                        where
                        (
                        m.user_id=".$uid."
                        or
                        m.user2_id=".$uid."
                        )
                        and m.parent_id=0
                        group by
                        m.isView2, m.user_id, m.user2_id, m.id, u2.login, u.login, m.dtm, m.isView, m.message
                        order by m.last_active desc
                ");

                return($list);
        }

        function getMessageByID($id)
        {


                      $list = sqr_obj("
                        select m.isView2,u.sex, u2.sex as sex2, m.id,m.user_id,m.user2_id, u2.login as user_name2, u.login as user_name, DATE_FORMAT(m.dtm,'%d.%m.%y %h:%i:%s') as dtm, m.isView, m.message
                        from
                        aim_message m
                        inner join aim_users u on u.id=m.user_id
                        inner join aim_users u2 on u2.id=m.user2_id
                        where
                        m.id=".$id."
                ");

                return($list);


        }


        function addMessage($user_id, $message, $user2_id, $parent_id=0)
        {

        $curUser = UserManager::GetCurrentUser();
                eqr("
                     insert into aim_message(user_id, dtm, message, isView, user2_id, parent_id, last_active, isView2)
                     values
                     (".$user_id.", '".date('Y-m-d H:i:s')."','".$message."', 0, ".$user2_id.",".$parent_id.",  '".date('Y-m-d H:i:s')."', 1)
                ");

                $id=mysql_insert_id();

                if ($parent_id!=0)
                {

                 eqr("update aim_message set last_active = '".date('Y-m-d H:i:s')."' where id=".$parent_id." ");
/*
                $m = DbManager::getMessageByID($parent_id);
               if ($m->user_id != $user2_id)
                eqr("update aim_message set isView = 0 where id=".$parent_id." ");
                else
                eqr("update aim_message set isView2 = 0 where id=".$parent_id." ");
*/
                }


                TLogMgr::add($id, 'add', 'aim_message');
                return($id);
        }

        function getMessageListByParentID($id)
        {
                $list = sqr_listObj("
                        select m.isView2,u.sex, m.id,m.user_id,m.user2_id, u.login as user_name, DATE_FORMAT(m.dtm,'%d.%m.%y %h:%i:%s') as dtm, m.isView, m.message
                        from
                        aim_message m
                        inner join aim_users u on u.id=m.user2_id
                        where
                        m.parent_id=".$id."
                ");

                return($list);
        }

        function ApplyViewMessage($id, $vn='')
        {
         $curUser = UserManager::GetCurrentUser();
                eqr("
                update aim_message set isView".$vn." = 1 where id=".$id."
                ");

                eqr("
                update aim_message set isView = 1 where parent_id=".$id." and user_id = ".$curUser->id."
                ");

                eqr("
                update aim_message set isView2 = 1 where parent_id=".$id." and user2_id = ".$curUser->id."
                ");

        }

        function getNewMessageCnt($uid)
        {

                $row=sqr_obj("
                select count(id) as cnt
                from aim_message
                where
                (user_id=".$uid." and isView=0)
                or
                (user2_id=".$uid." and isView2=0)

                ");
                return($row->cnt);
        }

        function postUserActive()
        {

               $curUser = UserManager::GetCurrentUser();
               eqr("
               update aim_users set last_active='".date('Y-m-d H:i:s')."' where id=".$curUser->id."
               ");
        }

          function GetUser($id)
        {
                return DbManager::GetUserByID($id);
        }


}
