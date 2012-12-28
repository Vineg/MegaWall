<?php
$user_id=User::get_id();
$ucnt = get_users_cnt();
if(!$user_id){print "Необходимо войти"; exit;}

if(h2b($_POST[lpost])){
	$post=new Post();
	$post=unserialize($_SESSION[lpost]);
}else{
	$post = new Post();
	if(!$_POST[img_src]){
		print "введите ссылку";
		exit;
	}
	$post->params[newlink]="newimg";
	$post->params[img_src]=$_POST[img_src];
	$post->source=make_img($_POST[img_src]);
	$post->name=h2s($_POST['name']);
	$post->link=s2link::translit($_POST['name']);
	$post->parent_post_id=0;
	$post->params[brreplace]=false;
	$post->params[preview]=h2b($_POST['preview'])||h2b($_POST['button_name']=="prev");
	$post->params[save]=false;
	$post->author_id=h2i(User::get_id());
	$post->rate=User::get_rate($user_id)*my_sqrt_z($ucnt+1)/10;
	$post->params["from"]=false;
	$post->pub=2;
	$post->params[scaptchar]=$_SESSION[scaptcha];
	$post->params[scaptcha]=$_POST[scaptcha];
	$post->params[upload]=false;
	
	
	$post->type_id=vars::$type_images;
	
	$msg=$post->process();
	
	if($msg!==true){print msg; exit;}
}
if(!$post->text&&!$post->name){
	$post=false;
}

//check cond
//init vars
date_default_timezone_set('Europe/Moscow');

$crate=User::get_c_rate();

$ncrate=st_vars::$rate_new_comment;
$e=0;
//end

if($crate<$ncrate){
	print "Слишком маленький рейтинг";
	exit;
}


if(($post->params[scaptcha]!=$post->params[scaptchar]||!$post->params[scaptcha])&&$crate<st_vars::$rate_no_captcha&&$post->parent_post_id==0){
	print "Неправильно введён код";
	exit;
}

if(!$post){
	print "Ошибка2";
	exit;
}
//end

if($post){
	if(!$post->params[preview]){
		$post->submit();
	}else{
		$_SESSION[lpost]=serialize($post);
	}
}else{
	print "Ошибка1";exit;
}

if($post){
	if($post->params[preview]==true){
		print "<script>location='/lpreview'</script>";
	}else if($post->parent_post_id==0){
		$pid=$post->pid;
		$link=$post->link;
		print "<script>location='/post/$pid/$link'</script>";
		unset($_SESSION[lpost]);
		$post=false;
	}else{
		$parlink=get_post_link($post->parent_post_id);
		print "<script>location='/post/".base_convert($post->parent_post_id, 10, 36)."/$parlink'</script>";
		unset($_SESSION[lpost]);
		$post=false;
	}
}else{
	print $res;
}



//$msg=($e==1)?"<span class='err'>$msg</span>":"<span class='ok'>$msg</span>";
//$text=($e==1)?$text:"";
//$name=($e==1)?$name:"";
//$link=($e==1)?$link:"";
//
//
//$tl=new types_list();
//$types=$tl->get_types_list(0, 1);
//
//$nrate=st_vars::$rate_new_post;
//$crate=User::get_rate();

function make_img($src){
	return "<img src=\"$src\" />";
}
?>
