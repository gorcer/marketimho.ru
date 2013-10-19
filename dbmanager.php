<?

//echo 'Сайт перегружен запросами. Пожалуста попробуйте зайти позже.';
//die();
//ss
$incadd = "";
while (!file_exists($incadd."dbmanager.php")){
        $incadd = $incadd."../";
}


include_once($incadd."config/common.php");
include_once($incadd."dbaccessor.php");
include_once($incadd."functions.php");
include_once($incadd."numbers/numbers.php");
include_once($incadd."rates/rates.php");
include_once($incadd."comment/comment.php");
include_once($incadd."regions/regions.php");
include_once($incadd."users/user.php");
include_once ($incadd."lib/f.inc");
include_once ($incadd."lib/LogMgr.inc");

class DbManager extends DbAccessor
{
        var $connect = null;

        function DbManager()
        {
                        parent::DbAccessor();
        }

        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new DbManager();
                return $instance;
        }
        /* --> Added by Dmitry 23.12.2003 */

        function GetNumberInternal($row)
        {


                $number = new Number();

                $number->set_ID($row["id"]);
                $number->set_Number($row["regnum"]);
                $number->set_Region($row["region"]);
                $number->set_RegionID($row["regionID"]);
                $number->set_PhotoCnt($row["PhotoCnt"]);
                $number->set_DefPhotoFile($row["DefPhotoFile"]);
                $number->set_OwnerID($row["ownerID"]);
                $number->set_OwnerLogin($row["OwnerLogin"]);
                $number->set_isMentos($row["isMentos"]);

                return $number;
        }

        function GetNumber($id)
        {

                $result = $this->ExecuteList("

                select n.id,n.regnum,n.regionID,
                       r.name as region, count(p.id) as PhotoCnt,
                       dp.filename as DefPhotoFile ,
                       n.ownerID, ow.Login as OwnerLogin,
                       n.isMentos
                from aim_reg_num n
                inner join aim_dic_region r on r.id = n.regionID
                left outer join aim_photos p on p.num_id=n.id
                left outer join aim_photos dp on dp.id=n.mainphotoID
                left outer join aim_users ow on ow.id=n.ownerID
                where n.id = ".$id."
                group by
                 n.id,n.regnum,n.regionID,
                 r.name,dp.filename "
                );

                if($result != null)
                        return $this->GetNumberInternal(
                                $result->get_Current()
                                );
                else
                        return null;
        }


                function GetEquivalentNumbers($number)
                {
                        $cmd = "select n.* from aim_rates r inner join aim_reg_num n on n.id = r.regnum_id where n.regnum = '".$number->get_Number()."' and r.isActive=1 and r.isGranted=1 and n.id <> ".$number->get_ID();
                        return $this->GetObjectsList($this->ExecuteList($cmd));
                }

                function FindNumberList($number,$region)
        {

        $number = str_replace('*','%',$number);

                        $sqlNumber = "n.regnum LIKE '$number'";
                        if ($number==''){
                                $sqlNumber = "";
                        }
                        $sqlRegion = "n.regionID = $region";
                        if (!$region || $region==-1){
                                $sqlRegion = "";
                        } elseif ($sqlNumber != ""){
                                $sqlRegion = " and $sqlRegion";
                        }
                        if ($number == "" and !$region){
                                return array();
                        }
                        $cmd = "
                select n.id,n.regnum,n.regionID,
                       r.name as region, count(p.id) as PhotoCnt,
                       dp.filename as DefPhotoFile,
                       n.ownerID, ow.Login as OwnerLogin
                from aim_reg_num n
                inner join aim_rates rr on rr.regnum_id=n.id
                inner join aim_dic_region r on r.id = n.regionID
                left outer join aim_photos p on p.num_id=n.id
                left outer join aim_photos dp on dp.id=n.mainphotoID
                left outer join aim_users ow on ow.id=n.ownerID
                where $sqlNumber $sqlRegion
                 group by
                 n.id,n.regnum,n.regionID,
                 r.name,dp.filename
                 limit 50
                ";

                        return $this->GetNumbersListInternal(
                                $this->ExecuteList($cmd)
                        );


        }

                function GetNumberByRegNumber($number,$region)
        {
                $result = $this->ExecuteList("
                select n.id,n.regnum,n.regionID,
                       r.name as region, count(p.id) as PhotoCnt,
                       dp.filename as DefPhotoFile,
                       n.ownerID, ow.Login as OwnerLogin ,
                       n.isMentos
                from aim_reg_num n
                inner join aim_dic_region r on r.id = n.regionID
                left outer join aim_photos p on p.num_id=n.id
                left outer join aim_photos dp on dp.id=n.mainphotoID
                left outer join aim_users ow on ow.id=n.ownerID
                where n.regnum = '$number' and n.regionID = $region
                 group by
                 n.id,n.regnum,n.regionID,
                 r.name,dp.filename
                ");

                if($result != null)
                        return $this->GetNumberInternal(
                                $result->get_Current()
                                );
                else
                        return null;
        }

        function GetNumbersListInternal($rs)
        {
                $list = array();

                if($rs)
                {
                        while($rs->get_Current())
                        {
                                $num = $this->GetNumberInternal($rs->MoveNext());

                                $list[count($list)] = $num;
                        }
                }
                return $list;
        }

        function GetNumberList()
        {
                return $this->GetNumbersListInternal(
                        $this->ExecuteList("select *,r.name as region from aim_reg_num n inner join aim_dic_region r on r.id = n.regionID")
                        );
        }

                function GetNumbersForUser($id)
        {
                return $this->GetNumbersListInternal(
                        $this->ExecuteList("select *,r.name as region from aim_reg_num n inner join aim_dic_region r on r.id = n.regionID where OwnerID = $id")
                        );
        }

                function GetNumberRating($id)
        {
                $rating = $this->ExecuteValue("select sum(rating) from aim_rates where isActive = 1 and isGranted=1 and regnum_id = ".$id);
                                return $rating;
        }

                function GetNumberWithRating($id)
        {
                        $result = $this->ExecuteList("select id, create_dtm, rating , comment , filename , visiter_id, isActive , isGranted , regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted=1 and regnum_id = $id group by regnum_id");
                        if($result != null && $result->get_Length()>0)
                                return $this->GetRateInternal(
                                        $result->get_Current()
                                );
                        else
                                return null;
        }

                function GetRowNumberWithRating($id)
                {
                        $result = $this->ExecuteList("select regnum_id, sum(rating) as sum,num.regionID, num.regnum from aim_rates r inner join aim_reg_num num on num.id = r.regnum_id where r.isActive = 1 and r.isGranted=1 and r.regnum_id = $id group by regnum_id");
                        if($result != null && $result->get_Length()>0){
                                return $result->get_Current();
                        }
                        else
                                return null;
                }

                function GetRowsNumberPosition($id)
        {
                        $cfg = Config::get_Instance();
            if (is_cached ('number_position_'.$id.'.php', 900)) {
                                $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/number_position_'.$id.'.php'));
                                return $res;
                        }

                        $list = array();
                        $current = $this->GetRowNumberWithRating($id);
                        $sumRate = null;
                        if ($current!=null){
                                $sumRate = intval($current["sum"]);
                                $query = "select r.regnum_id, r.sum, n.regionID, n.regnum from (select  regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id having sum>$sumRate order by sum asc limit 5) r inner join aim_reg_num n on n.id=r.regnum_id order by r.sum desc";
                                $rs = $this->ExecuteList($query);
                                $list = $this->GetObjectsList($rs);
                                $list[count($list)] = $current;
                                $query = "select r.regnum_id, r.sum, n.regionID, n.regnum from (select regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id having sum<$sumRate order by sum asc limit 5) r inner join aim_reg_num n on n.id=r.regnum_id order by r.sum desc";
                                $rs = $this->ExecuteList($query);
                                $list = $this->GetObjectsList($rs,$list);
                        }
                        if (count($list)>0)
                                cache_file ('number_position_'.$id.'.php', serialize($list));
            return $list;
                }


                function GetNumberPosition($id)
        {
                        $cfg = Config::get_Instance();
            if (is_cached ('number_position_'.$id.'.php', 900)) {
                                $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/number_position_'.$id.'.php'));
                                return $res;
                        }

                        $list = array();
                        $current = $this->GetNumberWithRating($id);
                        $sumRate = null;
                        if ($current!=null){
                                $sumRate = intval($current->get_Sum());
                        }
                        if  ($sumRate != null){
                                //$query = "select r.id, r.create_dtm, r.rating , r.comment , r.filename , r.visiter_id, r.isActive , r.isGranted , r.regnum_id, r.sum from (select  id, create_dtm, rating , comment , filename , visiter_id, isActive , isGranted , regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id) r where r.sum>=$sumRate order by sum desc limit 5";
                                $query = "select r.regnum_id, r.sum from (select  regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id having sum(rating)>$sumRate order by sum(rating) asc limit 5) r order by sum desc";
                                                                $rs = $this->ExecuteList($query);
                                $length = $rs->get_Length();
                                if($rs)        {
                                        while($rs->get_Current())
                                        {
                                                $num = $this->GetRateInternal($rs->MoveNext());
                                                $list[count($list)] = $num;
                                        }
                                }
                                //$current = $this->GetNumberWithRating($id);
                                if ($current != null)
                                $list[count($list)] = $current;
                                //$query = "select r.id, r.create_dtm, r.rating , r.comment , r.filename , r.visiter_id, r.isActive , r.isGranted , r.regnum_id, r.sum as sum from (select id, create_dtm, rating , comment , filename , visiter_id, isActive , isGranted ,regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id) r where r.sum<$sumRate order by sum desc limit 5";
                                $query = "select r.regnum_id, r.sum as sum from (select regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 and regnum_id <>$id group by regnum_id having sum<$sumRate order by sum asc limit 5) r order by r.sum desc";
                                                                $rs = $this->ExecuteList($query);
                                if($rs){
                                        while($rs->get_Current()){
                                                $num = $this->GetRateInternal($rs->MoveNext());
                                                $list[count($list)] = $num;
                                        }
                                }
                        }
                        cache_file ('number_position_'.$id.'.php', serialize($list));
                        return $list;
        }


                function NumberCreateAndGetByRegNumber($numberStr,$regionID)
                {
                        $number = $this->GetNumberByRegNumber($numberStr,$regionID);
                        if ($number==null || !$number->get_ID()){
                                $number = new Number();
                                $number->set_RegionID($regionID);
                                $number->set_Number($numberStr);
                                $id = $this->AddNumber($number);
                                $number = $this->GetNumber($id);
                        }
                        return $number;
                }

        function AddNumber($number)
                {
                        $isMentos = 0;
                        if (isValidMentosNumber($number->get_Number())||(isValidVladGaiNumber($number))){
                                $isMentos = 1;
                        }

                        $this->ExecuteNonQuery("insert into aim_reg_num (regnum, regionID,create_dtm,isMentos) values ('".$number->get_Number()."', ".intval($number->get_RegionID()).",'".date("Y-m-d H:i:s")."',".$isMentos.")");
                        return mysql_insert_id();
        }


                /*
                Regions
                */
                function IsRegionExists($region)
                {
                        $region = $this->ExecuteValue("select id from aim_dic_region where id = ".$region);
                        if ($region){
                                return true;
                        }
                        return false;
                }

                function GetRegionInternal($row)
        {
                $region = new Region();
                $region->set_ID($row["id"]);
                $region->set_Name($row["name"]);
                                $region->set_NumberCount($row["numberCount"]);
                                $region->set_ParentID($row["parent_id"]);
                return $region;
        }

        function GetRegion($id)
        {
                $result = $this->ExecuteList("select *, 0 as numberCount from aim_dic_region where id = ".$id);

                if($result != null)
                        return $this->GetRegionInternal(
                                $result->get_Current()
                                );
                else
                        return null;
        }

        function GetRegionsListInternal($rs)
        {
                $list = array();

                if($rs)
                {
                        while($rs->get_Current())
                        {
                                $num = $this->GetRegionInternal($rs->MoveNext());

                                $list[count($list)] = $num;
                        }
                }
                return $list;
        }

                function GetDicRegionList()
                {
                        return $this->GetRegionsListInternal(
                        $this->ExecuteList('select id, name from aim_dic_region where id=parent_id and CountryID=7 order by name')
                        );

                }

        function GetRegionList()
        {
                return $this->GetRegionsListInternal(
                        $this->ExecuteList("
                        select d.*,count(rn.regionID) as numberCount
                        from aim_dic_region d
                        inner join aim_reg_num rn on d.id = rn.regionID
                        where
                        d.CountryID=7
                        group by d.name
                        order by rn.regionID")
                        );
        }

        function AddRegion($region)
                {
            $this->ExecuteNonQuery("insert into aim_dic_region (id, name) values (".intval($region->get_ID()).", '".$region->get_Name()."') ");
        }

                /*
                Rates
                */



                function GetRateInternal($row)
        {
                        $rate = new Rate();

                        $rate->type = $row['type'];
                        $rate->set_ID($row["id"]);

                        if ($row["regnum_id"]!='')
                        {
                        $rate->set_NumberID($row["regnum_id"]);

                        $rate->set_Number($this->GetNumber($row["regnum_id"]));

                        $rate->set_CreateDateTime($row["create_dtm"]);
                        $rate->set_Rate($row["rating"]);
                        $rate->set_Description($row["comment"]);
                        $rate->set_Filename($row["filename"]);
                        $rate->set_VisiterID($row["visiter_id"]);
                        $rate->set_IsActive($row["isActive"]);
                        $rate->set_IsGranted($row["isGranted"]);
                        $rate->set_Coords($row["coords"]);
                        $rate->set_Coords_type($row["coords_type"]);
                                                $rate->set_Coords_zoom($row["coords_zoom"]);


                                                if (isset($row["sum"])){
                                                        $rate->set_Sum($row["sum"]);
                                                } else
                                                        $rate->set_Sum($this->GetNumberRating($row["regnum_id"]));
                       }
                        return $rate;
        }

        function GetRate($id)
        {
                $result = $this->ExecuteList("select * from aim_rates where id = ".$id);

                if($result != null)
                        return $this->GetRateInternal(
                                $result->get_Current()
                                );
                else
                        return null;
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

                function GetCountRatesByNumberID($id)
                {
                        return $this->ExecuteValue("select count(id) from aim_rates where regnum_id = $id and isActive = 1");
                }

                function GetCommentsCountForRate($id)
                {
                        return $this->ExecuteValue("select count(id) from aim_rates where regnum_ID = $id and isActive = 1");
                }

                function GetRatesCount($region=-1, $val=0)
                {
                    $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;

                        if ($region==-1){
                                return $this->ExecuteValue("select count(id) from aim_rates");
                        } else {
                                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aim_rates r
                                        inner join aim_reg_num n on n.id=r.regnum_id
                                        inner join aim_dic_region g on g.id=n.regionID
                                        where g.parent_id=".$region
                                );
                        }
        }

                function GetRatesPerPage(&$pageCount, $page = 1, $region=-1, $perPage = 10, $val=0)
                {
                        $commentCount = $this->GetRatesCount($region, $val);
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
                        return $this->GetRateList($region,$from,$to-$from, $val);
                        /*
                        $commentList = $this->GetRateList($region);
                        $count = $perPage;
                        $from = ($page-1) * $count;
                        $pageCount = intval((count($commentList) / $count));
                        if (count($commentList) % $count != 0){
                                $pageCount = $pageCount + 1;
                        }
                        $to = $page * $count;
                        $i = 0;
                        $isPaged = false;
                        $res = array();
                        foreach ($commentList as $key => $comment)
                        {
                $i=$i+1;
                if (($i<=$from)||($i>$to)){
                                        if ($isPaged){
                                                break;
                                        }
                                        continue;
                                }
                                $res[count($res)]=$comment;
                                $isPaged = true;

                        }
                        return $res;
                        */
                }

                /// Получение списка всех комментариев
        function GetRateList($region=-1,$lineFrom=0,$lineTo=0, $val=0)
        {
                        $lim = "";
                        if ($lineTo>0){
                                $lim = " LIMIT $lineFrom,$lineTo";
                        }
                        if ($region==-1)
                        {
                                    $valq='';
                        if ($val!==0) $valq='where rating='.$val;

                return $this->GetRatesListInternal(
                                        $this->ExecuteList("
                                           select tt.*
                                                from
                                                (
                                                SELECT
                                                'aim' as type, r.id, r.regnum_id, r.visiter_id, r.create_dtm, r.rating, r.comment, r.isActive, r.isGranted, r.filename, r.ip, r.coords, r.coords_type, r.coords_zoom
                                                FROM aim_rates r
                                                inner JOIN aim_reg_num n ON n.id = r.regnum_id
                                                inner JOIN aim_dic_region g ON g.id = n.regionID

                                                union all

                                                select
                                                'aimw' as type, r.id,null, null, r.to_aim as create_dtm, null, null,null,null,null,null,null,null,null
                                                from aimw_objectrate r
                                                where
                                                r.to_aim is not null
                                                ) as tt
                                                ORDER BY tt.create_dtm DESC
                                                $lim
                                        ")
                );
                }
                        else
                        {

                            $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;

                return $this->GetRatesListInternal(

                                $this->ExecuteList("
                  select tt.*
                                                from
                                                (
                                                SELECT
                                                'aim' as type, r.id, r.regnum_id, r.visiter_id, r.create_dtm, r.rating, r.comment, r.isActive, r.isGranted, r.filename, r.ip, r.coords, r.coords_type, r.coords_zoom
                                                FROM aim_rates r
                                                inner JOIN aim_reg_num n ON n.id = r.regnum_id
                                                inner JOIN aim_dic_region g ON g.id = n.regionID
                                                WHERE
                                                (
                                                r.isActive =1
                                                AND r.isGranted =1
                                                AND n.isMentos =0
                                                AND g.parent_id =".$region."
                                                ".$valq."
                                                )

                                                union all

                                                select
                                                'aimw' as type, r.id,null, null, r.to_aim, null, null,null,null,null,null,null,null,null
                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                where
                                                r.to_aim is not null
                                                and ci.regionID=".$region."
                                                ) as tt

                                                ORDER BY tt.create_dtm DESC


                                                $lim")
                );
                }
                }

        function AddRate($rate)
                {
            $this->ExecuteNonQuery("
            insert into aim_rates (coords_type,coords_zoom,coords, ip, regnum_id, rating, create_dtm, comment, visiter_id,filename)
             values ('".$rate->get_Coords_type()."','".$rate->get_Coords_zoom()."','".$rate->get_Coords()."','".$_SERVER['REMOTE_ADDR']."',".intval($rate->get_NumberID()).",".intval($rate->get_Rate()).",'".date("Y-m-d H:i:s")."','".$rate->get_Description()."',".$rate->get_VisiterID().",'".$rate->get_Filename()."')");
                        $id = mysql_insert_id();
                        $this->ClearRatesCacheForNumber(intval($rate->get_NumberID()));

                        TLogMgr::Add($id, 'add', 'aim_rates');

                        return $id;
        }

                function ClearRatesCacheForNumber($numberID){
                        if ($numberID>0){
                                clear_cache_by_mask('all_rates_for_number_'.$numberID);
                        }
                        return true;
                }

                function GetRegnumIDByRateID($rateID)
                {
                        return intval($this->ExecuteValue("SELECT regnum_id FROM aim_rates WHERE id = ".$rateID));
                }

                function UpdateRate($rate)
        {
                        $this->ExecuteNonQuery("update aim_rates set coords_type='".$rate->get_Coords_type()."',coords_zoom='".$rate->get_Coords_zoom()."',coords='".$rate->get_Coords()."', rating = ".$rate->get_Rate().", comment = '".$rate->get_Description()."', filename = '".$rate->get_Filename()."' where id = ".$rate->get_ID());
                        $this->ClearRatesCacheForNumber(intval($rate->get_NumberID()));
                        TLogMgr::Add($rate->id, 'upd', 'aim_rates');
        }

                function SetRateActive($id,$active)
                {
            $numberID = $this->GetRegnumIDByRateID($id);
                        $this->ExecuteNonQuery("update aim_rates set isActive = $active where id = $id");
                        $this->ClearRatesCacheForNumber($numberID);
        }

                function SetRateGrant($id,$active)
        {
                        $numberID = $this->GetRegnumIDByRateID($id);
                        $this->ExecuteNonQuery("update aim_rates set isGranted = $active where id = $id");
                        $this->ClearRatesCacheForNumber($numberID);
        }


                function GetGrantedRatesCount($region=-1, $val=0)
                {


                        $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;

                    if ($region==-1){
                                return $this->ExecuteValue("select count(r.id) from aim_rates r inner join aim_reg_num n on n.id = r.regnum_id where r.isActive = 1 and r.isGranted = 1 and n.isMentos=0 ".$valq);
                        } else {
                                return $this->ExecuteValue(
                                        "select count(r.id)
                                        from aim_rates r
                                        inner join aim_reg_num n on n.id=r.regnum_id
                                        inner join aim_dic_region g on g.id=n.regionID
                                        where r.isActive = 1 and r.isGranted = 1 and n.isMentos=0
                                        ".$valq."
                                        and g.parent_id=".$region
                                );
                        }
        }

             function GetGrantedRatesCount_byUserComments($uid,$region=-1, $val=0)
                {

                        $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;




                    if ($region==-1){
                                return $this->ExecuteValue("
                                select count(distinct r.id)
                                from aim_rates r
                                inner join aim_reg_num n on n.id = r.regnum_id
                                inner join aim_comments c on c.RatingID=r.id and c.visiterID=".$uid."
                                where r.isActive = 1 and r.isGranted = 1 and n.isMentos=0
                                ".$valq);
                        } else {
                                return $this->ExecuteValue(
                                        "select count(distinct r.id)
                                        from aim_rates r
                                        inner join aim_reg_num n on n.id=r.regnum_id
                                        inner join aim_dic_region g on g.id=n.regionID
                                        left outer aim_comments c on c.RatingID=r.id and c.visiterID=".$uid."
                                        where r.isActive = 1 and r.isGranted = 1 and n.isMentos=0
                                        ".$valq."
                                        and g.parent_id=".$region
                                );
                        }
        }


                function GetGrantedRatesPerPage_byUserComments($uid, &$pageCount, $page = 1, $region=-1, $perPage = 10, $val=0)
                {
                        $commentCount = $this->GetGrantedRatesCount_byUserComments($uid,$region, $val);
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
                        return $this->GetAllGrantedRates_byUserComments($uid,$region,$from,$to-$from, $val);
                }



                /// Получение списка проверенных комментариев
                function GetGrantedRatesPerPage(&$pageCount, $page = 1, $region=-1, $perPage = 10, $val=0)
                {
                                                $commentCount = $this->GetGrantedRatesCount($region, $val);
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
                        return $this->GetAllGrantedRates($region,$from,$to-$from, $val);


                }


                function GetAllGrantedRates_byUserComments($uid, $region=-1,$lineFrom=0,$lineTo=0, $val=0)
                {

                        $cfg = Config::get_Instance();

                        $lim = "";


                        $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;



                        if ($lineTo>0){
                                $lim = " LIMIT $lineFrom,$lineTo";
                        }



                        if ($region==-1){
                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select distinct r.* from aim_rates r
                                                inner join aim_reg_num n on n.id = r.regnum_id
                                                 inner join aim_comments c on c.RatingID=r.id
                                                where c.visiterID=".$uid." and r.isActive = 1 and r.isGranted = 1 and n.isMentos = 0 ".$valq." order by r.id DESC $lim")
                                        );

                                return $res;
                        } else {
                                                                $res = $this->GetRatesListInternal(
                                        $this->ExecuteList("
                                                select distinct r.*
                                                from aim_rates r
                                                inner join aim_reg_num n on n.id=r.regnum_id
                                                inner join aim_dic_region g on g.id=n.regionID
                                                 inner join aim_comments c on c.RatingID=r.id
                                                where c.visiterID=".$uid." and r.isActive = 1 and r.isGranted = 1 and n.isMentos = 0
                                                ".$valq."
                                                and g.parent_id=".$region."
                                                order by id DESC $lim")
                                );



                                return $res;
                        }
        }

                function GetAllGrantedRates($region=-1,$lineFrom=0,$lineTo=0, $val=0)
        {
//        echo $region;


                        $cfg = Config::get_Instance();

                        $lim = "";

                        $valq='';
                        if ($val!==0) $valq='and r.rating='.$val;



                        if ($lineTo>0){
                                $lim = " LIMIT $lineFrom,$lineTo";
                        }



                        if ($region==-1){
                                $res = $this->GetRatesListInternal(
                                                $this->ExecuteList("
                                                select tt.*
                                                from
                                                (
                                                SELECT
                                                'aim' as type, r.id, r.regnum_id, r.visiter_id, r.create_dtm, r.rating, r.comment, r.isActive, r.isGranted, r.filename, r.ip, r.coords, r.coords_type, r.coords_zoom
                                                FROM aim_rates r
                                                inner JOIN aim_reg_num n ON n.id = r.regnum_id
                                                inner JOIN aim_dic_region g ON g.id = n.regionID
                                                WHERE
                                                (
                                                r.isActive =1
                                                AND r.isGranted =1
                                                AND n.isMentos =0
                                                ".$valq."
                                                )


                                                union all

                                                select
                                                'aimw' as type, r.id,null, null, r.to_aim, null, null,null,null,null,null,null,null,null
                                                from aimw_objectrate r
                                                where
                                                r.to_aim is not null
                                                ) as tt
                                                ORDER BY tt.create_dtm DESC
                                                $lim
                                                ")
                                        );

                                return $res;
                        } else {
                                                                $res = $this->GetRatesListInternal(
                                        $this->ExecuteList("
                                                select tt.*
                                                from
                                                (
                                                SELECT
                                                'aim' as type, r.id, r.regnum_id, r.visiter_id, r.create_dtm, r.rating, r.comment, r.isActive, r.isGranted, r.filename, r.ip, r.coords, r.coords_type, r.coords_zoom
                                                FROM aim_rates r
                                                inner JOIN aim_reg_num n ON n.id = r.regnum_id
                                                inner JOIN aim_dic_region g ON g.id = n.regionID
                                                WHERE
                                                (
                                                r.isActive =1
                                                AND r.isGranted =1
                                                AND n.isMentos =0
                                                AND g.parent_id =".$region."
                                                ".$valq."
                                                )

                                                union all

                                                select
                                                'aimw' as type, r.id,null, null, r.to_aim, null, null,null,null,null,null,null,null,null
                                                from aimw_objectrate r
                                                inner join aimw_street s on s.id = r.street_id
                                                inner join aimw_city ci on ci.id=s.city_id
                                                where
                                                r.to_aim is not null
                                                and ci.regionID=".$region."
                                                ) as tt

                                                ORDER BY tt.create_dtm DESC


                                                $lim")
                                );


                                return $res;
                        }
        }

                function GetRatesForUser($id)
                {
                                        return $this->GetRatesListInternal(
                                $this->ExecuteList("select r.* from aim_rates r inner join aim_reg_num n on n.id = r.regnum_id where r.visiter_id = $id and r.isActive = 1 and n.isMentos = 0 order by id DESC")
                                        );
                }

                function GetAllRatesForUser($id)
                {
            return $this->GetRatesListInternal(
                                $this->ExecuteList("select * from aim_rates where visiter_id = $id order by id DESC")
            );
                }

                function GetRatesCountForUser($id)
        {
                        $result = $this->ExecuteValue("select count(id) from aim_rates where visiter_id = $id");
                        return $result;
        }


                function GetNumberRates($id)
                {
                                        $filename = "all_rates_for_number_$id.php";
                                        $cfg = Config::get_Instance();
                                        if (is_cached ($filename, 9000)){
                                                return unserialize(file_get_contents ($cfg->mainPath.'/cache/'.$filename));
                                        }
                                        $list = $this->GetRatesListInternal(
                                                $this->ExecuteList("select r.* from aim_rates r inner join aim_reg_num n on n.id = r.regnum_id where r.isActive = 1 and r.regnum_id = $id order by r.id desc")
                                        );
                                        if (count($list)>0)
                                                cache_file ($filename, serialize($list));
                                        return $list;
                }

                function GetNumberRatesForUser($id, $user)
                      {
                      $uid = -1;
                      if ($user)
                      $uid=$user->get_ID();

                        return $this->GetRatesListInternal(
                $this->ExecuteList("select r.* from aim_rates r inner join aim_reg_num n on n.id = r.regnum_id where r.isActive = 1 and r.regnum_id = $id and r.visiter_id=".$uid." order by r.id desc")
            );
                }

                function getTopUsers($limit=10, $region=-1)
                {
                                        if ($region==-1){
                                                $cmd = "

                                                select u.login as name, count(c.id) as numberCount
                             from aim_users u
                             inner join aim_rates c on c.visiter_id=u.id and c.isActive=1 and c.isGranted=1
                             where
                             u.isActive=1 and u.isBlocked=0 and u.id <> ".intval(Config::get_Instance()->sms_user_id)."
                             group by u.id
                             order by numberCount desc
                             LIMIT ".$limit;
                                        }
                                        else {
                                                $cmd = "

                                                select u.login as name, count(c.id) as numberCount
                             from aim_users u
                             inner join aim_rates c on c.visiter_id=u.id and c.isActive=1 and c.isGranted=1
                             where u.regionID=".$region."
                             and
                             u.isActive=1 and u.isBlocked=0 and u.id <> ".intval(Config::get_Instance()->sms_user_id)."
                             group by u.id
                             order by numberCount desc
                             LIMIT ".$limit;
                                        }

                                        $filename="top_users_region_$region.php";
                                        $cfg = Config::get_Instance();
                                        if (is_cached ($filename, 900)) $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/'.$filename));
                                        else {
                        $res = $this->GetRegionsListInternal($this->ExecuteList($cmd));
                                                cache_file ($filename, serialize($res));
                                        }


                                        return $res;

                }

                                function GetTopRatesRows($top,$sign, $region=-1)
                                {
                                        $order = " ASC";
                                        $compare = "<";
                                        if($sign>0)
                                        {
                                                $order = " DESC";
                                                $compare = ">";
                                        }

                                        if ($region==-1)
                                                $query = "
                        select regnum_id, sum(rating) as sum, n.regnum, n.regionID
                         from aim_rates r inner join aim_reg_num n on r.regnum_id = n.id
                         where
                         isActive = 1 and isGranted = 1
                         group by regnum_id having sum $compare 0
                         order by sum $order limit $top";
                        else
                        $query = "
                        select ra.regnum_id, sum(ra.rating) as sum, n.regnum, n.regionID
                         from aim_rates ra
                         inner join aim_reg_num n on n.id=ra.regnum_id
                         inner join aim_dic_region g on g.id=n.regionID
                         where
                         ra.isActive = 1 and ra.isGranted = 1
                         and g.parent_id=".$region."
                         group by ra.regnum_id
                         having sum $compare 0
                         order by sum $order limit $top";

                        $cfg = Config::get_Instance();

                   //     if (is_cached ('top_numbers_rows_'.$sign.'_'.$region.'.php', 900)) $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/top_numbers_rows_'.$sign.'_'.$region.'.php'));
                   //             else {
                                                                        $rs = $this->ExecuteList($query);
                                                                        $res = $this->GetObjectsList($rs);
                                                                       // cache_file ('top_numbers_rows_'.$sign.'_'.$region.'.php', serialize($res));
                               // }


                        return $res;
                                }

                function GetTopRates($top,$sign, $region=-1)
                {
                        $order = " ASC";
                        $compare = "<";
                        if($sign>0)
                        {
                                $order = " DESC";
                                $compare = ">";
                        }

                        //$query = "select r.regnum_id, r.sum from (select regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 group by regnum_id order by sum $order) r limit $top";

                        if ($region==-1)
                        $query = "
                        select r.regnum_id, r.sum
                        from
                        (select regnum_id, sum(rating) as sum
                         from aim_rates
                         where
                         isActive = 1 and isGranted = 1
                         group by regnum_id having sum $compare 0
                         order by sum $order) r
                        limit $top";
                        else
                        $query = "
                        select r.regnum_id, r.sum
                        from
                        (select ra.regnum_id, sum(ra.rating) as sum
                         from aim_rates ra
                         inner join aim_reg_num n on n.id=ra.regnum_id
                         inner join aim_dic_region g on g.id=n.regionID
                         where
                         ra.isActive = 1 and ra.isGranted = 1
                         and g.parent_id=".$region."
                         group by ra.regnum_id
                         having sum $compare 0
                         order by sum $order) r
                        limit $top";

                        $cfg = Config::get_Instance();

                        if (is_cached ('top_numbers_'.$sign.'_'.$region.'.php', 900)) $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/top_numbers_'.$sign.'_'.$region.'.php'));
                                else {
                                 $res = $this->GetRatesListInternal($this->ExecuteList($query));
                                 cache_file ('top_numbers_'.$sign.'_'.$region.'.php', serialize($res));
                                }


                        return $res;
                }


                function GetTopRatesCnt($sign, $region=-1)
                {
                        $order = " ASC";
                        $compare = "<";
                        if($sign>0)
                        {
                                $order = " DESC";
                                $compare = ">";
                        }

                        //$query = "select r.regnum_id, r.sum from (select regnum_id, sum(rating) as sum from aim_rates where isActive = 1 and isGranted = 1 group by regnum_id order by sum $order) r limit $top";

                        if ($region==-1)
                        $query = "
                          select count(r.regnum_id) as cnt
                        from
                        (select regnum_id, sum(rating) as sum
                         from aim_rates
                         where
                         isActive = 1 and isGranted = 1
                         group by regnum_id having sum $compare 0
                         ) r
                         ";
                        else
                        $query = "
                         select count(r.regnum_id) as cnt
                        from
                        (select ra.regnum_id, sum(ra.rating) as sum
                         from aim_rates ra
                         inner join aim_reg_num n on n.id=ra.regnum_id
                         inner join aim_dic_region g on g.id=n.regionID
                         where
                         ra.isActive = 1 and ra.isGranted = 1
                         and g.parent_id=".$region."
                         group by ra.regnum_id
                         having sum $compare 0
                         ) r
                         ";

                        $cfg = Config::get_Instance();

                        if (is_cached ('top_numbers_cnt_'.$sign.'_'.$region.'.php', 900)) $res = file_get_contents ($cfg->mainPath.'/cache/top_numbers_cnt_'.$sign.'_'.$region.'.php');
                                else {
                                 $res = sqr($query);
                                 $res=$res['cnt'];
                                 cache_file ('top_numbers_cnt_'.$sign.'_'.$region.'.php', $res);
                                }


                        return $res;
                }
                /*
                        Users
                */
                function IsUserExists($user)
                {
                        $user = $this->ExecuteValue("select id from aim_users where login = '".$user->get_Login()."' or email = '".$user->get_Email()."' limit 1");
                        if ($user){
                                return true;
                        }
                        return false;
                }

                function ActivateUser($login,$conf)
                {
                        $user = $this->GetUserByGUID($conf);
                        if ($user!=null&&$user->get_IsBlocked()==0) {
                                $user->set_IsActive(1);
                                $this->UpdateUser($user);
                                return true;
                        }
                        return false;
                }


                function GetUserByGUID($guid)
                {
            $result = $this->ExecuteList("select * from aim_users where guid = '$guid'");

                        if($result != null)
                return $this->GetUserInternal(
                                        $result->get_Current()
                                );
                else
                                        return null;
        }

                function GetUserByLoginPass($login,$pass)
                {
                        $result = $this->ExecuteList("select * from aim_users where login = '$login' and pass='$pass' and isActive=1");
                        if($result != null)
                                return $this->GetUserInternal(
                                $result->get_Current()
                                        );
                        else
                        return null;
                }

                function GetUserByLogin($login)
                {
                        $result = $this->ExecuteList("select * from aim_users where login = '$login'");
                        if($result != null)
                                return $this->GetUserInternal(
                                $result->get_Current()
                                        );
                        else
                        return null;
                }

                                function GetUserByEmail($email)
                {
                        $result = $this->ExecuteList("select * from aim_users where email = '$email'");
                        if($result != null)
                                return $this->GetUserInternal(
                                $result->get_Current()
                                        );
                        else
                        return null;
                }

                function GetUserInternal($row)
        {

            $user = new User();

                        $user->set_ID($row["id"]);

                        $user->set_GUID($row["guid"]);
                        $user->set_Name($row["name"]);
                        $user->set_Login($row["login"]);
                        $user->set_Pass($row["pass"]);
                        $user->set_IsActive($row["isActive"]);
                        $user->set_IsEditor($row["isEditor"]);
                        $user->set_IsAdmin($row["isAdmin"]);
                        $user->set_IsBlocked($row["isBlocked"]);
                                                $user->set_Email($row["email"]);
                                                $user->set_Filename($row["filename"]);
                                                $user->set_Phone($row["phone"]);
                                                $user->set_SendNews($row["sendNews"]);
                                                $user->set_City($row["city"]);
                                                $user->set_Sex($row["sex"]);
                                                $user->set_BirthDate($row["birthDate"]);
                                                $user->set_RegionID($row["regionID"]);
                                                if (intval($row["regionID"])>0){
                                                        $region = $this->GetRegion($row["regionID"]);
                                                        if ($region != null) {
                                                                $user->set_Region($region->get_Name());
                                                        }
                                                }
                        return $user;
        }


        function GetUserCnt($region)
        {
                        $cfg = Config::get_Instance();
                        if (is_cached ('user_cnt_'.$region.'.php', 900)) $res = file_get_contents ($cfg->mainPath.'/cache/user_cnt_'.$region.'.php');
                                else {

                                 if ($region==-1)
                                 $row=sqr("select count(distinct u.id) as cnt
                                           from aim_users u
                                           where u.isActive = 1");
                                 else
                                 $row=sqr("select count(distinct u.id) as cnt
                                           from aim_users u
                                           where u.isActive = 1 and u.regionID=".$region);

                                 $res=$row['cnt'];
                                 cache_file ('user_cnt_'.$region.'.php', $res);
                                }
                return($res);
        }

        function GetUser($id)
        {
                $result = $this->ExecuteList("select * from aim_users where id = ".$id);

                if($result != null)
                        return $this->GetUserInternal(
                                $result->get_Current()
                                );
                else
                        return null;
        }

        function GetUsersListInternal($rs)
        {
                        $list = array();
                        if($rs)
                        {
                                while($rs->get_Current())
                                {
                                        $num = $this->GetUserInternal($rs->MoveNext());

                                        $list[count($list)] = $num;
                                }
                        }
                        return $list;
        }


        function GetUserList_notActive()
        {
                        return $this->GetUsersListInternal(
                                $this->ExecuteList("select * from aim_users
                                                    where isActive=0
                                                    and
                                                    ADDDATE(create_dtm, 10)>Now()

                                                    ")
                        );
        }


        function GetUserList()
        {
                        return $this->GetUsersListInternal(
                                $this->ExecuteList("select * from aim_users")
                        );
        }

        function AddUser($user)
                {
                        $conf = uniqid();
            $this->ExecuteNonQuery("insert into aim_users (create_dtm, name, login,pass,guid,sendNews,phone,email,city,filename,sex,birthDate,regionID) values ('".date("Y-m-d H:i:s")."', '".$user->get_Name()."', '".$user->get_Login()."', '".$user->get_Pass()."','".$conf."','".$user->get_SendNews()."','".$user->get_Phone()."','".$user->get_Email()."','".$user->get_City()."','".$user->get_Filename()."','".$user->get_Sex()."','".$user->get_BirthDate()."',".$user->get_RegionID().") ");
                        return $conf;
        }

        function UpdateUser($user)
        {

                $fn='';
                if ($user->get_Filename()!='')
                $fn="aim_".$user->get_Filename();

            $this->ExecuteNonQuery("update aim_users set name = '".$user->get_Name()."',login = '".$user->get_Login()."',pass = '".$user->get_Pass()."',isAdmin = ".$user->get_IsAdmin().",isEditor = ".$user->get_IsEditor().",isActive = ".$user->get_IsActive().",city = '".$user->get_City()."',filename = '".$fn."',sendNews = ".$user->get_SendNews().",phone = '".$user->get_Phone()."',email = '".$user->get_Email()."',sex = '".$user->get_Sex()."',birthDate = '".$user->get_BirthDate()."',regionID=".$user->get_RegionID()." where id=".intval($user->get_ID()));
        }

                /*
                Comments
                */

                function GetCommentInternal($row)
        {
            $c = new Comment();
                        $c->set_ID($row["id"]);
                        $c->set_Text($row["text"]);
                        $c->set_CreateDateTime($row["create_dtm"]);
                        $c->set_VisiterID($row["visiterID"]);
                        $c->set_IsActive($row["isActive"]);
                        $c->set_RateID(intval($row["ratingID"]));
                        $c->set_Filename($row["filename"]);

                        return $c;
        }

                function GetComment($id)
        {
                        $result = $this->ExecuteList("select * from aim_comments where id = ".$id);

                        if($result != null)
                                return $this->GetCommentInternal(
                                        $result->get_Current()
                                );
                        else
                                return null;
        }

                function GetCommentsListInternal($rs)
        {
                        $list = array();
                        if($rs)
                        {
                                while($rs->get_Current())
                                {
                                        $num = $this->GetCommentInternal($rs->MoveNext());
                                        $list[count($list)] = $num;
                                }
                        }
                        return $list;
        }

                function GetCommentsByParent($parentID)
                {
                        return $this->GetCommentsListInternal(
                                $this->ExecuteList("select * from aim_comments where parentID = $parentID")
                        );
                }


                                function GetCommentsForRateCount($rateID)
                                {
                                        return $this->ExecuteValue("select count(ID) from aim_comments where ratingID = $rateID and isActive = 1");
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

                                        if (is_cached ('comments_for_rate_'.$rateID.'_page_'.$page.'.php', 900)) $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/comments_for_rate_'.$rateID.'_page_'.$page.'.php'));
                                        else {
                                                $res = $this->GetCommentsListInternal($this->ExecuteList("select * from aim_comments where ratingID = $rateID and isActive = 1 order by create_dtm DESC limit $from,$count"));
                                                cache_file ('comments_for_rate_'.$rateID.'_page_'.$page.'.php', serialize($res));
                                        }
                                        return $res;
                }


                                function GetAllCommentsCount()
                                {
                                        return $this->ExecuteValue("select count(ID) from aim_comments");
                                }

                                function GetAllComments($page,$count,&$pageCount)
                {
                                        $commentCount = $this->GetAllCommentsCount();
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
                    return $this->GetCommentsListInternal(
                                                $this->ExecuteList("select * from aim_comments order by create_dtm DESC limit $from,$count")
                                        );
                }

                                function GetObjectsCount($cmd)
                                {
                                        return $this->ExecuteValue($cmd);
                                }


                                function GetCommentsForRegion($page,$count,&$pageCount,$region)
                {
                                        $commentCount = $this->GetObjectsCount("select count(c.id) from aim_comments c inner join aim_rates r on r.id = c.ratingID inner join aim_reg_num num on r.regnum_id = num.id inner join aim_dic_region reg on reg.id = num.regionID where reg.parent_id = $region");
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
                                        $cmd = "select c.* from aim_comments c inner join aim_rates r on r.id = c.ratingID inner join aim_reg_num num on r.regnum_id = num.id inner join aim_dic_region reg on reg.id = num.regionID where reg.parent_id = $region order by c.create_dtm DESC limit $from,$count";
                    return $this->GetCommentsListInternal(
                                                $this->ExecuteList($cmd)
                                        );
                }


                                function GetRateCommentsCount($rateID)
                {
                    return $this->ExecuteValue("select count(ID) from aim_comments where ratingID = $rateID and isActive = 1");
                }

        function AddComment($comment)
        {
                        $this->ClearCommentCacheForRate($comment->get_RateID());
                        //@unlink($cfg->mainPath.'/cache/comments_for_rate_'.$comment->get_RateID().'_page_1.php');
                        return  $this->ExecuteNonQuery("insert into aim_comments (filename, visiterID,ratingID,create_dtm,text) VALUES ('".$comment->get_Filename()."',".$comment->get_VisiterID().",".$comment->get_RateID().",'".date("Y-m-d H:i:s")."','".$comment->get_Text()."')");


        }


                function ClearCommentCacheForRate($rateID){
                        clear_cache_by_mask('comments_for_rate_'.$rateID.'_page');
                        return true;
                }


                function GetCommentRateID($commentID){
                        return intval($this->ExecuteValue("select ratingID from aim_comments where id = $commentID"));
                }


                function KillComment($commentID)
        {
                        $rate = $this->GetCommentRateID($commentID);
                        $this->ClearCommentCacheForRate($rate);
                        return  $this->ExecuteNonQuery("update aim_comments set isActive = 0 where id = $commentID");
        }

                function RestoreComment($commentID)
        {
                        $rate = $this->GetCommentRateID($commentID);
                        $this->ClearCommentCacheForRate($rate);
                        return  $this->ExecuteNonQuery("update aim_comments set isActive = 1 where id = $commentID");
        }

        function getInternalSelRegionList()
        {

        $query = sqr_list('
        select
        tt.parent_id as id,r.name, tt.cnt
        from
        (
        select parent_id, count(n.id) as cnt
        from
        aim_dic_region r
        left outer join aim_reg_num n on n.regionID=r.id
        left outer join aim_rates ra on ra.regnum_id=n.id
        group by parent_id
        ) as tt
        inner join aim_dic_region r on tt.parent_id=r.id
        where
        r.id=r.parent_id
        order by r.name ');

          $res=array();
          while (($row=mysql_fetch_assoc($query))!=false)
          {
              $res[]=$row;
          }

         return($res);
        }

        function getSelRegionList()
        {
           $cfg = Config::get_Instance();

          $name='reg_numbers.php';
         if (is_cached ($name, 3600)) $res = unserialize(file_get_contents ($cfg->mainPath.'/cache/'.$name));
                                else {
                                 $res=$this->getInternalSelRegionList();
                                  cache_file ($name, serialize($res));
                                }




         return($res);
        }



                /*
                Notify
                */

                function GetRateSubscribersList($rateID,$userID = 0)
                {
                        //print_r("select distinct u.* from aim_notify n inner join aim_users u on u.id = n.userID where n.rateID = $rateID and (u.id <> $userID)");
                        return $this->GetUsersListInternal(
                                $this->ExecuteList("select distinct u.* from aim_notify n inner join aim_users u on u.id = n.userID where n.rateID = $rateID and u.isActive=1 and u.isBlocked=0 and (u.id <> $userID)")
                        );
                }

                function SubscribeOnRate($rateID,$userID)
                {
                        $id= intval($this->ExecuteValue("select id from aim_notify where rateID=$rateID and userID=$userID"));
                        if (!$id){
                                $this->ExecuteNonQuery("insert into aim_notify (rateID,userID) values ($rateID,$userID)");
                        }
                        return true;
                }

                function UnSubscribeOnRate($rateID,$uniqID)
                {
                        $userID= intval($this->ExecuteValue("select n.userID from aim_notify n inner join aim_users u on u.id = n.userID where n.rateID = $rateID and u.guid='$uniqID'"));
                        if ($userID>0){
                                $this->ExecuteNonQuery("delete from aim_notify where rateID = $rateID and userID = $userID");
                                return true;
                        }
                        return false;
                }

                /*
                Work with user option
                */
                function GetUserParam($userID,$name)
                {
                        return  ($this->ExecuteValue("select paramValue from aim_users_params where userID = $userID and name='$name'"));
                }

                function getRatesCoordsByNumber($number)
                {
                        $q = sqr_list("
                        select id,coords, coords_type, coords_zoom
                        from aim_rates
                        where regnum_id=".$number->id."
                        and isActive=1 and isGranted=1
                        and coords is not null
                        and coords<>''
                        ");

            $arr=array();
                        while (($row=mysql_fetch_object($q))!=false)
                        {
                                $arr[]=$row;
                        }

                        return($arr);
                }

          function getCoordsByRegion($regionID)
          {
          $row=sqr("
          select distinct r.coords, r.coords_type
          from aim_dic_region d2
          inner join aim_dic_region d on d.parent_id=d2.parent_id
          inner join aim_reg_num n on d.id=n.regionID
          inner join aim_rates r on n.id=r.regnum_id
          where d2.id=".$regionID."
          order by r.id desc
          limit 1
          ");

          return($row);
          }

                function getMWRateByID($id)
                {
                $obj = sqr_obj('select r.id,r.filename,r.rating, r.create_dtm,r.comment,
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
                                                r.id='.$id
                                                );

                                                return($obj);
                }

        function getCommentCntByMWRate($id)
        {
                $row=sqr_obj('select count(id) as cnt from aimw_comments where isActive=1 and ratingID='.$id);
                return($row->cnt);
        }


        function getTableByType($type)
        {
            $table='';
                switch ($type)
                {
                case 1: $table = 'aim_rates';break;
                case 2: $table = 'aim_comments';break;
                }

                return($table);
        }

          function getOwnerFieldByType($type)
        {
            $field='';
                switch ($type)
                {
                case 1: $field = 'visiter_id';break;
                case 2: $field = 'VisiterID';break;
                }

                return($field);
        }

        function getObjOwner($type, $id)
        {

          $table=DbManager::getTableByType($type);
          if ($table=='') return;

          $field=DbManager::getOwnerFieldByType($type);
          if ($field=='') return;


          $row = sqr_obj('select '.$field.' as uid from '.$table.' where id='.$id);
          return($row->uid);
        }

        function getBallValue($type, $id)
        {
//        echo '-'.$id;
                $table=DbManager::getTableByType($type);

                if ($table=='') return;

                $row = sqr_obj('
                select ball from '.$table.'
                where id='.$id.'
                ');
  //              echo $row->ball;
                return($row->ball);
        }

        function addBall($id, $val, $type)
        {

               $table=DbManager::getTableByType($type);
               if ($table=='') return;

               eqr('
               update '.$table.'
               set ball=ball+('.$val.')
               where id='.$id.'
               ');

        }

        function getUserRating($uid)
        {
         $row1=sqr("
         select sum(ball) as sm
         from aim_comments
         where VisiterID=".$uid."
         ");

         $row2=sqr("
         select sum(ball) as sm
         from aim_rates
         where Visiter_ID=".$uid."
         ");

         return($row1['sm']+$row2['sm']);


        }

        function getUserMessages($uid)
        {

                $list = sqr_listObj("
                        select m.isView2, m.user_id, m.user2_id, m.id, u2.login as user_name2, u.login as user_name, DATE_FORMAT(m.dtm,'%d.%m.%y %h:%i:%s') as dtm, m.isView, SUBSTRING(m.message,1,56) as message
                        from
                        aim_message m
                        inner join aim_users u on u.id=m.user_id
                        inner join aim_users u2 on u2.id=m.user2_id
                        where
                        (
                        m.user_id=".$uid."
                        or
                        m.user2_id=".$uid."
                        )
                        and m.parent_id=0
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

                $m = DbManager::getMessageByID($parent_id);
//                       echo intval($m->user_id == $user_id);
               if ($m->user_id != $user2_id)
                eqr("update aim_message set isView = 0 where id=".$parent_id." ");
                else
                eqr("update aim_message set isView2 = 0 where id=".$parent_id." ");

                }


                TLogMgr::add($id, 'add', 'aim_message');
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
                eqr("
                update aim_message set isView".$vn." = 1 where id=".$id." or parent_id=".$id."
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

}
?>
