<?
//ss
$incadd = "";
while (!file_exists($incadd."dbmanager.php")){
        $incadd = $incadd."../";
}


include_once($incadd."config/common.php");

include_once($incadd."dbaccessor.php");
include_once($incadd."functions.php");

include_once($incadd."lib/f.inc");
include_once($incadd."lib/LogMgr.inc");
include_once($incadd."lib/myway/object.inc");
include_once($incadd."lib/myway/category.inc");
include_once($incadd."lib/myway/city.inc");
include_once($incadd."lib/myway/street.inc");
include_once($incadd."lib/myway/objectrate.inc");

class MyWayDbManager extends DbAccessor
{
        var $connect = null;

        function MyWayDbManager()
        {
                        parent::DbAccessor();
        }

        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new MyWayDbManager();
                return $instance;
        }


 function GetAllGrantedRates_byUserWay($uid, $lineFrom=0,$lineTo=0)
        {



                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                u.id as AuthorID,
                                                r.isDeleted, r.isGranted
                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                inner join aimw_userways w on w.street_id=s.id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id
                                                where
                                                r.isDeleted = 0 and (r.isGranted = 1 or r.author_id=".$uid.")
                                                and w.user_id=".$uid."
                                                order by r.id DESC $lim")
                                        );




                       //         cache_file ($filename, serialize($res));
                                return $res;
                                }


 function GetAllGrantedRates_byStreet($street_id, $lineFrom=0,$lineTo=0)
        {

                             $User = UserManager::GetCurrentUser();



                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                r.isGranted, r.isDeleted
                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id
                                                where
                                                r.isDeleted = 0 and (r.isGranted = 1 or r.author_id=".intval($User->id).")
                                                and r.street_id=".$street_id."
                                                order by r.id DESC $lim")
                                        );





                                return $res;
                                }


       function GetAllRates_byObject($obj_id, $lineFrom=0,$lineTo=0)
        {

                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                o.name as object_name, o.id as object_id,
                                                u.login as AuthorLogin,
                                                u.id as AuthorID,
                                                s.id as street_id, s.name as street_name,
                                                s.city_id,
                                                r.isGranted,
                                                r.isDeleted
                                                from aimw_objectrate r
                                                inner join aim_users u on u.id=r.author_id
                                                inner join aimw_objects o on o.id=r.object_id
                                                inner join aimw_street s on s.id = o.street_id
                                                where
                                                o.id=".$obj_id."
                                                order by r.id DESC $lim")
                                        );




                       //         cache_file ($filename, serialize($res));
                                return $res;
                                }


  function GetAllRates_byCategory_andStreet($cat_id,$street_id, $lineFrom=0,$lineTo=0)
        {



                        $cfg = Config::get_Instance();

                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                o.name as object_name, o.id as object_id,
                                                s.id as street_id, s.name as street_name,
                                                s.city_id,
                                                u.login as AuthorLogin,
                                                r.isGranted, r.isDeleted
                                                from aimw_objectrate r
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                inner join aimw_street s on s.id=r.street_id
                                                where
                                                r.category_id=".$cat_id." and
                                                r.street_id=".$street_id."
                                                and r.isDeleted=0

                                                order by r.id DESC $lim")
                                        );

                        return $res;
                                }

function GetAllGrantedRates_byCategory_andCity($cat_id,$city_id, $lineFrom=0,$lineTo=0)
        {


                       $User = UserManager::GetCurrentUser();

                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select
                                                r.isGranted,
                                                r.isDeleted,
                                                r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                o.name as object_name, o.id as object_id,
                                                u.login as AuthorLogin,
                                                s.id as street_id, s.name as street_name
                                                from aimw_objectrate r
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                inner join aimw_street s on s.id=r.street_id
                                                where
                                                r.isDeleted = 0 and (r.isGranted = 1 or r.author_id=".intval($User->id).")
                                                and
                                                r.category_id=".$cat_id." and
                                                s.city_id=".$city_id."
                                                and r.isDeleted=0
                                                order by r.id DESC $lim")
                                        );



                        return $res;
                                }

 function GetAllRates($city=-1,$region=-1,$lineFrom=0,$lineTo=0, $isgranted=1)
        {


                                                   $User = UserManager::GetCurrentUser();
                      $uid=-1;
                      if ($User) $uid=$User->id;

                        $lim = "";
                        if ($lineTo>0){$lim = " LIMIT $lineFrom,$lineTo";}

                        if ($city==-1){

                                $gratxt='';
                if ($isgranted==1)
                {
                $gratxt="r.isDeleted = 0 and (r.isGranted = 1 or r.author_id=".intval($uid).")";

                }


                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                u.id as AuthorID,
                                                r.isGranted,
                                                r.isDeleted

                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id
                                                where
                                                ".$gratxt."
                                                order by r.id DESC $lim")
                                        );





                                return $res;

                        } else {

                         $gratxt='';
                if ($isgranted==1)
                {
                $gratxt="r.isDeleted = 0 and (r.isGranted = 1 or r.author_id=".intval($User->id).") and";

                }


                                                                $res = $this->GetRatesListInternal(
                                        $this->ExecuteList("
                                                select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                r.isGranted,
                                                r.isDeleted
                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id
                                                where ".$gratxt."
                                                ci.id=".$city."
                                                order by id DESC $lim")
                                );


                       //         cache_file ($filename, serialize($res));
                                return $res;
                        }
        }


            function GetRatesListInternal($rs)
        {
                        $list = array();

                        if($rs)
                        {
                                while($rs->get_Current())
                                {
                                        $num = $this->GetRateInternal($rs->MoveNext());
                    $list[count($list)] = $num;
                                }
                        }
                        return $list;
        }


        function GetRateInternal($row)
        {
//   select r.id, r.create_dtm,r.description, s.name as street_name,c.name as category_name, o.name as object_name
                        $rate = new ObjectRate();
                        $rate->set_ID($row["id"]);

                        $rate->set_Description($row["comment"]);
                        $rate->set_Create_dtm($row["create_dtm"]);
                        $rate->set_AuthorLogin($row["AuthorLogin"]);

                        $rate->street->set_Name($row["street_name"]);
                        $rate->street->set_Id($row["street_id"]);

                        $rate->city->set_Name($row["city_name"]);
                        $rate->city->set_Id($row["city_id"]);


                        $rate->category->set_Name($row["category_name"]);
                        $rate->category->set_Id($row["category_id"]);

                        $rate->object->set_Name($row["object_name"]);
                        $rate->object->set_Id($row["object_id"]);

                        $rate->set_FileName($row["filename"]);
                        $rate->set_Rating($row["rating"]);

                        $rate->isGranted=$row["isGranted"];
                        $rate->isDeleted=$row["isDeleted"];

                        return $rate;
        }

        function getMyWayRating()
        {
                $Um = UserManager::get_Instance();
                $u=$Um->GetCurrentUser();

                $row = sqr_obj('select sum(s.rating) as summ
                                from aimw_userways w
                                inner join aimw_street s on w.street_id=s.id
                                where
                                w.user_id='.$u->id.'
                ');

                return($row->summ);
        }

        function getMyWayStreetCnt()
        {
                            $Um = UserManager::get_Instance();
                $u=$Um->GetCurrentUser();

                $row = sqr_obj('select count(distinct w.street_id) as cnt
                                from aimw_userways w
                                where
                                w.user_id='.$u->id.'
                ');

                return($row->cnt);
        }

          function GetGrantedRatesPerPage_byUserWay(&$pageCount, $page = 1, $perPage = 10)
          {
                $Um = UserManager::get_Instance();
                $u=$Um->GetCurrentUser();

                $commentCount = $this->GetGrantedRatesCount_inWay($u->id);
                $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }
                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;
                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");
                        return $this->GetAllGrantedRates_byUserWay($u->id,$from,$to-$from);

          }

          function GetRatesPerPage_byCity(&$pageCount, $page = 1, $region=-1, $city_id=-1, $perPage = 10, $isgranted=1)
                {
                        $commentCount = $this->GetRatesCount($city_id, $isgranted);
                        $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }
                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;

                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");

                        return $this->GetAllRates($city_id, $region,$from,$to-$from, $isgranted);
                        }


          function GetGrantedRatesPerPage_byStreet(&$pageCount, $page = 1, $street_id=-1, $perPage = 10)
                {
                        $commentCount = $this->GetGrantedRatesCount_byStreet($street_id);
                        $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }


                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;
                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");

                        return $this->GetAllGrantedRates_byStreet($street_id,$from,$to-$from);
                        }


          function GetAllRatesPerPage_byObject(&$pageCount, $page = 1, $obj_id=-1, $perPage = 10)
                {
                        $commentCount = $this->GetAllRatesCount_byObject($obj_id);
                        $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }
                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;
                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");
                        return $this->GetAllRates_byObject($obj_id,$from,$to-$from);
                        }

 function GetAllRatesPerPage_byCategory_andStreet(&$pageCount, $page = 1, $cat_id=-1,$street_id=-1, $perPage = 10)
                {
                        $commentCount = $this->GetAllRatesCount_byCategory_andStreet($cat_id, $street_id);
                        $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }
                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;
                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");
                        return $this->GetAllRates_byCategory_andStreet($cat_id, $street_id,$from,$to-$from);
                        }

 function GetAllGrantedRatesPerPage_byCategory_andCity(&$pageCount, $page = 1, $cat_id=-1,$city_id=-1, $perPage = 10)
                {
                        $commentCount = $this->GetAllRatesCount_byCategory_andCity($cat_id, $city_id);
                        $count = $perPage;
                        $pageCount = intval($commentCount / $count);
                                                $from = ($page-1) * $count;
                        if ($commentCount % $count != 0){
                                                        $pageCount = $pageCount + 1;
                        }
                                                if ($page>$pageCount){
                                                        $page = 1;
                                                        $from = 0;
                                                }

                        $to = $page * $count;
                        $i = 0;
                                                //print_r("list ->".$region.",".$from.",".($to-$from).",$pageCount,$page");
                        return $this->GetAllGrantedRates_byCategory_andCity($cat_id, $city_id,$from,$to-$from);
                        }


function GetGrantedRatesCount_inWay($uid=-1)
                {
               return $this->ExecuteValue("
               select count(r.id)
               from aimw_objectrate r
               inner join aimw_userways w on w.street_id=r.street_id
               where
               r.isDeleted = 0 and r.isGranted = 1
               and w.user_id=".$uid."
               ");
                }

     function GetRatesCount($city=-1, $isgranted=1)
                {
                $gratxt1='';
                $gratxt2='';
                if ($isgranted==1)
                {
                $gratxt1="r.isDeleted = 0 and r.isGranted = 1 and";
                $gratxt2="where r.isDeleted = 0 and r.isGranted = 1";
                }



                    if ($city==-1){
                                return $this->ExecuteValue("select count(r.id) from aimw_objectrate r ".$gratxt2);
                        } else {
                                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aimw_objectrate r
                                        inner join aimw_street s on s.id=r.street_id
                                        where ".$gratxt1." s.city_id=".$city
                                );
                        }
        }

        function GetGrantedRatesCount_byStreet($street_id)
                {

                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aimw_objectrate r
                                        where r.isDeleted = 0 and r.isGranted = 1
                                        and r.street_id=".$street_id
                                );

        }

            function GetAllRatesCount_byCategory_andCity($cat_id, $city_id)
                {

                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aimw_objectrate r
                                        inner join aimw_street s on s.id = r.street_id
                                        where
                                        r.isDeleted = 0 and r.isGranted = 1  and
                                        r.category_id=".$cat_id."
                                        and s.city_id=".$city_id."
                                        "
                                );

        }

           function GetAllRatesCount_byCategory_andStreet($cat_id, $street_id)
                {

                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aimw_objectrate r
                                        where
                                        r.category_id=".$cat_id."
                                        and r.street_id=".$street_id."
                                        "
                                );

        }


          function GetAllRatesCount_byObject($obj_id)
                {

                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aimw_objectrate r
                                        where r.object_id=".$obj_id
                                );

        }



        function getCityList($region=-1)
        {
        $qt="
                select c.id, c.name, r.name as regionName
                from aimw_city c
                inner join aim_dic_region r on c.regionID=r.id
                ";

                if ($region!=-1)
                $qt.=' where r.id='.$region;

                $qt.=' order by c.name';

                $q=sqr_list($qt);

        $arr=array();

        while($row=mysql_fetch_object($q))
         {
                 $arr[]=$row;
         }

         return($arr);

        }

        function getCityByMask($mask)
        {
        $arr=array();

        $q=sqr_list('SELECT c.id, r.name as regionName, c.name
                                FROM aimw_city c
                                inner join aim_dic_region r on c.regionID=r.id
                                where
                                c.name like "'.$mask.'%"');


        while($row=mysql_fetch_object($q))
         {
                 $arr[]=$row;
         }
          return($arr);

        }

        function getCityFromRow($row)
        {
                 $ret = new City();
                        $ret->set_ID($row['id']);

                        $ret->set_Name($row['name']);

                        $ret->set_RegionName($row['regionName']);
                        $ret->set_RegionId($row['regionID']);
                        return($ret);
        }

        function getObjectFromRow($row)
        {
                  $ret = new Object();
                        $ret->set_ID($row['id']);
                        $ret->set_Name($row['name']);
                        $ret->street->set_Id($row['street_id']);
                        return($ret);
        }

        function getStreetFromRow($row)
        {
                 $ret = new Street();
                        $ret->set_ID($row['id']);
                        $ret->set_Name($row['name']);
                        $ret->city->set_Id($row['city_id']);
                        return($ret);
        }

        function getCityByID($id)
        {
                $row=sqr('select id, name, regionID from aimw_city where id='.$id);
                if ($row!=false)
                {
                  return($this->getCityFromRow($row));
                }
                else
                return(false);

        }


        function getCityByName($name)
        {
                $row=sqr('select id, name, regionID from aimw_city where name="'.$name.'" limit 1');
                if ($row!=false)
                {
                       return($this->getCityFromRow($row));
                }
                else
                return(false);
        }


        function getStreetCnt_byCity($city_id)
        {
           $row=sqr('select count(id) as cnt from aimw_street where city_id='.$city_id);
           return($row['cnt']);

        }

        function getObjects_byCity($city_id)
        {
                $q=sqr_list('
                select o.id, o.name, s.name as street_name, s.id as street_id, c.name as cat_name, c.id as cat_id
                from aimw_objects o
                inner join aimw_street s on s.id=o.street_id
                inner join aimw_category c on c.id=o.category_id
                where s.city_id='.$city_id.'
                order by c.name, o.name
                ');

                $arr=array();
                while(($row=mysql_fetch_object($q))!=false)
                {
                $arr[]=$row;
                }

                return($arr);
        }

        function getObjects_byStreet($street_id)
        {
                $q=sqr_list('
                select o.id, o.name, c.name as cat_name, c.id as cat_id
                from aimw_objects o
                inner join aimw_category c on c.id=o.category_id
                where street_id='.$street_id.'
                ');


                $arr=array();
                while(($row=mysql_fetch_object($q))!=false)
                {
                $arr[]=$row;
                }

                return($arr);
        }

        function getObjectCnt_byCity($city_id)
        {
           $row=sqr('select count(o.id) as cnt
                     from aimw_objects o
                     inner join aimw_street s on s.id=o.street_id
                     where s.city_id='.$city_id);
           return($row['cnt']);

        }

        function getCityRating($city_id)
        {
            $row=sqr('select sum(s.Rating) as rating
                     from aimw_street s
                     where
                     s.city_id='.$city_id);
           return($row['cnt']);
        }

        function getObjectCnt_byStreet($street_id)
        {

           $row=sqr('select count(id) as cnt from aimw_objects where street_id='.$street_id);
           return($row['cnt']);

        }

        function getObjectTopByCat($cat_id)
        {
            $q=sqr_list('
                  select id, name, rating
                  from aimw_objects
                  where category_id='.$cat_id.'
                  and rating>0
                  order by rating desc
                  limit 5
                  ');

                   $arr=array();

                  while($row=mysql_fetch_object($q))
                  {
                          $arr[]=$row;
                  }

                   $q=sqr_list('
                  select id, name, rating
                  from aimw_objects
                  where category_id='.$cat_id.'
                  and rating<0
                  order by rating desc
                  limit 5
                  ');



                  while($row=mysql_fetch_object($q))
                  {
                          $arr[]=$row;
                  }

                  return($arr);
        }

        function getStreetTop($cityid)
        {
                  $q=sqr_list('
                  select id, name, rating
                  from aimw_street
                  where city_id='.$cityid.'
                  and rating>0
                  order by rating desc
                  limit 5
                  ');
                   $arr=array();

                  while($row=mysql_fetch_object($q))
                  {
                          $arr[]=$row;
                  }


                  $q=sqr_list('
                  select id, name, rating
                  from aimw_street
                  where city_id='.$cityid.'
                  and rating<0
                  order by rating desc
                  limit 5
                  ');


                  while($row=mysql_fetch_object($q))
                  {
                          $arr[]=$row;
                  }

                  return($arr);

        }

        function getStreetList($cityid)
        {
        $Um = UserManager::get_Instance();
        $u=$Um->GetCurrentUser();
                          $uid=-1;
                  if ($u->id) $uid=$u->id;

                  $q=sqr_list('
                  select s.id, s.name, w.id as wayid
                  from
                  aimw_street s
                  left outer join aimw_userways w on w.street_id=s.id and w.user_id='.$uid.'
                  where
                  s.city_id='.$cityid.'
                  order by s.name');
                  $arr=array();

         while($row=mysql_fetch_object($q))
         {
                 $arr[]=$row;
         }
         return($arr);

        }

        function getStreetByMask($mask, $city, $city_id=0)
        {
        $arr=array();

        $rcity = $this->getCityByName($city);

        if ($rcity==false) return($arr);

        if ($rcity->id!=$city_id)
        $city_id=$rcity->id;



        $qt='
        select s.id, s.name
        from aimw_street s
        where
        s.city_id='.$city_id.'
        and
        s.name like "%'.$mask.'%"';

//        echo $qt;
        $q=sqr_list($qt);

        while($row=mysql_fetch_object($q))
         {

                 $arr[]=$row;
         }
          return($arr);

        }


         function getStreetByName_andCity($name, $city_id)
        {
                $row=sqr('select id, name, city_id from aimw_street where name = "'.$name.'" and city_id="'.$city_id.'" limit 1');

                if ($row!=false)
                {
                       return($this->getStreetFromRow($row));
                }
                else
                return(false);
        }

          function getStreetByID($id)
        {
                $row=sqr_obj('select s.id, s.name, s.city_id, s.rating, c.name as city_name, c.id as city_id
                              from aimw_street s
                              inner join aimw_city c on c.id=s.city_id
                              where s.id='.$id.' limit 1');

                if ($row!=false)
                {
                       return($row);
                }
                else
                return(false);
        }

        function getObjByMask($mask, $city, $city_id=-777, $street, $street_id=0, $cat_id=-1)
        {
        $arr=array();

        if ($street_id==0)
        {
        $rcity = $this->getCityByName($city);

        if ($rcity==false) return($arr);

        if ($rcity->id!=$city_id)
        $city_id=$rcity->id;

        $rstreet = $this->getStreetByName_andCity($street,$city_id);

        if ($rstreet->id!=$street_id)
        $street_id=$rstreet->id;
        }




        $qt='
        select o.id, o.name
        from aimw_objects o
        where
        o.street_id='.$street_id.'
        and
        o.name like "%'.strtolower($mask).'%"';

        if ($cat_id!=-1)
        $qt.='
        and o.category_id='.$cat_id;

         // echo $qt;
       $q=sqr_list($qt);
        while($row=mysql_fetch_object($q))
         {
                 $arr[]=$row;
         }
          return($arr);

        }

        function getCatByID($id)
        {
                $row=sqr_obj('
                select id, name, useintop from aimw_category
                where id='.$id.'
                ');

                return($row);

        }

        function getCategoriesList()
        {
                $q=sqr_list('
                select id, name, useintop from aimw_category
                order by useintop, name
                ');

                $arr=array();
                while ($row=mysql_fetch_object($q))
                {
                       $arr[]=$row;
                }
                return($arr);
        }

        function addCity($city_name)
        {
                eqr('insert into aimw_city(name) values("'.$city_name.'")');
                $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'city');
                return($id);
        }

        function processCity($city_name)
        {
         $city = $this->getCityByName($city_name);

           if ($city==false)
           $id=$this->addCity($city_name);
           else
           $id=$city->id;

           return($id);
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

         $street = MyWayDbManager::getStreetByName_andCity($street_name, $city_id);

           if ($street==false)
           {

           $id=MyWayDbManager::addStreet($street_name, $city_id);
           }
           else
           $id=$street->id;

           return($id);
          }

    function getObjectByID($id)
        {
                $row=sqr_obj('select distinct o.id, o.name, o.street_id, o.rating, c.id as cat_id, c.name as cat_name,
                s.city_id, s.name as street_name,ci.name as city_name
                from aimw_objects o
                inner join aimw_category c on c.id=o.category_id
                inner join aimw_street s on s.id=o.street_id
                inner join aimw_city ci on ci.id=s.city_id
                where o.id='.$id);
                return($row);
        }

         function getObjectByName_andStreet_andCat($name, $street_id, $cat_id)
        {
                $row=sqr_obj('select id, name, street_id from aimw_objects where name = "'.$name.'" and street_id="'.$street_id.'" and category_id="'.$cat_id.'" limit 1');

                return($row);
        }


        function addObject($obj_name, $street_id, $cat_id)
        {
                eqr('insert into aimw_objects(name, street_id, category_id) values("'.$obj_name.'", '.$street_id.', '.$cat_id.')');
                $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'objects');
                return($id);
        }

        function processObject($obj_name, $street_id, $cat_id)
        {
        if ($obj_name=='') return(0);

         $obj = $this->getObjectByName_andStreet_andCat($obj_name, $street_id, $cat_id);

           if ($obj==false)
           $id=$this->addObject($obj_name, $street_id, $cat_id);
           else
           $id=$obj->id;

           return($id);
          }

          function AddRate($rate, $desc, $street_id, $object_id, $category_id, $fn)
          {
           $Um = UserManager::get_Instance();
           $u=$Um->GetCurrentUser();
           $dt = date('Y-m-d h:i:s');

             eqr('insert into aimw_objectrate (street_id, object_id, category_id, author_id, create_dtm, rating, comment, filename)
                  values ('.$street_id.','.$object_id.','.$category_id.','.$u->id.',"'.$dt.'", '.$rate.', "'.$desc.'", "'.$fn.'")
             ');
             $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'objectrate');
                return($id);
          }

          function UpdateRate($rate_id, $rate, $desc, $street_id, $object_id, $category_id, $fn, $deleteOld=0)
          {
          $ufn='';



          if ($fn!='')
          $ufn=',
             filename = "'.$fn.'"
          ';
          elseif ($deleteOld==1)
          $ufn=',
             filename = ""
          ';
             echo
            eqr('
            update aimw_objectrate
            set
             street_id = '.$street_id.',
             object_id = '.$object_id.',
             category_id = '.$category_id.',
             comment = "'.$desc.'",
             rating = '.$rate.'
             '.$ufn.'
            where
            id='.$rate_id.'
            ');

             TLogMgr::add($rate_id, 'edit', 'objectrate');
          }


          function getWayByStreet_andUser($street_id, $user_id)
          {
          if (($street_id=='') || ($user_id=='')) return(false);
                  $row=sqr('select id from aimw_userways where street_id='.$street_id.' and user_id='.$user_id);

                  return($row);

          }

          function AddWay($street_id, $user_id)
          {
           eqr('insert into aimw_userways (user_id, street_id) values('.$user_id.', '.$street_id.') ');
           $id=mysql_insert_id();
                TLogMgr::add($id, 'add', 'userways');
                return($id);
          }

          function DelWay($id)
          {

          eqr('delete from aimw_userways where id='.$id);
          TLogMgr::add($id, 'del', 'userways');
           return($id);

          }

          function getWaysCntForUser($uid)
          {
                  $row=sqr('select count(id) as cnt from aimw_userways where user_id='.$uid);
                  if ($row!=false)
                  return($row['cnt']);
                  else
                  return(0);
          }

           function getRateByID2($id)
          {
                  $row = sqr('
                  select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                u.id as AuthorID,
                                                r.isGranted,
                                                r.isDeleted

                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id

                   where r.id='.$id);
                  return($this->GetRateInternal($row));
          }

          function getRateByID($id)
          {
                  $row = sqr_obj('
                  select r.id,r.filename,r.rating, r.create_dtm,r.comment,
                                                s.name as street_name, s.id as street_id,
                                                c.name as category_name, c.id as category_id,
                                                o.name as object_name, o.id as object_id,
                                                ci.name as city_name, ci.id as city_id,
                                                u.login as AuthorLogin,
                                                u.id as AuthorID,
                                                r.isGranted,
                                                r.isDeleted

                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                inner join aim_users u on u.id=r.author_id
                                                left outer join aimw_objects o on o.id=r.object_id
                                                left outer join aimw_category c on c.id=r.category_id

                   where r.id='.$id);
                  return($row);
          }

          function SwitchGrantedOnRate($rate_id)
          {
           $rate = MyWayDbManager::getRateByID($rate_id);
           if ($rate!=false)
           {
           eqr('update aimw_objectrate set isGranted=ABS(isGranted-1) where id='.$rate_id);

           MyWayDbManager::RecalcRating($rate_id);
           if ($rate->isGranted==1){ TLogMgr::add($rate_id, 'unapprove', 'objectrate');   return('off');}
           if ($rate->isGranted==0){ TLogMgr::add($rate_id, 'approve', 'objectrate');   return('on');}
           }

          }

          function SwitchDeletedOnRate($rate)
          {
           if ($rate!=false)
           {
           eqr('update aimw_objectrate set isDeleted=ABS(isDeleted-1) where id='.$rate->id);
           MyWayDbManager::RecalcRating($rate->id);

           if ($rate->isDeleted==1)
           {
           TLogMgr::add($rate->id, 'restore', 'objectrate');
            return('off');
           }
           if ($rate->isDeleted==0)
            {
                   TLogMgr::add($rate->id, 'delete', 'objectrate');
                    return('on');
            }
           }
          }

              function GetCommentsForRateCount($rateID)
                                {
                                        return $this->ExecuteValue("select count(ID) from aimw_comments where ratingID = $rateID and isActive = 1");
                                }

          function GetCommentsForRate($rateID,$page, $count,&$pageCount)
                {
                                        $cfg = Config::get_Instance();
                                        $commentCount = $this->GetCommentsForRateCount($rateID);
                                        if ($page<=0) {
                                                $page = 1;
                                        }

                                        $pageCount = intval($commentCount / $count);
                                        $from = ($page-1) * $count;
                    if ($commentCount % $count != 0){
                                                $pageCount = $pageCount + 1;
                                        }

                    if ($page>$pageCount){
                                                $page = 1;
                                                $from = 0;
                                        }

                                        $to = $page * $count;

                                        $cfg = Config::get_Instance();

                                        $q = sqr_list("
                                        select distinct
                                        c.id, c.text, c.create_dtm,
                                        u.Login as AuthorLogin,
                                        u.id as AuthorID
                                        from aimw_comments c
                                        inner join aim_users u on c.userID=u.id
                                        where c.ratingID = $rateID and c.isActive = 1
                                        order by c.create_dtm DESC limit $from,$count");


                                        $arr=array();
                                        while(($row=mysql_fetch_object($q))!=false)
                                        {
                                                $arr[]=$row;
                                        }

                                        return $arr;
                }


       function AddComment($comment)
        {
                        //$this->ClearCommentCacheForRate($comment->get_RateID());
                        //@unlink($cfg->mainPath.'/cache/comments_for_rate_'.$comment->get_RateID().'_page_1.php');

                         $res=$this->ExecuteNonQuery("insert into aimw_comments (UserID,ratingID,create_dtm,text) VALUES (".$comment->get_VisiterID().",".$comment->get_RateID().",'".date("Y-m-d H:i:s")."','".$comment->get_Text()."')");

                         $id=mysql_insert_id();
                        TLogMgr::add($id, 'add', 'comments');
                                                return $res;
        }

        function getCommentCntByRate($id)
        {
                $row=sqr_obj('select count(id) as cnt from aimw_comments where isActive=1 and ratingID='.$id);
                return($row->cnt);
        }

        function RecalcRating($rate_id)
        {
        $rate = MyWayDbManager::getRateByID($rate_id);

                if ($rate->street_id!='')
                eqr('
                update aimw_street
                set rating = (select sum(rating) from aimw_objectrate where street_id='.$rate->street_id.' and rating in (-1,1) and isDeleted=0 and isGranted=1)
                where id='.$rate->street_id.'
                ');

                if ($rate->object_id!='')
                eqr('
                update aimw_objects
                set rating = (select sum(rating) from aimw_objectrate where object_id='.$rate->object_id.' and rating in (-1,1) and isDeleted=0 and isGranted=1)
                where id='.$rate->object_id.'
                ');


        }

   function GetComment($id)
        {
                        $row = sqr_obj("select * from aimw_comments where id = ".$id);
                      return($row);
        }

      function KillComment($commentID)
        {
          TLogMgr::add($commentID, 'del', 'comments');
          return  $this->ExecuteNonQuery("update aimw_comments set isActive = 0 where id = $commentID");
        }

        function getTitle($val)
        {


         switch($val) {
                       case 'template/view_city_comment.html':
                                                     $id=intval($_REQUEST['city_id']);
                                                     $city = $this->getCityByID($id);
                                                     $ret='г.'.$city->name;
                                                     break;
                       case 'template/news_bystreet.html':
                                                     $id=intval($_REQUEST['street_id']);
                                                     $street = $this->getStreetByID($id);
                                                     $ret='г.'.$street->city_name.' / ул.'.$street->name;
                                                     break;
                       case 'template/news_bycategory.html':
                                                     if (isset($_REQUEST['street_id']))
                                                     {
                                                     $street_id=intval($_REQUEST['street_id']);
                                                     $cat_id=intval($_REQUEST['cat_id']);
                                                     $street = $this->getStreetByID($street_id);
                                                     $cat = $this->getCatByID($cat_id);

                                                     $ret='г.'.$street->city_name.' / ул.'.$street->name.' / '.$cat->name;
                                                     }
                                                     elseif (isset($_REQUEST['city_id']))
                                                     {
                                                     $city_id=intval($_REQUEST['city_id']);
                                                     $city = $this->getCityByID($city_id);
                                                     $cat_id=intval($_REQUEST['cat_id']);
                                                     $cat = $this->getCatByID($cat_id);
                                                     $ret='г.'.$city->name.' / '.$cat->name;
                                                     }
                                                     break;
                       case 'template/view_discus.html':
                                                     $rateid=intval($_REQUEST['rate_id']);
                                                     $id=intval($_REQUEST['street_id']);
                                                     $street = $this->getStreetByID($id);
                                                     $ret='г.'.$street->city_name.' / ул.'.$street->name.' / '.$rateid;
                                                     break;
                       case 'template/news_byobject.html':
                                                     $id=intval($_REQUEST['obj_id']);
                                                     $obj = $this->getObjectByID($id);
                                                     $ret='г.'.$obj->city_name.' / ул.'.$obj->street_name.' / '.$obj->cat_name.' :: '.$obj->name;
                                                     break;
                       case 'template/street_list.html':
                                                     $id=intval($_REQUEST['city_id']);
                                                     $city = $this->getCityByID($id);
                                                     $ret='г.'.$city->name.' / Список улиц';
                                                     break;

                }

                return($ret);
        }

}

?>