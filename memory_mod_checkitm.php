<?
header('Content-type: text/html; charset=windows-1251');
session_start();

function getStripe($n)
{
$txt='';
        for ($i=0;$i<$n;$i++)
        {
                $txt.='-';
        }
        return($txt);
}

if (!isset($_SESSION['in_memory_products']))
$_SESSION['in_memory_products'] = array();

$err=false;

$act='';
if (isset($_REQUEST['act']))
$act = $_REQUEST['act'];


switch ($act)
{
case 'add':

                         if (!isset($_REQUEST['name'])) $err=true;
                         $goods_names = htmlspecialchars($_REQUEST['name'], ENT_QUOTES);
                         $goods_names = iconv('UTF-8', 'cp1251', $goods_names);

                         if (!isset($_REQUEST['price'])) $err=true;
                         $goods_prices = floatval(str_replace(',','.',$_REQUEST['price']));

                         if (isset($_REQUEST['cnt']))
                         $goods_cnt = floatval(str_replace(',','.',$_REQUEST['cnt']));
                         else
                         $goods_cnt =1;

                         $barcode='';
                         if (isset($_REQUEST['barcode']))
                         $barcode = htmlspecialchars($_REQUEST['barcode'], ENT_QUOTES);


                         if ($goods_cnt==0) $goods_cnt=1;


                         if ($goods_names=='') $err=true;
                         if ($goods_prices=='') $err=true;

                         $goods_prices=str_replace(',','.',$goods_prices);

                         $good->name = $goods_names;
                         $good->price = $goods_prices;
                         $good->cnt = $goods_cnt;
                         $good->del=0;
                         $good->barcode = $barcode;


                         //unset($_SESSION['in_memory_products']);
                         if (!$err)
                         $_SESSION['in_memory_products'][]=$good;
                         break;
case 'del':
                         if (!isset($_REQUEST['index'])) $err=true;

                         $good=$_SESSION['in_memory_products'][$_REQUEST['index']];
                         $good->del=1;

                         break;
case 'undel':
                         if (!isset($_REQUEST['index'])) $err=true;

                         $good=$_SESSION['in_memory_products'][$_REQUEST['index']];
                         $good->del=0;

                         break;

case 'clear':
                         $i=0;
                         foreach($_SESSION['in_memory_products'] as $item)
                         {

                         $_SESSION['in_memory_products'][$i]->del=1;

                          $i++;
                         }

                         break;
}
//var_dump($_SESSION['in_memory_products']);
$i=0;

if (sizeof($_SESSION['in_memory_products'])==0)
{
echo 'empty';
        return;
}

?>
<table cellpadding=0 cellspacing=0 border=0 class='chk_print_list'>
<?
$sum=0;
foreach($_SESSION['in_memory_products'] as $key=>$item)
{
$i++;
//var_dump($item);

$max_l=40;
$max_r=15;

$cnttxt='';
$stripe_cnt_r='';
if (($item->cnt!='') && ($item->cnt>0))
{
$cnttxt=$item->cnt.' x '.round($item->price/$item->cnt,2);
$stripe_cnt=$max_l-strlen($cnttxt);
$cnttxt='<br/><nobr>'.$cnttxt.'</nobr>'.getStripe($stripe_cnt);
$stripe_cnt_r=getStripe($max_r-strlen($item->price.' RUB [-]'));

$bar='';
if ($item->barcode!='')
$bar='<br/>'.$item->barcode;
}



        echo '
       <tr>
          ';
          if ((!isset($item->del)) || ($item->del==0))
          {
          echo '
          <td width="10px" valign="top">'.$i.'. </td>
          <td width="300">'.$item->name.$bar.$cnttxt.'</td>
          <td align="right" valign="bottom">
          <nobr>'.$stripe_cnt_r.$item->price.' RUB  <a href="javascript:DelChkItm('.$key.')"> [-] </a></nobr>';
          $sum+=$item->price;
          }
          else
          {
          echo '
          <td width="10px" valign="top">'.$i.'. </td>
          <td><strike>'.$item->name.$bar.$cnttxt.'</strike></td>
          <td align="right" valign="bottom">
          <nobr><strike>'.$stripe_cnt_r.$item->price.' RUB <a href="javascript:UnDelChkItm('.$key.')"> [+]</a></strike></nobr>';
          }

          echo '</td>
       </div>
        ';
}
?>
<tr>
<td></td>
  <td align='right'>
  <b>Itogo:</b>
  </td>
  <td align='right'>
    <b><?=$sum ?>  RUB</b>
  </td>
</tr>
<tr>
 <td>
 </td> <td>
 </td>
 <td align='right' style='padding-top:10px;'>
  <a href='javascript:ClearList()'>[очистить список]</a>
 </td>
</tr>
</table>