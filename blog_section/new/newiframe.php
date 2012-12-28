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
$ht=Request::get_ht();

$sv=st_vars::$script_version;

$page=new Page(PAGE_SIMPLE);
$page->title="Новый пост";

$page->head=<<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/jq.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/new.js?$sv"></script>
EOQ;




if(((!$_GET[version]||st_vars::$uscript_last_version>$_GET[version])&&!$_COOKIE[update_try])||($_GET[newbie]&&User::get_rate()>=st_vars::$rate_no_captcha)){
	sc("update_try", 1, 3600*24*7);
	$host=vars::$host;
	$notification="<script>if(confirm(\"Доступна новая версия скрипта MegawallUS. Установить?\")){window.location=\"http://$host/get/megawallus.user.js\"}</script>";
	sc("update_try", 1, 3600*24*7);
}

$crate=User::get_c_rate();

$ncrate=st_vars::$rate_new_comment;
$e=0;
$msg=($e==1)?"<span class='err'>$msg</span>":"<span class='ok'>$msg</span>";




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
if(!User::get_c_id()){
	$post_content="Необходимо <a href=/login_vk>войти</a>.";
}else if($crate>=$nrate){
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
		$text=h2s($post->source);
		$name=$post->name;
		$brreplace=b2hcb($post->params[brreplace]);
		$from=$post->params['from'];
		$unpub=b2hcb(!$post->pub);
		$ctype=$post->type_id;
		$link=$post->link;
		$save=b2hcb($post->params[save]);
		$upload=b2hcb($post->params[upload]);
		$sourcelink=b2hcb($post->params[sourcelink]);
	}else{
		$ctype=$_GET[type];
	}
	if($_GET[h]){
		$text=h2s(urldecode($_GET[h]));
		$name=h2s(urldecode($_GET[name]));
		$t2t=array("img"=>vars::$type_images, "mov"=>vars::$type_movie);
		$ctype=$t2t[$_GET[type]];
		$from=h2s($_GET[url]);
	}

	$tl=new types_list();
	$tl->selection=true;
	$tl->selected=$ctype;
	$types=$tl->get_types_list();

	if(User::get_c_rate()>=st_vars::$rate_upload){
		$uploadbox=<<<EOQ
			<li>
                Сохранить все файлы <input class=cb $upload type="checkbox" name="upload">
                </input>
            </li>
EOQ;
	}

	$post_content=<<<EOQ
	$notification
	<form action="$ht/func/new/new.php" method=post class="conp async">
            <textarea style="width:100%;" placeholder="Введите текст поста." name="text" id="texted" class="big-textarea">$text</textarea>
            <br/>
                    Введите название: 
                    <input class="ut conf" placeholder="название" name="name" value="$name">
                    </input>
    <br/><br/>
    Выберите раздел: 
    <br/>
    $types
    <ul class="olist">
        <a class="open">Дополнительные настройки</a>
        <div class="openobj hidden">
            <li>
                Ссылка: <input class="ut cont link" placeholder="ссылка" name="link" value="$link">
                </input>
            </li>
            <li>
                Заменять перенос строки на &laquo;&lt;br /&gt;&raquo; <input class=cb $brreplace type="checkbox" name="brreplace">
                </input>
            </li>
            $uploadbox
            <li>
                Код взят с сайта(или заменять &laquo;"/&raquo; на...) <input value="$from" placeholder="адрес" name="from" />
                </input>
            </li>
            <li>
                Не публиковать на главной <input class=cb $unpub type="checkbox" name="unpub">
                </input>
            </li>
            <li>
                Добавить ссылку на источник <input class=cb $sourcelink type="checkbox" name="sourcelink">
                </input>
            </li>
            <li>
                Сохранить настройки <input class=cb $save type="checkbox" name="save">
                </input>
            </li>
        </div>
    </ul>
    $capcha<br />
    <p>
	<input type=hidden name=iframe value=1></input>
	<input type=hidden name=iframebut value=$_GET[butn]></input>
    <button class=submit>Отправить</button><button class=def onClick='top.postMessage("mw_iframe_cmd=close_iframe","*"); return false;'>Отмена</button><br /><span class=err></span></p>
</form>
	
EOQ;
}else{
	$post_content="Необходимо иметь рейтинг больше, чем $nrate. Ваш текущий рейтинг:$crate<br /><br />
Оценивайте посты других пользователей, оставляйте свои комментарии и через некоторое время вы сможете писать свои.
";
}
$style="<style type='text/css'>
body{
	background:transparent;
	position:absolute;
	top:0;
}

</style>";

$script=<<<EOQ
<script>
window.onload=function(){
	sh();
}

$("body").bind("DOMSubtreeModified", function(){sh();});

function sh(){
	top.postMessage(
		"mw_iframe_height="+$(document).height(),
		"*"
	);
}
</script>
EOQ;
$page->content=$script.$style.simple_post($msg.$post_content);

def_page("<div style='position:relative; marginpadding:0; top:0; height:100%; width:100%;' >$page->content</div>", "", true);
?>