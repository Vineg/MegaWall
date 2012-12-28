<?php
class Lang{
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
		"October"=>"Октябрь",
		"November"=>"Ноябрь",
		"December"=>"Декабрь",
		);
		$nval=$arr[$val];
		return($nval=="")?$val:$nval;
	}
}
?>
