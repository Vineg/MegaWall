<?php

class archive_list{
	static $get_childs;
	static $iget_childs;
	static $get_name;
	static $get_link;

	function archive_list(){
		require_once 'shortcuts.php';
		require_once 'settings/mysql_connect.php';
		 require_once 'phpscr/functions.php';
	}
	
	static function get_archive_list($type_id=false){
		return;
		global $_vars;
 		$ht=Request::get_ht();
//rint_r($svars);
//		if(get_svar("archanged")==true){
//			fp("get_archive_list.html", "<ul class='ttree'>".self::build_childs("null", "", "$ht/archive")."</ul>");
//			set_svar("archanged", false);
//		}
//		return fr("get_archive_list.html");
		if(!$type_id){
			$type_id=$_vars[main_type_id];
		}
		$mdate=archive_list::get_first_record_date($type_id);
		if(!$mdate){return false;}
		$mdate=strtotime($mdate);
		$mdate+=24*60*60;
		//$cal=array(array(array()));
		for($i=time(); $i>$mdate; $i-=60*60*24){
			$year=date("Y", $i);
			$month=date("F", $i);
			$rmonth=Lang::translate($month);
			$day=date("d", $i);
			$cal[$year][$rmonth][$day]=Request::get_ht()."/archive/$year/$month/$day";
		}
		return Page::make_olist($cal, true, array("nofollow_op"=>true));
	}
	
	static function get_childs($q){
		$result=my_q($q);
		for($i=0; $i<my_n($result); $i++){$get_childs[$i]=my_r($result, $i, "date");}
		return $get_childs;
	}


	static function get_name($cval){
		return(self::translate($cval));
	}


	static function build_childs($val, $type, $plk="", $pq=""){
		if($type==""){$ctype="%Y";}else if($type=="%Y"){$ctype="%M";}else if($type=="%M"){$ctype="%d";}else if($type=="%d"){return false;}
		$nq=self::nq($val, $type, $ctype, $pq);
		$npq=self::pq($val, $type, $pq);
		$childs=self::get_childs($nq);
		if(count($childs)<1){return false;}
		for($i=0; $i<count($childs); $i++){
			$cval=$childs[$i];
			$cname=self::get_name($cval);
			$link=$cval;
			$cplk=$plk."/".$link;
			$childdivs=self::build_childs($cval, $ctype, $cplk, $npq);
			$obul=($ctype!="%d")?"<td><button type=button class='bullet up open'></button></td>":"";
			$cont=($ctype=="%d")?"<a href='$cplk'>$cname</a>":$cname;
			$res.=<<<EOQ
			<li class="olist">
			<table><tr>
			$obul
			<td>$cont
			<ul class="openobj hidden">
			$childdivs
			</ul>
			
			</td>
			</tr></table>
			</li>
EOQ;
		}
		return $res;
	}


	static function nq($val, $type, $ctype, $pq){
		$fr=($pq=="")?"archive":"($pq)date";
		$exp=($type=="")?"1":"date_format(date, '$type')='$val'";
		return "select distinct date_format(date, '$ctype') as date from $fr where $exp order by date desc";
	}
	static function pq($val, $type, $pq){
		$fr=($pq=="")?"archive":"($pq)date";
		$exp=($type=="")?"1":"date_format(date, '$type')='$val'";
		return "select * from $fr where $exp order by date desc";
	}
	static function translate($val){
		$arr=array(
		"January"=>"Январь",
		"February"=>"Февраль",
		"March"=>"Март",
		"April"=>"Апрель",
		"May"=>"Май",
		"June"=>"Июнь",
		"July"=>"Июль",
		"August"=>"Август",
		"September"=>"Сентябль",
		"Octombre"=>"Октябрь",
		"Novembre"=>"Ноябрь",
		"Desembre"=>"Декабрь",
		);
		$nval=$arr[$val];
		return($nval=="")?$val:$nval;
	}
	
	static function get_first_record_date($type_id){
		$date=my_fst("select * from archive where type_id = $type_id order by date asc limit 0,1", "date");
		//$date=$date?$date:date("d-m-y");
		return $date;
	}
}
?>