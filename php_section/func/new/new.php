<?php
$user_id = User::get_c_id ();
if (! $user_id) {
	print "Необходимо войти";
	exit ();
}
$post = new Post ();
if (h2b ( $_POST [lpost] )) {
	$lpost = unserialize ( $_SESSION [lpost] );
	if($lpost instanceof Post){
		$post=$lpost;	
	}
} else {
	$post->source = $_POST ['text'];
	$post->name = h2s ( $_POST ['name'] );
	$post->link = s2link::translit ( $_POST ['link'] );
	$post->parent_post_id = $_POST ['parent'] ? $_POST ['parent'] : 0;
	$post->author_id = h2i ( User::get_id () );
	$post->rate = Post::get_new_post_rate ( User::get_c_id () );
	$post->pub = $_POST [unpub] ? 1 : 2;
	
	unset($_POST ['text']);
	
	$ctype = 0;
	$params = $_POST;
	while ( $params ["s" . $ctype] > 0 ) {
		if ($ctype == $params ["s" . $ctype]) {
			error("Wtf with types??");
		}
		$ptype=$ctype;
		$ctype = $params ["s" . $ctype];
		unset($params ["s" . $ptype]);
	}
	foreach ($params as $name=>$value){
		if(preg_match("#s[0-9]+#", $name)){
			unset($params[$name]);
		}
	}
	$post->params = $params;
	$post->params [scaptchar] = $_SESSION [scaptcha];
	$post->params [preview] = h2b ( $_POST ['preview'] ) || h2b ( $_POST ['button_name'] == "prev" );
	$post->params [newlink] = "new";
	
	if (h2b ( $_POST [save] )) {
		$_SESSION ["new_set"] = null;
		//unset($_POST[save]);
		$_SESSION ["new_set"] [pub] = ! h2b ( $_POST ['unpub'] );
		$_SESSION ["new_set"] [params]  = $_POST;
	} else {
		$_SESSION ["new_set"] = null;
	}
	
	
	$post->type_id = $ctype;
//	if ($ctype == st_vars::$type_main) {//what??
//		if ($post->pub < 2) {
//			$post->pub -= 1;
//		}
//	}
	$msg = $post->process ();
	if ($msg !== true) {
		print $msg;
		exit ();
	}
}
if (! $post->text && ! $post->name) {
	$post = null;
}

//check cond
//init vars
date_default_timezone_set ( 'Europe/Moscow' );

$crate = User::get_c_rate ();

$ncrate = st_vars::$rate_new_comment;
$e = 0;
//end


if ($crate < $ncrate) {
	print "Слишком маленький рейтинг";
	exit ();
}
if (($post->params [scaptcha] != $post->params [scaptchar] || ! $post->params [scaptcha]) && $crate < st_vars::$rate_no_captcha && $post->parent_post_id == 0) {
	print "Неправильно введён код";
	exit ();
}

$post->newp();

?>