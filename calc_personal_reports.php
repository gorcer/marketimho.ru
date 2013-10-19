<?

include_once("config/common.php");
include_once("lib/DBManager.inc");
include('lib/ReportMgr.inc');

session_start();


$dbmanager = DbManager::get_Instance();
$UserManager = UserManager::get_Instance();
$MemoryMgr = TMemoryMgr::get_Instance();

$curUser = $UserManager->GetCurrentUser();


  $list=sqr_listObj("
                                                        select tt.id, tt.det_cnt
                                                        from
                                                        (
                             select u.id, count(distinct d.product_id) as det_cnt
                             from
                             aim_users u
                             inner join chk_check_head c on c.owner_id=u.id
                             inner join chk_check_det d on d.head_id=c.id

                             left outer join chk_friends f on  f.user_id=u.id and f.status='friend'
                             where
                             c.DelMark=0
                             group by u.login
                             ) as tt
                             where tt.det_cnt>150


                             ");

foreach($list as $item)
{
if ($item->id!='')
 {
//  Mark(ReportMgr::CalcUserReps('2009-09-01',$item->id));
//  Mark(ReportMgr::CalcUserReps('2009-10-01',$item->id));
  Mark(ReportMgr::CalcUserReps('2010-01-01',$item->id));

 }
}


?>
