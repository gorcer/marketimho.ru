<?


class ReportMgr
{
	function calcPereplatiByUser($dt1, $uid)
	{
		$all_det=sqr_listObj("
		select d.id, d.product_id, d.cnt, d.OnePrice, h.shop_id, h.create_dtm, d.head_id
		from chk_check_det d
		inner join chk_check_head h on h.id=d.head_id
		where h.RealBuy=1
		and h.create_dtm>'".$dt1."'
		and h.create_dtm<DATE_ADD('".$dt1."', INTERVAL 1 MONTH)
		and h.owner_id=".$uid."
		order by h.shop_id
		"
		);


		$pereplata=0;
		$prim_pereplata=0;
		$econom=0;
		$shop_id=0;
		foreach($all_det as $chk_det)
		{

		if ($shop_id!=$chk_det->shop_id)
		{
		$shop_id=$chk_det->shop_id;			  $CP = DbManager::getShopCPlist($shop_id, false);
		}


                      $analiz = sqr_obj('
                                 select min(d.OnePrice) as min_price, max(d.OnePrice) as max_price
                                 from chk_check_det d
                                 inner join chk_check_head h on h.id=d.head_id
                                 inner join chk_v_det_by_user vd on vd.product_id=d.product_id and vd.user_id='.$uid.'
                                 where
                                 d.product_id='.$chk_det->product_id.'
                                 and h.DelMark=0
                                 and d.OnePrice>0
                                 and h.create_dtm<="'.$chk_det->create_dtm.'"
                                 and h.create_dtm>DATE_SUB(h.create_dtm, INTERVAL 3 MONTH)
              ');

			if (($analiz->max_price!='') && ($analiz->max_price>$chk_det->OnePrice))
			{
			//Mark( $analiz->max_price.' - '.$chk_det->OnePrice.'='.$econom);				$econom+=($analiz->max_price-$chk_det->OnePrice)*$chk_det->cnt;			}

			  if (($analiz->min_price=='') || ($analiz->min_price==$chk_det->OnePrice))
			  {
			    if ($CP!=false)
	            $MinPriceCP = DbManager::getMinPrice_byCP($CP,$chk_det->OnePrice);

			if ($MinPriceCP!=false)
			if ($MinPriceCP<$chk_det->OnePrice)
			$prim_pereplata+=$MinPriceCP;			  }
			  else
              if ($analiz->min_price<$chk_det->OnePrice)
              {             //	Mark( $chk_det->product_id.' - ('.$chk_det->head_id.') купил за "'.$chk_det->OnePrice.'", а можно было за '.$analiz->min_price);
              $pereplata+=($chk_det->OnePrice-$analiz->min_price)*$chk_det->cnt;
              }
		}
           $res->pereplata = $pereplata;
           $res->prim_pereplata = $prim_pereplata;

			if ($pereplata>0)
            eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (1, "'.$dt1.'", '.$res->pereplata.', '.$uid.') ');

			if ($prim_pereplata>0)
            eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (2, "'.$dt1.'", '.$res->prim_pereplata.', '.$uid.') ');

			if ($econom>0)
            eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (6, "'.$dt1.'", '.$econom.', '.$uid.') ');

			return($res);

	}

	function getSumByTag($dt1,$uid, $tag)
	{      $row=sqr_obj("
        select sum(d.price) as sm
		from chk_check_det d
		inner join chk_check_head h on h.id=d.head_id
		inner join chk_products p on d.product_id=p.id
		where h.RealBuy=1
		and h.create_dtm>'".$dt1."'
		and h.create_dtm<DATE_ADD('".$dt1."', INTERVAL 1 MONTH)
		and h.owner_id=".$uid."
        ".$tag."
      ");

      return($row->sm);
	}

	function calcGotovieObedi($dt1,$uid)
	{	$tag='and p.tags like "%Готовые блюда%"';
	$sm=ReportMgr::getSumByTag($dt1,$uid, $tag);
	$sm=$sm*0.7;

	if ($sm>0)
 	eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (3, "'.$dt1.'", '.$sm.', '.$uid.') ');

    return($row->sm);	}


	function calcFuell($dt1,$uid)
	{
	$tag='and p.tags like "%Топливо%"';
	$sm=ReportMgr::getSumByTag($dt1,$uid, $tag);
	$sm=$sm*0.7;

	if ($sm>0)
 	eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (7, "'.$dt1.'", '.$sm.', '.$uid.') ');

    return($row->sm);
	}


	function calcAlcohol($dt1,$uid)
	{
	$tag='and (p.tags like "%алкоголь%"';
	$tag.='or p.tags like "%пиво%"';
	$tag.='or p.tags like "%вино%")';

	$sm=ReportMgr::getSumByTag($dt1,$uid, $tag);

     // echo $sm;
	if ($sm>0)
 	eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (4, "'.$dt1.'", '.$sm.', '.$uid.') ');

    return($row->sm);
	}


	function calcToiletPaper($dt1,$uid)
	{
	$tag='and p.tags like "%Туалетная бумага%"';
	$sm=ReportMgr::getSumByTag($dt1,$uid, $tag);
	$sm=$sm*0.7;

	if ($sm>0)
 	eqr('insert into chk_report_value(rep_val_type, dtm, value, user_id) values (5, "'.$dt1.'", '.$sm.', '.$uid.') ');

    return($row->sm);
	}

	function CalcUserReps($dt1,$uid)
	{
	 eqr('delete from chk_report_value where dtm="'.$dt1.'" and user_id='.$uid);
	 ReportMgr::calcPereplatiByUser($dt1,$uid);
	 ReportMgr::calcGotovieObedi($dt1,$uid);
	 ReportMgr::calcAlcohol($dt1,$uid);
	 ReportMgr::calcToiletPaper($dt1,$uid);

	}

	function getMouthByUser($uid)
	{      $list = sqr_listObj("
      	select distinct dtm, DATE_FORMAT(dtm, '%m.%Y') as dtm_c, DATE_FORMAT(dtm, '%Y-%m-%d') as dtm_l
      	from chk_report_value
      	where
      	user_id=".$uid."
      			order by dtm desc
      ");

      return($list);
	}

	function getSumBuyMouth($mouth, $uid)
	{
      $row = sqr_obj("
      select sum(d.price) as sm
      from chk_check_det d
      inner join chk_check_head h on d.head_id=h.id
      where
      	h.create_dtm>'".$mouth."'
		and h.create_dtm<DATE_ADD('".$mouth."', INTERVAL 1 MONTH)
		and h.owner_id=".$uid."

      ");

      return($row->sm);	}

	function getPereplatiByMouth($mouth, $uid)
	{      $row = sqr_obj("
      select sum(value) as sm
      from chk_report_value
      where
      dtm='".$mouth."'
      and user_id=".$uid."
      and rep_val_type=1
      ");

      return($row->sm);	}

	function getMaxPereplatiByMouth($mouth, $uid)
	{
      $row = sqr_obj("
      select sum(value) as sm
      from chk_report_value
      where
      dtm='".$mouth."'
      and user_id=".$uid."
      and rep_val_type in (2,3,4,5)
      ");

      return($row->sm);
	}

	function getEconomByMouth($mouth, $uid)
	{
      $row = sqr_obj("
      select sum(value) as sm
      from chk_report_value
      where
      dtm='".$mouth."'
      and user_id=".$uid."
      and rep_val_type=6
      ");

      return($row->sm);
	}

	function getUserReportDetail($mouth, $uid)
	{
     $list = sqr_listObj("
     select d.value, di.name, di.descript, di.pos
     from chk_report_value d
     inner join chk_report_val_dic di on di.id=d.rep_val_type
     where
     d.user_id=".$uid."
     and
     d.dtm='".$mouth."'
     and di.pos in (0,1,2)
     order by di.pos desc
     ");
     return($list);	}

}

?>