<?php
class Bot {
	//static $recount_link_weight=10;
	static function run() {
	}
	static function add_task($task, $params, $rate = 0) {
		if ($task < 100) {
			$host_task = true;
			$table = "bot_task_host";
			$param = "host_id";
		} else {
			$link_task = true;
			$table = "bot_task_link";
			$param = "link_id";
		}
		$recounalltask=BOT_RECOUNT_OLINKS_ALL;
		$recounttask = BOT_RECOUNT_OLINKS;
		$reindextask = BOT_INDEX;
		if($task==$recounalltask){
			my_q("delete bot_task_link from bot_task_link left join link on link.id=bot_task_link.link_id where task=$recounttask and link.host_id=$params");
		}elseif ($task == $reindextask) {
			//$time=time();
			//my_up("host:reindex_time=$time:host_id=$params");
			//my_q ( "delete bot_task_link from bot_task_link left join link on link.id=bot_task_link.link_id where task=$recounttask and link.host_id=$params" );
		} elseif ($task == $recounttask) {
			//$host_id = Link::get_host_id ( $params );
			//$var2 = $host_id;
			if (my_qn ( "select * from $table where task=$reindextask and $param=$params" )) {
				return "Конфликт операций.";
			}
		}
		$params = h2s ( $params );
		$task = h2s ( $task );
		$exists = false;
		//		if($task==BOT_RECOUNT_OLINKS){
		//			$ar=explode(" ", $task);
		//			$ar[0];
		//			$exists=my_qn ( "select * from bot_task where task='$id %' and params='$params'");
		//		}else{
		//			$exists=my_qn ( "select * from bot_task where task=$task and params='$params'");
		//		}
		$exists = my_qn ( "select * from $table where task=$task and $param='$params'" );
		
		if (! $exists) {
			my_in ( "$table:task=$task,$param=$params,rate=$rate" );
			return true;
		}
		return "Операция уже существует.";
	}
	
	static function rem_task($task, $params) {
		if ($task < 100) {
			$host_task = true;
		} else {
			$link_task = true;
		}
		
		if ($host_task) {
			$table = "bot_task_host";
			$param = "host_id";
		} elseif ($link_task) {
			$table = "bot_task_link";
			$param = "link_id";
		}
		
		my_q ( "delete from $table where task=$task and $param='$params'" );
	}
}
?>