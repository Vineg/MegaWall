<?php
if (! function_exists ( "process_page" )) {
	function process_page(Page $page) {
		$page->ready();
		$page = render_page ( $page );
		return $page;
	}
}


function render_page(Page $page = null) {
	$ht = Request::get_ht ();
	require_once 'phpscr/user.php';
	$sv = st_vars::$script_version;
	if (! $page) {
		$page = new Page ();
	}
	
	if (Request::request_URI () == "/" && ! Page::get_page_user_id ()) {
		//$mylinks="<a href='/trash/'>Другое</a>|<a href='/pages'>третье</a>";
	}
	if (! User::get_id ()) {
		$ar = Ar::remvalue ( array ($mylinks, $links ), "" );
		//global $mwclient;
		
		global $mwclient;
		$mwlinks = $mwclient->return_links ( 1 );
// 		global $linkfeed;
// 		$llinks = $linkfeed->return_links ( 1 );
// 		global $sape;
// 		$links = $sape->return_links ( 2 );
		
		//define ( 'TRUSTLINK_USER', '739647d8d8be3c842f8a44d798fa90277edfb0f0' );
		//require_once ($_SERVER ['DOCUMENT_ROOT'] . '/links_exchange/' . TRUSTLINK_USER . '/trustlink.php');
		//$o ['charset'] = 'UTF-8'; //кодировка сайта
		//$trustlink = new TrustlinkClient ( $o );
		//$spam = $trustlink->build_links ();
		//$page->spam = $spam;
		$links = join ( "|", Ar::remvalue ( array ($mylinks, $links, $llinks, $mwlinks ), "" ) );
		$page->footer = $links ? "aлсо " . $links : "";
	}
	$page->dtime = Timer::end_time ();
	
	$page->head .= <<<EOQ
<script type="text/javascript" src="http://vkontakte.ru/js/api/share.js?11" charset="windows-1251"></script>
<script type="text/javascript" src="$ht/files/jscripts/share.js?$sv"></script>
<!--<script type="text/javascript">
	$(document).ready(function(){
		InitMouseDown();
	});
	function InitMouseDown () {
		if (document.createEventObject) {   // IE before version 9
			var mousedownEvent = document.createEventObject (window.event);
			mousedownEvent.button = 1;  // left button is down
			$("#spblock a").each(function(n,obj){obj.fireEvent ("onmouseover", mousedownEvent);});
		}else{
			var mousedownEvent = document.createEvent ("MouseEvent");
             mousedownEvent.initMouseEvent ("mouseover", false, true, window, 0, 
                                                12, 13, Math.floor(50+Math.random()*10),  Math.floor(80+Math.random()*10), 
                                                false, false, false, false, 
                                                0, null);
            $("#spblock a").each(function(n,obj){obj.dispatchEvent (mousedownEvent);});
			$("#spblock a").click(function(){return false;});
			//alert(1);
			
			var clickEvent = document.createEvent ("MouseEvent");
            clickEvent.initMouseEvent ("click", false, true, window, 0, 
                                                12, 13, Math.floor(50+Math.random()*10),  Math.floor(80+Math.random()*10), 
                                                false, false, false, false, 
                                                0, null);
           // $("#spblock a").each(function(n,obj){obj.dispatchEvent (mousedownEvent);});
			
			$("#spblock a").each(function(n,obj){
			obj.dispatchEvent (clickEvent);
			obj = $(obj);
			var link = $(obj).attr("href");
			var text = $(obj).text();
			obj.replaceWith("<a target=_top href=\""+link+"\">"+text+"</a>");
			//$("body").prepend("init");
			});
		}
	}
</script>-->
EOQ;
	
	$metrika_id=vars::$metrika_id[$_SERVER[HTTP_HOST]];
	
	if($metrika_id){
	$page->counter = $page->counter ? $page->counter : <<<EOQ
<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter$metrika_id = new Ya.Metrika({id:$metrika_id, enableAll: true, webvisor:true});
        }
        catch(e) { }
    });
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/$metrika_id" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
EOQ;
	}
	
	$page->shareblock = <<<EOQ
	<div>
		<script type="text/javascript">
document.write(VK.Share.button(false,{type: "round_nocount", text: "Сохранить"}));
		</script>
	</div>
	<div style="margin-top:3px;">
		<g:plusone size="medium"></g:plusone>
	</div>
EOQ;
	
	$user_id = User::get_id ();
	$login = User::get_login ();
	$loginstr = $_COOKIE [registered] ? "Вход" : "Вход/Регистрация";
	if ($page->title) {
		$page->head .= "<title>$page->title</title>";
	}
	$ht = Request::get_ht ();
	$pht = Request::get_pht();
	if (! $user_id) {
		$headad = <<<EOQ
	<li>
		<span class="separator"></span>
	</li>
	<li class="page_item">
		<a href="$ht/about" title="О сайте" class="amtext"><span><span>О 
сайте</span></span></a>
	</li>
	<li class="page_item">
		<a href="$pht/login_vk" title="Вход/Регистрация" class="amtext"><span><span>$loginstr
</span></span></a>
	</li>
EOQ;
//	<li class="page_item">
//		<a href="$ht/chat" title="Чат" class="amtext"><span><span>Чат</span></span></a>
//	</li>
	} else {
		$ul = User::get_url ( $login );
		$type_id = Type::get_ctype_id();
		if ($type_id == vars::$type_images) {
			$newlink = "newimg";
		
		//$newname="Новая картинка";
		} else {
			if ($type_id && $type_id != Type::get_main_type_id()) {
				$type = "?type=$type_id";
			}
			$newlink = "new$type";
		
		//$newname="Новый пост";
		}
		$uid = User::get_id ();
		$ncnt = my_qn ( "select * from notification where user_id=$uid and readed<1" );
		$ncnt = $ncnt ? " ($ncnt)" : "";
		$headad = <<<EOQ
	<li class="page_item">
		<a href="$pht/logout" title="Выход" class="amtext"><span><span>Выход</span></span></a>
	</li>
	<li class="info_text">
		<span class="amtext"><span><span><a href="$ul" class="ok">$login
</a></span></span></span>
	</li>
	<li class="page_item">
		<a href="$ht/$newlink" title="Новый пост" class="amtext"><span><span>Новый 
пост</span></span></a>
	</li>
	<li class="page_item">
		<a href="$ht/notifications" title="Оповещения" 
class="amtext"><span><span>Оповещения $ncnt</span></span></a>
	</li>
EOQ;
	}
	//end
	

	$menu = <<<EOQ
<li>
   <a href="/" class="amtext"><span><span>Главная</span></span></a>
</li>
<li>
   <span class="separator"></span>
</li>
<li>
   <span class="separator"></span>
</li>
$headad
<li>
   <span class="separator"></span>
</li>
EOQ;
	
	//$in=file_get_contents("./templates/ultimate.html");
	

	//if(!empty($in)){
	//	$cond=$in;
	//	$reg='[{$]([a-zA-Z_0-9]+)[}]';
	//	while(ereg($reg,$cond,$carr)){
	//		$from="{\\\$$carr[1]}";
	//		$to=${$carr[1]};
	//		$reg1=$from;
	//		$cond=ereg_replace($reg1, "$to", $cond);
	//		//cho ($from=="\$page->dtime")?$to:"";
	//	}
	//	print $cond;
	//
	//	//	cho ereg_replace('\$script([^a-zA-Z_0-9])', "1", " \$script ");
	//}else{
	//	print false;
	//}
	

	//$in=replace_php_vars($in, array(menu=>$menu, content=>$page->content, dtime=>$page->dtime, 
	//settings_block=>$page->settings_block, types_block=>$page->types_block, archive=>$page
	//->archive_block, head=>$page->head, ht=>$ht, style_version=>st_vars::$style_version));
	//print $in;
	$page->menu = $page->menu ? $page->menu : $menu;
	if ($_GET [t]) {
		$tid = pid2id ( $_GET [t] );
		if ($tid) {
			$data = Post::get_content ( pid2id ( $_GET [t] ) );
		}
		$bad = "
		<div class='mw_topbox' style=\"z-index:10000;position:fixed; width:100%; height:100%; background:black; opacity:0.6; marginpadding:0; top:0; left:0;\"></div>
		<div class='mw_topbox' style=\"overflow:auto;z-index:10001;position:fixed; width:100%; height:100%; marginpadding:0; top:0; left:0;\">
			<div onMouseMove='init(this);' id=\"mw_topbox\" style=\"display:table; z-index:10002;padding:0 0 0 0; background:white; margin:auto; margin-top:50px;opacity:1;\">
				<div style='position:relative; width:100%; height:100%; margin:50px 50px 0 50px;'>
					$data
				</div>
				<div style='width:100%; height:80px; position:relative;'><a class=topboxclose style='position:absolute; right:10px; bottom:10px;'><img alt='close' src=\"/files/templates/ultimate/images/closelabel.gif\" /></a></div>
				</div>
			</div>
		</div>
		<style>
		body{
			overflow:hidden;
		}
		</style>
		";
		$page->bodyad .= $bad;
	}
	include "templates/ultimatep.html";

		//foreach($time as $pnt=>$tm)
//		print "$pnt:".round($time[$i]-$time[$i-1], 3)."<br />";
//		$ppn
//	}
}

function wall_post(Post $post, $vars=array()) {
	//init vars
	global $_vars;
	//cho $post->pub;
	$section_name = $vars[section_name]?$vars[section_name]:"post";
	$rate = $post->rate;
	$ht = Request::get_ht () . "";
	$pid = $post->pid;
	$link = $post->link;
	$ver = ($post->content_preview && ! $post->main) ? "?v=$post->version" : "";
	$author = User::get_login ( $post->author_id );
	$bh = ($post->comments_num < 1) ? "shadow" : "";
	$comments_num = ($post->comments_num > 0) ? $post->comments_num : "нет";
	$type_name = $post->type_name;
	$type_name = $post->type_name ? "<span class='shadow small'>($type_name)</span>" : "";
	
	if (! $post->pid) {
		$plink = "#";
	} else {
		$plink = "$ht/$section_name/$pid/$link$ver";
	}
	$bdate = $post->bdate;
	//end
	global $u;
	$post_content .= $post->content;
	require_once "phpscr/user.php";
	require_once "sys/vars.php";
	//cho $post->link;
	if($post->pub<1&&$_vars[super]==true){
		$header_style="color:red";
	}
	if ($post->parent_post_id == 0) {
		$headern = ($post->name == "") ? "Без названия" : $post->name;
		$header = <<<EOQ
		<a href="$plink" style="$header_style">
	    	<span class="PostHeader">
		$headern
			</span>
			
		</a>
EOQ;
	} else {
		$parent_name = get_post_name ( $post->parent_post_id );
		$headern = ($parent_name == "") ? "посту" : "\"$parent_name\"";
		$parent_pid = base_convert ( $post->parent_post_id, 10, 36 );
		$parent_link = get_post_link ( $post->parent_post_id );
		$header = <<<EOQ
		Комментарий к
		<a href="$ht/post/$parent_pid/$parent_link"><span class="PostHeader">$headern</span></a>
EOQ;
	}
	
	$sharetitle = addslashes ( $headern );
	//$sharedescr = str_replace ( "\n", "", escape ( shorten_string ( get_text ( $post_content ), 10000, true ) ) );
	$sharelink = str_replace ( "\n", "", escape ( $plink ) );
	//$shareimg = get_image($post_content);
	if ($post->vars [fexist] && ! $post->vars [full]) {
		$post_content .= "<br /><br /><center><a href='$plink' class=noshare>Читать полностью</a></center>";
	}
	$path = $_SERVER ['DOCUMENT_ROOT'];
	$user_id = User::get_id ();
	$login = User::get_login ( $user_id );
	if (User::get_id () != false) {
		$user_rate = User::get_rate ();
		if ($user_rate >= st_vars::$rate_delete || User::get_id () == $post->author_id) {
			$xbuton .= <<<EOQ
			<td>
				<button class="delete"></button>
			</td>
EOQ;
		}
		if ($user_rate >= st_vars::$rate_edit_post && $post->vars [full]) {
			$ebuton .= <<<EOQ
			<td>
				<button class="edit"></button>
			</td>
EOQ;
		}
		if ($post->content_preview) {
			$cid = "cid=" . $post->cid;
		} else {
			$hpid = "pid=$pid";
		}
		$postpanel = <<<EOQ
		<tr $hpid $cid>
			<td class="vmsg">
			</td>
			$ebuton
			<td>
				<button class="voteup"></button>
			</td>
			<td>
				<button class="votedn"></button>
			</td>
			$xbuton
		</tr>
EOQ;
	}
	$al = User::get_url ( $author );
	//		$vkshare=<<<EOQ
	//<a style="text-decoration:none;" onclick="return VK.Share.click(0, this);" onmouseup="this._btn=event.button;this.blur();" href="http://vkontakte.ru/share.php?url=http%3A%2F%2Fd1.megawall.ru%2F">
	//<img style="vertical-align: middle;border:0;" src="https://vk.com/images/vk16.png">
	//</a>
	//EOQ;
	

	$vkshare = <<<EOQ
<span class=right>
	<script type="text/javascript">
	document.write(getShareButton("$pid","$sharetitle","$sharelink"));
	</script>
</span>
EOQ;
	$content = <<<EOQ
<div class=PostHeader>
	<div>
		<table class=PostPanel>
		$postpanel
		</table>
  		<h2 class="PostHeaderIcon-wrapper">
		$header
   		$type_name
   		</h2>
	</div>
    <div class="PostHeaderIcons metadata-icons">
		$bdate | Автор: <a href="$al">$author</a>
    </div>
</div>
<div class="PostContent">
    $post_content
</div>
<div class="cleared">
</div>
<div class="PostFooterIcons metadata-icons">
        <a href="$plink" class='$bh'>комментарии: $comments_num</a> | рейтинг:$rate$vkshare
</div>

EOQ;
	$post_content = build_post ( $content );
	$post = build_post_block ( $post_content, array (pid => $pid, type => $post->type ) );
	return $post;
}

function build_post($post_content, $params = array()) {
	$scroll = "$params[scroll]";
	return <<<EOQ
<div class="Post-tl"></div>
<div class="Post-tr">
	<div></div>
</div>
<div class="Post-bl">
	<div></div>
</div>
<div class="Post-br">
	<div></div>
</div>
<div class="Post-tc">
	<div></div>
</div>
<div class="Post-bc">
	<div></div>
</div>
<div class="Post-cl">
	<div></div>
</div>
<div class="Post-cr">
	<div></div>
</div>
<div class="Post-cc"></div>
<div class="Post-body $scroll" onMouseMove='init(this);'>
	<div class="Post-inner article">
		$post_content <div class="cleared"></div>
	</div>
</div>
	
EOQ;
}

function build_post_block($block_content, $params = array()) {
	if ($params [header]) {
		$header = "<div class=header>$params[header]</div>";
	}
	$type = $params [type];
	$scroll = " $params[scroll]";
	//$pid = $params [pid] ? "pid=$params[pid]" : "";
	$id = $params [pid] ? "id=\"p$params[pid]\"" : "";
	return <<<EOQ
<div class="Post$type" $pid $id>
	$header
	$block_content
</div>
EOQ;
}

function get_obul($opened) {
	$dn = $opened ? "dn" : "up";
	return <<<EOQ
<button type=button class='bullet $dn open'></button>
EOQ;
}

function wall_comment_post(Post $post) {
	global $u;
	require_once "phpscr/user.php";
	require_once "sys/vars.php";
	$bh = ($post->comments_num < 1) ? "shadow" : "";
	//cho $post->comments_num>0;
	$comments_num = ($post->comments_num > 0) ? $post->comments_num : "нет";
	$path = $_SERVER ['DOCUMENT_ROOT'];
	$user_id = User::get_id ();
	$login = User::get_login ( $user_id );
	$author = User::get_login ( $post->author_id );
	$pid = $post->pid;
	$user_rate = User::get_rate ();
	if (User::get_id () != false) {
		if ($user_rate >= st_vars::$rate_delete || $user_id == $post->author_id) {
			$xbut = <<<EOQ
			<td>
				<button class="delete" type=button></div>
			</td>
EOQ;
		}
		$postpanel = <<<EOQ
		<tr pid=$pid rem=3>
			<td class="vmsg">
			</td>
			<td>
				<button class="voteup" type=button></div>
			</td>
			<td>
				<button class="votedn" type=button></div>
			</td>
		$xbut
		</tr>
EOQ;
	
	}
	$ht = Request::get_ht ();
	$comblock = "<a href='$ht/post/$post->pid/$post->link'>комментарии: $comments_num</a>";
	$al = User::get_url ( $author );
	$content = <<<EOQ
<div class="Post-inner article">
	<div class=PostHeader>
		<div>
			<table class=PostPanel>
			$postpanel
			</table>
		    <h2 class="PostHeaderIcon-wrapper">
				<a href="$al"><i>$author:</i></a>
		    </h2>
		</div>
	</div>
	<div>
	    <div class="PostHeaderIcons metadata-icons">
			$post->bdate
	    </div>
	</div>
    <div class="PostContent">
   	 $post->content
    </div>
    <div class="cleared">
    </div>
    <div class="PostFooterIcons metadata-icons">
        $comblock
    </div>
</div>

EOQ;
	
	$post_content = build_post ( $content );
	$post = build_post_block ( $post_content, array (pid => $pid ) );
	return $post;
}
?>