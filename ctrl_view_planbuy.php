<?
header('Content-type: text/html; charset=windows-1251');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');



DbManager::Connect();
  $curUser = UserManager::GetCurrentUser();
  UserManager::RefreshUserOptions();
  $curUser = UserManager::GetCurrentUser();


 $Products = DbManager::getUserPlan_Products($curUser);

 if (sizeof($Products)==0)
 {
         ?>
         <div class='txt'>
         <center>
         Список планируемых покупок пуст.
         </center>
         </div>
         <?
         return;
 }

$shop_id=-1;
if (isset($curUser->options['inplan_shop']))
{
$shop_id=$curUser->options['inplan_shop'];
}


?>

<script type="text/javascript">
$(document).ready(function()
{
$("#buyplan_det_m").click(function()
{
var checked_status = this.checked;

$("input").each(function()
{
if ($(this).attr('name')=='buyplan_det[]')
this.checked = checked_status;
});

});
});

</script>

<form action='ctrl_action.php'>
<!--<input type='hidden' name='act' value='del_buyplan_det'/> -->
<table cellpadding=0 cellspacing=3 border=0 class='inproduct_row' width='466'>
<tr>
<th></th><th></th>
 <th align='center' class='inprice_dtm' width='90px'><u>цена</u></th>
 <th align='center' class='inprice_dtm' width='60px'><u>кол-во</u></th>
 <th align='center' class='inprice_dtm' width='70px'><u>итого</u></th>
 <td ><input type='checkbox' name='buyplan_det_m' id='buyplan_det_m'/></td>
</tr>

<?
$i=0;
$sum=0;
$sum_min=0;
$sum_max=0;
$total=0;
$arr=array();
foreach ($Products as $item)
{
//$arr[]=$item->id;

 $total_min=0;
 $total_max=0;
 $total='n/a';

if ($item->product_id!=-1)
$prodname_line="<a href='product/".$item->product_id."'>".$item->name."</a>";
else
$prodname_line=$item->name;


if ($shop_id!=-1)
{

$sum+=round($item->price*$item->cnt,2);
$i++;
if ($item->price=='') $item->price='n/a'

?>
<tr>
 <td width='25px' height='25px' style='background:#f5f5f5;' align='center' valign='center'><?=$i ?></td>
 <td style='border-bottom:1px solid #e5e5e5;'><?=$prodname_line ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=$item->price ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=$item->cnt ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=round($item->price*$item->cnt,2) ?></td>
 <td style='border-bottom:1px solid #e5e5e5;'><input type='checkbox' name='buyplan_det[]' value='<?=$item->id ?>'/></td>
</tr>
<?
}
else
{
$total_min=round($item->min_price*$item->cnt,2);
$total_max=round($item->max_price*$item->cnt,2);

$sum_min+=$total_min;
$sum_max+=$total_max;
$i++;

if ($item->min_price=='')
$price='n/a';
else
{

if ($item->min_price!=$item->max_price)
{
$price=($item->min_price.' - '.$item->max_price);
$total=($total_min.' - '.$total_max);
}
else
{
$price=($item->min_price);
$total=($total_min);
}


}


?>
<tr>
 <td width='25px' height='25px' style='background:#f5f5f5;' align='center' valign='center'><?=$i ?></td>
 <td style='border-bottom:1px solid #e5e5e5;'><?=$prodname_line ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=$price ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=$item->cnt ?></td>
 <td class='inprice_price'  style='border-bottom:1px solid #e5e5e5;' align='center' ><?=$total ?></td>
 <td style='border-bottom:1px solid #e5e5e5;'><input type='checkbox' name='buyplan_det[]' value='<?=$item->id ?>'/></td>
</tr>
<?
}
}

?>
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <?

  $total = ($shop_id!=-1)?$sum:($sum_min==$sum_max?$sum_min:($sum_min.' - '.$sum_max));

 ?>

 <td align='center' style='border-top:1px solid #e5e5e5;'><?=$total; ?></td>
</tr>
</table>

<table width='98%'>
<tr>
<td>
<p align='left'>
<a href='p/<?=$curUser->id ?>' title='Печатать всё.' target='_blank'><img src='pict/print.gif'/></a>
</p>
</td>
<td>
<p align='right' class='txt' style='font-size:13px;'>
Действие с выбранными<br/>
<select name='act'>
 <option value='print_buyplan_det'>Распечатать</option>
 <option value='del_buyplan_det'>Удалить</option>
</select>
<input type='submit' value='ok'/>
</p>

</td>
</tr>
</table>




</form>