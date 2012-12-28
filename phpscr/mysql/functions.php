<?php
// table:f1,f2,f3:v1,v2,v3//for mysql code insert 'value'
// table,array(values)
function my_in($s, $ar = false) {
	if (is_array ( $ar )) {
		$def = st_vars::$def_values [$s] ? st_vars::$def_values [$s] : array ();
		$ar = cut_spaces ( array_merge ( $def, $ar ) );
		foreach ( $ar as $name => $value ) {
			$fsar [] = $name;
			if (stripos ( $value, "'" ) !== false) {
				$valar = mark_out ( $value, "'" );
				$vsar [] = $valar [0];
			} else {
				$vsar [] = "'$value'";
			}
		}
		$vs = join ( ",", $vsar );
		$fs = join ( ",", $fsar );
		my_q ( "insert into $s($fs) values($vs);" );
		return mysql_insert_id ();
	} else {
		$ar0 = explode ( ":", $s );
		if (count ( $ar0 ) == 3) {
			// rint_r ( $ar0 );
			$ar0 [1] = explode ( ",", $ar0 [1] );
			$ar0 [2] = explode ( ",", $ar0 [2] );
			// for($i=0; $i<count($ar0[2]); $i++){
			// $ar0[2][$i]="'".$ar0[2][$i]."'";
			// }
			$qar = array ();
			for($i = 0; $i < count ( $ar0 [1] ); $i ++) {
				$qar [$ar0 [1] [$i]] = $ar0 [2] [$i];
			}
		} else {
			$ss = $ar0 [1];
			$qar = s2ar ( $ss, ",", "=" );
		}
		return my_in ( $ar0 [0], $qar );
		
		// $tbl=$ar0[0];
		// $fs=join(",", $ar0[1]);
		// $vs=join(",", $ar0[2]);
		// my_q("insert into $tbl($fs) values($vs);");
	}
}

//table:f1=v1,f2=v2:where|table:where,up_array //for mysql code use 'v1' instead of v1
function my_up($shorten_query, $set_ar = false) {
	if($set_ar[message]!=""){
		debug_message();
	}
	if (is_array ( $set_ar )) {
		$shorten_query_ar = explode ( ":", $shorten_query );
		$table = $shorten_query_ar [0];
		$where = $shorten_query_ar [1];
		
		if (st_vars::$fields [$table]) {
			$set_ar = array_mask ( st_vars::$fields [$table], $set_ar );
		}
		
		//$def = st_vars::$def_values [$table] ? st_vars::$def_values [$table] : array ();
		$set_ar = cut_spaces ($set_ar);
		foreach ( $set_ar as $name => $value ) {
			$column_ar [] = $name;
			if (stripos ( $value, "'" ) !== false) {
				$valar = mark_out ( $value, "'" );
				$value_ar [] = $valar [0];
			} else {
				$value = my_s ( $value );
				$value_ar [] = "'$value'";
			}
		}
		for($i = 0; $i < count ( $column_ar ); $i ++) {
			$set_ar_str [$i] = u2s ( $column_ar [$i] ) . "=" . u2s ( $value_ar [$i] ) . "";
		}
		$set_str = join ( ",", $set_ar_str );
		// cho "update $s set $ss where $where;<br />";
		$columns = $set_str;
		
		// if($table=="host"){
		// debug(print_br(debug_backtrace(),true));
		// }
		
		my_q ( "update $table set $set_str where $where;" );
		//return "update $s set $ss where $where;";
	} else {
		$shorten_query_ar = explode ( ":", $shorten_query );
		$pcount = count ( $shorten_query_ar );
		if ($pcount != 3) {
			// my_in("log:msg:error in my_up".my_s(rint_r($ar0, true))."");
			error ( "wrong count of my_up params:expected 3, give $pcount" . print_br ( $shorten_query_ar, true ) );
			exit ();
		} else {
			$shorten_query_ar [1] = s2ar ( $shorten_query_ar [1], ",", "=" );
			// for($i=0; $i<count($ar0[2]); $i++){
			// $ar0[2][$i]="'".$ar0[2][$i]."'";
			// }
			// $qar=array();
			// for($i=0; $i<count($ar0[1]); $i++){
			// $qar[$ar0[1][$i]]=$ar0[2][$i];
			// }
			return my_up ( "$shorten_query_ar[0]:$shorten_query_ar[2]", $shorten_query_ar [1] );
			
			// $tbl=$ar0[0];
			// $fs=join(",", $ar0[1]);
			// $vs=join(",", $ar0[2]);
			// my_q("insert into $tbl($fs) values($vs);");
		}
	}
}

//table:field:where
function my_f($shorten_query){
		$shorten_query_ar = explode ( ":", $shorten_query );
		$pcount = count ( $shorten_query_ar );
		if ($pcount != 3) {
			// my_in("log:msg:error in my_up".my_s(rint_r($ar0, true))."");
			error ( "wrong count of my_f params:expected 3, give $pcount" . print_br ( $shorten_query_ar, true ) );
			exit ();
		} else {
			$shorten_query_ar = explode ( ":", $shorten_query );
			$table = $shorten_query_ar [0];
			$field = $shorten_query_ar [1];
			$where = $shorten_query_ar [2];			
			my_fst ( "select $field from $table where $where", $field);
		}
}