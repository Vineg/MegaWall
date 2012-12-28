<?php
$user_id=User::get_c_id();
if(!$user_id){print "Необходимо войти"; exit;}


if(h2b($_POST[lpost])){
	$post=new Post();
	$post=unserialize($_SESSION[lpost]);
	if(!$post->id){ret("Ошибка1");}
	if(Post::get_author_id($post->id)==st_vars::$system_id){ret("Нельзя редактировать посты System");}
}else{
	$post = new Post();
	$post->params[newlink]="post_edit";
	$post->params[form]=Request::get_path($_SERVER["HTTP_REFERER"]);
	$post->source=$_POST['text'];
	$post->name=h2s($_POST['name']);
	$post->link=s2link::translit($_POST['link']);
	$post->parent_post_id=$_POST['parent']?$_POST['parent']:0;
	$post->params[brreplace]=h2b($_POST['brreplace']);
	$post->params[preview]=h2b($_POST['preview'])||h2b($_POST['button_name']=="prev");
	$post->params[save]=h2b($_POST['preview']);
	$post->author_id=h2i(User::get_id());
	$post->rate=Post::get_new_post_rate(User::get_c_id());
	$post->params["from"]=$_POST["from"];
	$post->pub=!h2b($_POST[unpub]);
	$post->params[scaptchar]=$_SESSION[scaptcha];
	$post->params[scaptcha]=$_POST[scaptcha];
	$post->params[upload]=h2b($_POST[upload]);
	$post->params[sourcelink]=h2b($_POST[sourcelink]);
	$post->id=h2i($_POST[id]);
	if(!$post->id){ret("Ошибка2");}
	if(Post::get_author_id($post->id)==st_vars::$system_id){ret("Нельзя редактировать посты System");}

	if(h2b($_POST[save])){
		$_SESSION["new_set"]=null;
		$_SESSION["new_set"][params][brreplace]=$post->params[brreplace];
		$_SESSION["new_set"][params]["from"]=$post->params["from"];
		$_SESSION["new_set"][pub]=!h2b($_POST['unpub']);
		$_SESSION["new_set"][params][save]=h2b($_POST[save]);
		$_SESSION["new_set"][params][upload]=h2b($_POST[upload]);
		$_SESSION["new_set"][params][sourcelink]=h2b($_POST[sourcelink]);
	}else{
		$_SESSION["new_set"]=null;
	}

	$ctype=0;
	while($_POST["s".$ctype]>0){
		if($ctype==$_POST["s".$ctype]){exit;}
		$ctype=$_POST["s".$ctype];
	}

	$post->type_id=$ctype;

	$post->process();

	if(is_string($post)){
		print $post; exit;
	}
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
	print "Ошибка4";
	exit;
}
//end

if($post){
	if(!$post->params[preview]){
		$ncont_id=Content::submit($post);
		$post_id = $post->id;
		if(User::get_c_id()==get_post_author($post_id)){
			$post_rate=Post::get_rate($post_id);
			$ccontent_id=Post::get_content_id($post_id);
			$ccontent_rate=Content::get_content_rate($ccontent_id);
			$rate=Content::get_content_rate($ncont_id);
			if($rate>$ccontent_rate+my_sqrt_z($post_rate/10)-0.05-my_sqrt_z(User::get_rate())){
				my_q("update post set content_id=$ncont_id where id=$post_id");
				$ut=true;
			};
		}else{
			$ut=check_contents($post_id);
		}
		//cho $post_id;
		$link=Post::get_uri_by_id($post_id);
		if(!$ut){
			$spost=new Post();
			$uid=User::get_id();
			$ulink=User::get_u_link_id($uid);
			$version=$post->version;
			$spost->version=0;
			$spost->text="<!--cid:$ncont_id-->$ulink предлагает изменить содержимое поста на такое: <a href=\"$link?v=$version\">link</a> и название поста на &laquo;$post->name&raquo;";
			$spost->parent_post_id=$post_id;
			$spost->rate=User::get_rate($user_id)*log(get_users_cnt()+1)/10;
			$spost->author_id=1;
			$spost->pub=-1;
			$spost->submit();
			print "<script>window.location='$link?v=$version'</script>";
		}else{
			print "<script>window.location='$link'</script>";
		}
	}else{
		$_SESSION[lpost]=serialize($post);
		//cho serialize($post);
	}
}else{
	print "Ошибка5";exit;
}

if($post){
	if($post->params[preview]==true){
		loc("/lpreview", true);
	}
}

?>
