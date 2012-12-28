<?php

class Timer{
	static $start_time;
	static $ld;


	static function start_time(){
		$start_time = microtime();
		$start_array = explode(" ",$start_time);
		self::$start_time = $start_array[1] + $start_array[0];
		return self::$start_time;
	}



	static function end_time(){
		$start_time=self::$start_time;
		$end_time = microtime();
		$end_array = explode(" ",$end_time);
		$end_time = $end_array[1] + $end_array[0];
		$dtime=$end_time - $start_time;
		global $page;
		if($page){
			$page->dtime = $dtime;
		}
		return $dtime;
	}

	static function dtime(){
		$end_time = microtime();
		$end_array = explode(" ",$end_time);
		$end_time = $end_array[1] + $end_array[0];
		if(!self::$ld){
			self::$ld=$end_time;return 0;
		}
		$page->dtime = $end_time - self::$ld;
		self::$ld=$end_time;

		return round($page->dtime, 7);
	}

}