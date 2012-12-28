<?php

function simple_post($post_content, $params=null){
	$scroll=$params[scroll];
	$post_content="<div class='spost-content $scroll'><div class=post-body>$post_content</div></div>";
	$post_content=build_post($post_content, $params);
	$post=build_post_block($post_content, $params);
	return $post;
}


function def_wall_post($res, $i, $vars=array()){
	
	if(is_string($res)){
		$query=$res;
		$res = my_q($query);
	}
	$post=new Post();
	if(my_n($res)<=$i){
		$post->text="Пост удалён, или ещё не создан.";
		$post->name="Нет такого поста.";
		if(vars::$debug){
			$post->text.=code($query);
		}
	}else{
		global $_vars;
		$path = $_SERVER['DOCUMENT_ROOT'];
		require_once "$path/settings/mysql_connect.php";
		$post->version=my_r($res, $i, "version");
// 		if(!$vars[fexist]){
// 			$post->content=my_r($res, $i, "fcontent");
// 		}else{
			$post->content = $vars[full]?my_r($res, $i, "fcontent"):my_r($res, $i, "content");
//		}
		$post->vars=unserialize(my_r($res, $i, "vars"));
		$post->parent_post_id = my_r($res, $i, "parent");
		$post->name = my_r($res, $i, "name");
		$post->author_id=my_r($res, $i, "author_id");
		$post->issafe=my_r($res, $i, "safe");
		$post->id=my_r($res, $i, "id");
		$post->rate=round(mysql_result($res, $i, "rate"), 3);
		if($vars[content_preview]){
			$post->cid=my_r($res, $i, "content_id");
			$post->content_preview=true;
		}
		$post->link=my_r($res, $i, "link");
		$post->type_id=my_r($res, $i, "type_id");
		$post->comments_num=get_comments_num($post->id);
		$post->date = date(mysql_result($res, $i, "date"));
		if($_vars[super]){
			$post->pub = my_r($res, $i, "pub");
		}
	}
	//$post_content=check_urls_h($post_content, $id);
//	if($comment==0){
//		$page->content=wall_post($post_content, $header, $date, $author_id, $pid, $rate, $link, $comments_num, $parent);
//	}else if($comment==1){
//		$page->content=wall_comment_post($post_content, $date, $author_id, $pid, $comments_num);
//	};
	return dif_wall_post_r($post, $vars);
}


function dif_wall_post_r(Post &$post, $vars=array()){
	if(!$post->content){
		$post->content=$vars[full]?$post->ftext:$post->text;
	}
	$post->main=$vars[main];
	$post->content_preview=$vars[content_preview];
	//$post_content=check_urls_h($post_content);
	$post->bdate = date("d.m.y",$post->date);
	$post->pid=base_convert($post->id, 10, 36);
	$post->type_name=get_type_full_name($post->type_id);
	$post->vars[full]=$vars[full];
	$post->type=$vars[type];
	if($vars[comment]==0){
		$content=wall_post($post, $vars);
	}else if($vars[comment]==true){
		$content=wall_comment_post($post);
	};
	return $content;
}

function post_edit(Post $post){
	$source=$post->source;
	$post_id=$post->id;
if(User::get_rate()<st_vars::$rate_no_captcha){
	$ht = Request::get_ht();
	$captcha=<<<EOQ
	<br />
	<img src="$ht/files/scaptcha.php" class="comp captcha" />
	<a class="reloadCaptcha right">Обновить картинку</a><br />
	<input name=scaptcha class="code small" placeholder="Капча" />
	<br />
EOQ;
}
	$page->content=<<<EOQ
<form class=async action=/func/post_edit.php>
<textarea id=mainArea style="width: 100%; " placeholder="Содержание." name="content" id="texted" class="big-textarea">$source</textarea>
<input name=id value='$post_id' type=hidden>
$captcha
<button class=submit>Отправить</button><span class=err></span>
</div>
EOQ;
	return simple_post($page->content);
}

function simple_post_delim($post_content){
	$post_content="<span>$post_content</span>";
	$post_content=build_post($post_content);
	$params[type]="delim";
	$post=build_post_block($post_content, $params);
	return $post;
}
?>