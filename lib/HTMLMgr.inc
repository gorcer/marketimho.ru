<?


class THTMLMgr
{
 var $iscache=true;
 var $cachedir='cache/';

       function THTMLMgr()
       {

       }

       function getPagesHTML($page, $pageCount, $link='')
       {

          $imax=10;
          $start=1;

          $start=(($page-$imax/2+1)<=0)?1:$page-$imax/2+1;
          $end = (($page+$imax/2-1)>$pageCount)?$pageCount:$page+$imax/2-1;

          if ($end<$pageCount && $pageCount<$imax) $end = $pageCount;
          elseif ($end<$imax && $imax<$pageCount) $end = $imax;



          if ($pageCount>1){


          if ($start>1)
           {
                    ?>  <div class='pageitem_l'><a href="<?=$link ?>&page=1">1</a></div> <?
           }
//           echo $start.'---'.$end;
                  for ($i=$start;(($i<=$end));$i++){

                          if ($i==$page) {
                                  ?>
                                  <div class='pageitem_sel'><?= $i ?></div>
                                  <?
                          } else {
                                  ?>
                                  <div class='pageitem'><a href="<?=$link ?>&page=<?= $i?>"><?= $i ?></a></div>
                                  <?
                          }
                  }

                  if (($i<$pageCount) && ($i=$imax))       //последн€€ страница
                   {
                    ?>  <div class='pageitem_r'><a href="<?=$link ?>&page=<?= $pageCount?>"><?= $pageCount ?></a></div> <?
                   }
          }

       }


}

?>