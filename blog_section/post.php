<?php
require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once 'phpscr/functions.php';
global $_vars;
//init vars
$post_id=Page::get_post_id();
$tl=new types_list();
$ctype = Post::get_type_id($post_id);
$_vars[type_id]=$ctype;
$types_list=$tl->get_types_list($ctype);
$apiid=vars::$vk_api_id;
$page=new Page();
//end

//cho Type::get_main_type_id();
if(Post::get_main_type_id($post_id)!=Type::get_main_type_id()&&!$_vars[noindex]){
	l404();
}

$content_preview=$_GET[versions]!==null||$_GET[v]?1:0;

if($_GET[edit]!==null){include "blog_section/post_edit.php";
	exit;
}

if($_GET[edit]===null){
	//build main post
	$pub = $_vars[super]?"":"and pub>=-1";
	if(!($_GET[v]!==null)){
		$quer="select * from ".Post_const::$post_select." where id=$post_id $pub LIMIT 0, 1";
	}else{
		$quer="select * from ".Post_const::$content_select." where id=$post_id and version='$_GET[v]' $pub LIMIT 0, 1";
		//cho $quer;
	}
//if(vars::$debug){
////cho $quer;
//}
	$res=my_q($quer);
	$curent_post_content_id=my_fst($res, "content_id");
	if(my_n($res)>0){
		$page->content.=def_wall_post($res, 0, array("content_preview"=>$content_preview, "full"=>1, "main"=>1));
	}else{
		l404();
	}
	//end
	$title=my_fst($res, "name");
	$title=($title=="")?"Без названия":$title;
}


if($_GET[versions]!==null){
	$adjs="<script type='text/javascript' src='$ht/files/jscripts/type_prev.js'></script>";
	//build versions
	$ppp=15;
	$cpage=max($_GET["p"],1);
	$rows=my_qn("select * from post_content where post_id='$post_id'");

	if($rows>1){
		$page->content.=simple_post_delim("<h3 class=middle>Варианты этого поста</h3>");
	}


	$maxpage=ceil($rows/$ppp);
	$opagesblock=page_block($maxpage, $cpage);
	$opagesblock="<div class='spage wb'>$opagesblock</div>";



	$begin = ($cpage-1)*$ppp;
	$quer="select * from ".Post_const::$content_select." where id=$post_id ORDER BY rate DESC LIMIT $begin, $ppp";
	//	global $page->content;
	//	$page->content.="<textarea>$quer</textarea>";
	$res=my_q($quer);
	$crows=my_n($res);
	for($i=0; $i<$crows; $i++){
		$post_content_id=my_r($res, $i, "content_id");
		if($post_content_id!=$curent_post_content_id){
			$page->content.=def_wall_post($res, $i, array("content_preview"=>true));
		}
	}

	$page->content.=$opagesblock;
	//end
}else{
	$adjs="<script type='text/javascript' src='$ht/files/jscripts/post.js'></script>";
	//build comments
	$ppp=15;
	$cpage=max($_GET["p"],1);
	$rows=my_qn("select * from post where parent='$post_id' and pub>=-1;");

	if($rows>0){
		$page->content.=simple_post_delim("<h3 class=middle><span>Комментарии</span></h3>");
	}


	$maxpage=ceil($rows/$ppp);
	$opagesblock=page_block($maxpage, $cpage);
	$opagesblock="<div class='spage wb'>$opagesblock</div>";



	$begin = ($cpage-1)*$ppp;
	$quer="select * from ".Post_const::$post_select." where parent='$post_id' and pub>=-1 ORDER BY rate DESC LIMIT $begin, $ppp";
	$res=my_q($quer);
	$crows=my_n($res);
	$page->content.="<div class=comments>";
	for($i=0; $i<$crows; $i++){
		$page->content.=def_wall_post($res, $i, array("comment"=>true));
	}
	$page->content.="</div>";

	$page->content.=$opagesblock;
	//end

	//new comment
	$crate=User::get_rate();
	$rate_new_comment=st_vars::$rate_new_comment;
	if(!User::get_id()){
		$post_content=<<<EOQ
		Чтобы оставлять комментарии необходимо войти.<div id="vk_auth" style="margin:auto; width:100%; margin-top:20px;"></div>
<script type="text/javascript">
VK.Widgets.Auth("vk_auth", {width: "600px", authUrl: '/st_func/login_vk.php'});
</script>
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?31"></script>

<script type="text/javascript">
  VK.init({apiId:$apiid});
</script>
EOQ;
	}else{
		if($crate>=$rate_new_comment){
			$post_content=<<<EOQ
	<form action="$ht/func/new/new.php" method=post class="conp new-form async">
	<h3>Новый комментарий:</h3>
	<textarea placeholder="Введите текст комментария" name="text" id="texted" class="middle-textarea">$text</textarea> <br />	
	<input type=hidden name=parent value='$post_id'></input>
	<input type=hidden name=brreplace value='on'></input>
	<p><button class=submit>Отправить</button><span class=err></span></p>
	</form>
EOQ;
		}else{
			$post_content=<<<EOQ
	Чтобы оставлять комментарии вам нужно иметь рейтинг больше $rate_new_comment, у вас сейчас $crate.
EOQ;
		}
	}
	$page->content.=simple_post($post_content);
	//end
}


$page->title=$title;
$page->head=<<<EOQ

<script type="text/javascript" src="$ht/files/jscripts/functions.js"></script>
$adjs
<script type="text/javascript" src="$ht/files/jscripts/objectsinit.js"></script>
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?31"></script>

<script type="text/javascript">
  VK.init({apiId:$apiid});
</script>
EOQ;

process_page($page);
?>
