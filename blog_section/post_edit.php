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
$post_id=Page::get_post_id();
$ht=Request::get_ht();

$user_id=User::get_id();
if(!$user_id){l404();}

$sv=st_vars::$script_version;
$page->title="Редактирование";
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
if($crate>=st_vars::$rate_edit_post){
	$post=unserialize(Post::get_source($post_id));
	if($_GET[cont]!==null){
		if($_SESSION[lpost]){
			$post=unserialize($_SESSION[lpost]);
		}
	}
	$fill=true;
	
	if($fill){
		$text=h2s($post->source);
		if(!$text){
			$text=Post::get_source($post_id);
		}
		$name=$post->name;
		$brreplace=b2hcb($post->params[brreplace]);
		$from=$post->params['from'];
		$unpub=b2hcb(!$post->pub);
		$ctype=$post->type_id;
		$link=$post->link;
		$save=b2hcb($post->params[save]);
		$upload=b2hcb($post->params[upload]);
		$sourcelink=b2hcb($post->params[sourcelink]);
	}
	
	if($_GET[type]){
		$ctype=$_GET[type];
	}


	if(User::get_c_rate()>=st_vars::$rate_upload){
		$uploadbox=<<<EOQ
			<li>
                Сохранить все файлы <input class=cb $upload type="checkbox" name="upload">
                </input>
            </li>
EOQ;
	}

	$post_content=<<<EOQ
	<form action="$ht/func/post_edit.php" method=post class="conp async">
            <textarea style="width:100%;" placeholder="Введите текст поста." name="text" id="texted" class="big-textarea">$text</textarea>
            <br/>
                    Введите название: 
                    <input class="ut conf" placeholder="название" name="name" value="$name">
                    </input>
    <br/>

    <ul class="olist">
        <a class="open">Дополнительные настройки</a>
        <div class="openobj hidden">
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
    <input type=hidden name=id value=$post_id>
    </input>
    <button class=submit>Отправить</button><button name=prev class=submit>Предпросмотр</button><br /><span class=err></span></p>
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