<?php
//init
function process_page($page=false){
	require_once 'phpscr/user.php';
	$page->dtime=Timer::end_time();
	$user_id=User::get_id();
	$login=User::get_login();
	$loginstr=$_COOKIE[registered]?"Вход":"Вход/Регистрация";
	if($page->title){
		$page->head.="<title>$page->title</title>";
	}
	if(!$user_id){
		$ht=Request::get_ht();
		$headad=<<<EOQ
		
	<li>
		<span class="separator"></span>
	</li>
	<li class="page_item">
		<a href="$ht/about" title="О сайте" class="amtext"><span><span>О 
сайте</span></span></a>
	</li>
	<li class="page_item">
		<a href="$ht/login_vk" title="Вход/Регистрация" class="amtext"><span><span>$loginstr
</span></span></a>
	</li>
	<li class="page_item">
		<a href="$ht/chat" title="Чат" class="amtext"><span><span>Чат</span></span></a>
	</li>
EOQ;
	}else{
		$ul=User::get_url($login);
		global $type_id;
		if($type_id==vars::$type_images){
			$newlink="newimg";
			//$newname="Новая картинка";
		}else{
			if($type_id&&$type_id!=1){
				$type="?type=$type_id";
			}
			$newlink="new$type";
			//$newname="Новый пост";
		}
		$uid=User::get_id();
		$ncnt=my_qn("select * from notification where user_id=$uid and readed<1");
		$ncnt=$ncnt?" ($ncnt)":"";
		$headad=<<<EOQ
	<li class="page_item">
		<a href="$ht/logout" title="Выход" class="amtext"><span><span>Выход</span></span></a>
	</li>
	<li class="info_text">
		<a href="$ul" class="ok">$login</a>
	</li>
	<li class="page_item">
		<a href="$ht/$newlink" title="Новый пост" class="amtext">Новый 
пост</a>
	</li>
	<li class="page_item">
		<a href="$ht/notifications" title="Оповещения" 
class="amtext">Оповещения $ncnt</a>
	</li>
EOQ;
	}
	//end

	$menu=<<<EOQ
	<li class="page_item">
	   <a href="$ht/">Главная</a>
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
	
	$test=1;
	$in=file_get_contents("./files/templates/punta.html");

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
	$in=replace_php_vars($in, array(menu=>$menu, content=>$page->content, dtime=>$page->dtime, 
settings=>$page->settings_block, types_list=>$page->types_block, archive=>$page
->archive_block, head=>$page->head, ht=>$ht, style_version=>st_vars::$style_version));
	print $in;

	//foreach($time as $pnt=>$tm)
	//		print "$pnt:".round($time[$i]-$time[$i-1], 3)."<br />";
	//		$ppn
	//	}
	
		
}



function build_post($post_content, $params=""){
$scroll="$params[scroll]";
return<<<EOQ
	$post_content
EOQ;
}

function build_post_block($block_content, $params=array()){
	if($params[header]){
		$header="<div class=header>$params[header]</div>";
	}
	$type=$params[type];
	$scroll=" $params[scroll]";
	$pid=$params[pid]?"pid=$params[pid]":"";

	return <<<EOQ
<div class="post$type" $pid onMouseMove='init(this);'>
	$header
	$block_content
</div>
EOQ;
}

function get_obul($opened){
	$text=$opened?"&ndash;":"+";
	return "<div class='obul open'>+</div>";
}

function wall_comment_post(Post $post){
	global $u;
	require_once "phpscr/user.php";
	require_once "sys/vars.php";
	$bh=($post->comments_num<1)?"shadow":"";
	//cho $post->comments_num>0;
	$comments_num=($post->comments_num>0)?$post->comments_num:"нет";
	$path = $_SERVER['DOCUMENT_ROOT'];
	$user_id=User::get_id();
	$login=User::get_login($user_id);
	$author=User::get_login($post->author_id);
	$pid=$post->pid;
	$user_rate=User::get_rate();
	if(User::get_id()!=false){
		if($user_rate>=st_vars::$rate_delete||$user_id==$post->author_id){
			$xbut=<<<EOQ
			<td>
				<button class="delete" type=button></div>
			</td>
EOQ;
		}
		$postpanel=<<<EOQ
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
		$comblock=($post->comments_num>0)?"<a href='/post/$pid/$link'>комментарии: $comments_num</a>":"";
		$al=User::get_url($author);
		$page->content=<<<EOQ
<div class="post-content">
	<div class="post-body">
		<div class=post-header>
			<div>
				<table class=post-panel>
					$postpanel
				</table>
			    <h2 class="PostHeaderIcon-wrapper">
					<a href="$al"><i>$author:</i></a>
			    </h2>
			</div>
		</div>
		<div>
			$post->content
		</div>
	</div>
</div>
<div class=post-footer>
	<div class="post-info">
		<a href="$h/post/$post->pid/$post->link" class="$bh">комментарии: $comments_num</a><span class=right>$post->bdate</span>
	</div>
</div>
EOQ;


		$post_content=build_post($page->content);
		$post=build_post_block($post_content, array(pid=>$pid));
		return $post;
}

function wall_post(Post $post){
	//init vars
	$rate=$post->rate;
	$ht=Request::get_ht();
	$pid=$post->pid;
	$link=$post->link;
	$ver=($post->content_preview&&!$post->main)?"?v=$post->version":"";
	$author = User::get_login($post->author_id);
	$bh=($post->comments_num<1)?"shadow":"";
	$comments_num=($post->comments_num>0)?$post->comments_num:"нет";
	$type_name=$post->type_name;
	$type_name=$post->type_name?"<span class='shadow small'>($type_name)</span>":"";

	if(!$post->pid){
		$plink="#";
	}else{$plink="$ht/post/$pid/$link$ver";
	}
	$bdate=$post->bdate;
	//end
	global $u;
	$post_content.=$post->content;
	require_once "phpscr/user.php";
	require_once "sys/vars.php";
	//cho $post->link;
	if($post->parent_post_id==0){
		$headern=($post->name=="")?"Без названия":$post->name;
		$header=<<<EOQ
		<a href="$plink">
	    	<span class="PostHeader">
		$headern
			</span>
			
		</a>
EOQ;
	}else{
		$parent_name=get_post_name($post->parent_post_id);
		$headern=($parent_name=="")?"посту":"\"$parent_name\"";
		$parent_pid=base_convert($post->parent_post_id, 10, 36);
		$parent_link=get_post_link($post->parent_post_id);
		$header=<<<EOQ
		Комментарий к
		<a href="$ht/post/$parent_pid/$parent_link"><span class="PostHeader">$headern</span></a>
EOQ;
	}
	if($post->vars[fexist]&&!$post->vars[full]){
		$post_content.="<br /><br /><center><a href='$plink' class=noshare>Читать полностью</a></center>";
	}
	$path = $_SERVER['DOCUMENT_ROOT'];
	$user_id=User::get_id();
	$login=User::get_login($user_id);
	if(User::get_id()!=false){
		$user_rate=User::get_rate();
		if($user_rate>=st_vars::$rate_delete||User::get_id()==$post->author_id){
			$xbuton.=<<<EOQ
			<td>
				<button class="delete"></button>
			</td>
EOQ;
		}
		if($user_rate>=st_vars::$rate_edit_post&&$post->vars[full]){
			$ebuton.=<<<EOQ
			<td>
				<button class="edit"></button>
			</td>
EOQ;
		}
		if($post->content_preview){
			$cid="cid=".$post->cid;
		}else{
			$hpid="pid=$pid";
		}
		$postpanel=<<<EOQ
		<tr $hpid $cid>
		$ebuton
			<td>
				<button class="voteup"></button>
			</td>
			<td>
				<button class="votedn"></button>
			</td>
		$xbuton
			<td class="vmsg">
			</td>
		</tr>
EOQ;
	}
		$al=User::get_url($author);
		$content=<<<EOQ
<div class="post-content">
<div class=post-head>
<div>
    <h2 class="PostHeaderIcon-wrapper">
		$header
    $type_name
    </h2>
</div>
</div>
<div class="post-body">
    $post_content
</div>
</div>
    
    <div class="post-footer">
    	<div class="post-info">
    		<table class="w100 invis mid">
    			<tbody>
    				<tr>
    					<td class="w0 nowrap">
				        	<a href="$plink" class='$bh'>комментарии: $comments_num</a> | рейтинг:$rate
						</td>
						<td class="w0 nowrap">
							<table class=post-panel>
								$postpanel
							</table>
						</td>
						<td class=w100>
						<div class=w100>
						</div>
						</td>
						<td class="w0 nowrap">
							$bdate | Автор: <a href="$al">$author</a>
    					</td>
    				</tr>
    			</tbody>
    		</table>
        </div>
    </div>
EOQ;
		$post_content=build_post($content);
		$post=build_post_block($post_content, array(pid=>$pid, type=>$post->type));
		return $post;
}
?>