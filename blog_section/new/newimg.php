<?php
require_once "phpscr/post.php";
require_once "phpscr/shortcuts.php";
require_once "phpscr/user.php";
require_once "phpscr/get_types_list.php";
require_once 'phpscr/functions.php';
require_once "phpscr/html_filter.php";
require_once "phpscr/kses.php";
require_once 'settings/mysql_connect.php';
require_once 'sys/vars.php';
require_once "phpscr/time.php";

$user_id=User::get_id();
if(!$user_id){l404();}

$page=new Page();

$sv=st_vars::$script_version;

$page->title="Новая картинка";

$page->head=<<<EOQ



<script type="text/javascript" src="$ht/files/jscripts/new.js?$sv"></script>
EOQ;

$text0=$_POST['text'];
$text=$text0;
$name=$_POST['name'];
$link=$_POST['link'];
$parent=$_POST['parent']?$_POST['parent']:0;
$brreplace=$_POST['brreplace'];

$name=h2s($name);
$link=s2link::translit($link);
$ctype=0;

while($_POST["s".$ctype]>0){
	if($ctype==$_POST["s".$ctype]){break;}
	$ctype=$_POST["s".$ctype];
}

$crate=User::get_c_rate();

$ncrate=st_vars::$rate_new_comment;
$e=0;
$msg=($e==1)?"<span class='err'>$msg</span>":"<span class='ok'>$msg</span>";

$text=($e==1)?$text:"";
$name=($e==1)?$name:"";
$link=($e==1)?$link:"";




$nrate=st_vars::$rate_new_post;
$crate=User::get_rate();
if($crate<st_vars::$rate_no_captcha){
	$capcha=<<<EOQ
	<img src="$ht/files/scaptcha.php" class="comp captcha" />
	<a class="reloadCaptcha right">Обновить картинку</a><br />
	<input name=scaptcha class="code small" placeholder="Капча" />
	<br />
EOQ;
}
if($crate>=$nrate){
	$text=str_ireplace("\n", "", htmlspecialchars($text));
	$brreplace="checked=true";
	if($_GET[cont]!==null){
		$post=unserialize($_SESSION[lpost]);
		$fill=true;
	}else if($_SESSION["new_set"]){
		$post=new Post();
		$post=set_class_vars($post, $_SESSION["new_set"]);
		$fill=true;
	}
	if($fill){
		$img_src=$post->params[img_src];
		$name=$post->name;
	}else{
		$ctype=$_GET[type];
	}

	$tl=new types_list();
	$tl->selection=true;
	$tl->selected=$ctype;
	$types=$tl->get_types_list();

	$post_content=<<<EOQ
	<form action="$ht/func/new/newimg.php" method=post class="async conp newForm">
            Ссылка на картинку:<input placeholder="ссылка" name="img_src" value="$img_src" />
            <br/>
                    Название: 
                    <input class="ut conf" placeholder="название" name="name" value="$name">
                    </input>
                    <br />
    $capcha
    <p><button class=submit>Отправить</button><button name=prev class=submit>Предпросмотр</button><br /><span class=err></span></p>
</form>
	
EOQ;
}else{
	$post_content="Необходимо иметь рейтинг больше, чем $nrate. Ваш текущий рейтинг:$crate<br /><br />
Оценивайте посты других пользователей, оставляйте свои комментарии и через некоторое время вы сможете писать свои.
";
}
$page->content=simple_post($msg.$post_content, array(header=>"Новая картинка"));

process_page($page);
?>