<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');

DbManager::Connect();

$curUser = UserManager::GetCurrentUser();

if (!$curUser) return;

 $FriendList = DbManager::getFriendsList($curUser->id);
 $ClaimList = DbManager::getFriendClaimList($curUser->id);

 $FriendList=array_merge($ClaimList,$FriendList);

?>


<table cellpadding=5 cellspacing=0 border=0 class='txt' width='90%'>
<tr>
 <td colspan='2'><div class='txt'><b>Список друзей</b></div> </td>
 <th width='100px' align='center' style='background-color:#E3E889;border-bottom:1px solid #C8D069;color:#656919;'>статус</th>
</tr>
<?
$i=0;
foreach($FriendList as $friend)
{



$i++;
$status='';
$tstyle='';
$cstyle='';
$proc_name='DelFriend';
$plus='';
$UserStat=false;
if ($friend->FriendStatus=='wait') $status='ожидание подтверждения';
if ($friend->FriendStatus=='emailed')
{
        $status='приглащение отправлено';
}
if ($friend->FriendStatus=='deleted')
{
        $status='удалена';
        $tstyle='text-decoration:line-through;color:#888888;';
        $cstyle='background:#E3E4C0;';
        }
if ($friend->FriendStatus=='claim')
{
         $status='<span class="fr_claim">хочет дружить</span>';
         $tstyle='background-color:#EBF7FF;';
         $proc_name='DeclineFriend';
         $plus='<a href="javascript:ApproveFriendship('.$friend->friendship_id.')" title="дружить"><img src="pict/plus.gif" width="12px"/></a> ';
}
if ($friend->FriendStatus=='decline')
{
   $tstyle='background-color:#FFF7F6;';
   $status='<span class="fr_declined">отказано</span>';
}
if ($friend->FriendStatus=='friend')
{
          $tstyle='background-color:#F8FFEA;';
          $status='<span class="fr_accepted">дружба</span>';
}

if ($friend->id!='')
$UserStat = DbManager::getUsetStatistic($friend->id);

              ?>
              <tr>
               <td style='border-bottom:2px solid #ffffff;background-color:#f0f0f0;' width='20px' height='20px' align='center' valign='center'><?=$i ?></td>
               <td class='fr_name' style='<?=$tstyle ?>'><b><? echo ($friend->login=='')?$friend->email:'<a href="user/'.$friend->login.'">'.$friend->login.'</a>'; ?></b>
               <?
               if ($UserStat)
               {
               ?>

               <br/>
               <span class='fr_descript'>Магазинов: <?=$UserStat->ShopCnt ?> Строк: <?=$UserStat->DetCnt ?></span>
               <? } ?>
               </td>
               <td align='center' class='fr_status' style='<?=$cstyle ?>'><?=$status ?></td>
               <td width='10%' align='center' class='fr_name'>
                <?=$plus ?><a href='javascript:<?=$proc_name ?>(<?=$friend->friendship_id ?>)'><img src='pict/minus.gif' width='12px'/></a>
               </td>
              </tr>
              <?
}

if ($i==0)
{
?>
<tr>
 <td style='border:1px solid #f0f0f0;' colspan='4'>
  Список друзей пуст.
 </td>
</tr>

<?
  }
?>

</table>