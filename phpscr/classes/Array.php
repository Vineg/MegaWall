<?php
class Ar{
	static function remvalue($ar, $rvalue=false, $strict=false){
		$res=array();
//		foreach($ar as $key=>$value){
//			if(($value!=$rvalue)||($strict&&$value!==$rvalue)){
//				if(is_int($key)){
//					$res[]=$value;
//				}else{
//					$res[$key]=$value;
//				}
//			}else{
//			}
//		}
		
		while(array_search($rvalue, $ar)!==false){
			unset($ar[array_search($rvalue, $ar)]);
		}
		return $ar;
	}
	
	static function ar2ar($array){
		$res=array();
		foreach ($array as $key=>$value){
			foreach ($value as $key=>$value){
				if(is_int($key)){
					$res[]=$value;
				}else{
					$res[$key]=$value;
				}
			}
		}
		return $res;
	}
}

function array_merge_unique($array1, $array2){
	foreach($array2 as $key=>$value){
		if(array_search($value, $array1)===false){
			if(is_int($key)){
				$array1[]=$value;
			}else{
				//if(!isset($array1[$key])){
				$array1[$key]=$value;
				//}
			}
		}
	}
	return $array1;
}

function array_substr($array1, $array2){
	foreach ($array2 as $key=>$value){
		$array1=Ar::remvalue($array1, $value);
	}
	$array1=array_compress($array1);
	return $array1;
}

function array_compress($array, $strict=false){
	$res=array();
	foreach ($array as $key=>$value){
		if(is_int($key)){
			$res[]=$value;
		}else{
			$res[$key]=$value;
		}
	}
	return $res;
}

function array_mask($array1, $array2){
	foreach ($array2 as $key=>$value){
		if(in_array("$key", $array1)){
			$res[$key]=$array2[$key];
		}
	}
	return $res;
}

function last_key($ar){
	end($ar);
	return key($ar)+1;
}

function max_index($ar){
	return last_key($ar);
}
?>