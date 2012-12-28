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

$page=new Page();
$ht=Request::get_ht();
$user_id=User::get_id();
if(!$user_id){l404();}

$sv=st_vars::$script_version;

$page->title="Новый пост";
$page->head=<<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/jq.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/new.js?$sv"></script>
EOQ;

$name=h2s($name);
$link=s2link::translit($link);

$ctype=$_GET[type]?$_GET[type]:Type::get_main_type_id();

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
	$sourcelink="checked=true";
	if($_GET[cont]!==null){
		$post=unserialize($_SESSION[lpost]);
		$fill=true;
	}else if($_SESSION["new_set"]){
		//rint_r($_SESSION["new_set"]);
		$post=new Post();
		$post=set_class_vars($post, $_SESSION["new_set"]);
		$fill=true;
	}else{
		$fill=false;
	}
	if($fill){
		
		$text=h2s($post->source);
		$name=$post->name;
		$from=$post->params['from'];
		if(!$ctype){
			$ctype=$post->type_id;
		}
		$link=$post->link;
		$brreplace=b2hcb($post->params[brreplace]);
		$p = get_class_vars_r($post);
		if(isset($post->pub)){
			$p[unpub]=b2hcb(!$post->pub);
		}
		$p=array_merge($p, $post->params);
		$cbp = b2hcb($p);
		$p["from"]=$post->params["from"];
		//rint_r($p);
	}
	$select_type_block = Page::build_select_type_block($ctype);

	if(User::get_c_rate()>=st_vars::$rate_upload){
		$uploadbox=<<<EOQ
			<li>
                Сохранить все файлы <input class=cb $p[upload] type="checkbox" name="upload">
                </input>
            </li>
EOQ;
	}

	$post_content=<<<EOQ
	<form action="$ht/func/new/new.php" method=post class="conp async">
            <textarea style="width:100%;" placeholder="Введите текст поста." name="text" id="texted" class="big-textarea">$text</textarea>
            <br/>
                    Введите название: 
                    <input class="ut conf" placeholder="название" name="name" value="$name">
                    </input>
    <br/><br/>
    $select_type_block
    <ul class="olist">
        <a class="open">Дополнительные настройки</a>
        <div class="openobj hidden">
            <li>
                Ссылка: <input class="ut cont link" placeholder="ссылка" name="link" value="$p[link]">
                </input>
            </li>
            <li>
                Включить HTML <input class=cb $cbp[htmlon] type="checkbox" name="htmlon" value=1>
                </input>
            </li>
            <li>
                Включить форматирование текста <input class=cb $cbp[text_format] type="checkbox" name="text_format" value=1>
                </input>
            </li>
            $uploadbox
            <li>
                Код взят с сайта(или заменять &laquo;"/&raquo; на...) <input value="$p[from]" placeholder="адрес" name="from" />
                </input>
            </li>
            <li>
                Не публиковать на главной <input class=cb $cbp[unpub] type="checkbox" value=1 name="unpub">
                </input>
            </li>
            <li>
                Добавить ссылку на источник <input class=cb $cbp[sourcelink] type="checkbox" value=1 name="sourcelink">
                </input>
            </li>
            <li>
                Сохранить настройки <input class=cb $cbp[save] type="checkbox" value=1 name="save">
                </input>
            </li>
        </div>
    </ul>
    $capcha
    <p>
    	<button class=submit>Отправить</button><button name=prev class=submit>Предпросмотр</button><br /><span class=msg><span class="body err"></span></span>
    </p>
</form>
	
EOQ;
}else{
	$post_content="Необходимо иметь рейтинг больше, чем $nrate. Ваш текущий рейтинг:$crate<br /><br />
Оценивайте посты других пользователей, оставляйте свои комментарии и через некоторое время вы сможете писать свои.
";
}
$page->content=simple_post($msg.$post_content);

process_page($page);
?>