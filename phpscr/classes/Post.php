<?php
class Post {
	public $bdate, $cid, $issafe, $name, $text, $ftext, $pub, $id, $pid = 0, $content_preview, $rate, $author_id, $link, $type_id, $comments_num, $date, $content, $type_name, $vars = array (), $parent_post_id, $source, $params = array (), $version, $stack_new, $stack_pub;
	//	static function get_post_content($id){
	//		return my_r(mysql_query("select * from post where id='$id'"), 0, "content");
	//	}
	private $processed = false;

	function Post($post_id = null, $vars = array()) {
		$this->params = array ();
		$this->parent_post_id = 0;
		$this->author_id = User::get_c_id ();
		$this->pub = - 3;
		if ($post_id) {
			$postsel = Post_const::$post_select;
			$res = my_q ( "select * from $postsel where id='$post_id'" );
			$i = 0;
			if (my_n ( $res ) < $i + 1) {
				$this->text = "Пост $post_id удалён, или ещё не создан.";
				$this->name = "Нет такого поста.";
			} else {
				$this->text = $this->full ? my_r ( $res, $i, "fcontent" ) : my_r ( $res, $i, "content" );
				$this->vars = serialize ( my_r ( $res, $i, "vars" ) );
				$this->parent_post_id = my_r ( $res, $i, "parent" );
				$this->name = my_r ( $res, $i, "name" );
				$this->author_id = my_r ( $res, $i, "author_id" );
				$this->issafe = my_r ( $res, $i, "safe" );
				$this->id = $post_id;
				$this->source = my_r ( $res, $i, "source" );
				if (! $this->vars [content_preview]) {
					$this->rate = round ( mysql_result ( $res, $i, "rate" ), 3 );
				} else {
					$this->cid = my_r ( $res, $i, "content_id" );
					$this->rate = round ( mysql_result ( $res, $i, "content_rate" ), 3 );
				}
				$this->link = my_r ( $res, $i, "link" );
				$this->type_id = my_r ( $res, $i, "type_id" );
				$this->comments_num = get_comments_num ( $this->id );
				$this->date = date ( mysql_result ( $res, $i, "date" ) );
			}
		} else {
			$this->date = time ();
			$this->type_id = 0;
			$this->issafe = 1;
		}
	}

	static function get_name($id) {
		$postsel = Post_const::$post_select;
		if (! $id) {
			$id = $this->id;
		}
		$id = h2s ( $id );
		return my_fst ( "select name from $postsel where id=$id", "name" );
	}

	static function get_main_type_id($post_id){
		return my_fst("select * from post where id=$post_id", "main_type_id");
	}

	static function get_source($id) {
		if (! $id) {
			$id = $this->id;
		}
		$postsel = Post_const::$post_select;
		$res = my_fst ( "select source from $postsel where id='$id'", source );
		return $res;
	}

	static function get_rate($id) {
		return my_fst ( "select rate from post where id=$id", "rate" );
	}

	static function get_pub($id) {
		return my_fst ( "select pub from post where id=$id", "pub" );
	}

	static function get_author_id($id) {
		return my_fst ( "select author_id from post where id=$id", "author_id" );
	}

	static function get_content_id($id) {
		return my_fst ( "select content_id from post where id=$id", "content_id" );
	}

	//	static function get_subscribers($id=false){
	//		if(!$id){
	//			$id=self::$id;
	//		}
	//		return explode(" ", my_f("select subscribers from post where id=$id", "subscribers"));
	//	}


	static function get_last_version($id = false) {
		if (! $id) {
			$id = $this->id;
		}
		return my_fst ( "select last_version from post where id=$id", "last_version" );
	}

	static function get_content($post_id = false) {
		if (! $page->content) {
			if (! $post_id) {
				if ($this) {
					$post_id = $this->id;
				} else {
					if (vars::$debug) {
						print_br ( debug_backtrace () );
					}
				}
			}
			$from = Post_const::$post_select;
			return my_fst ( "select content from $from where id=$post_id", "content" );
		}

		//		else{
		//			return my_f("select content from post_content where id=$post_content", "content");
		//		}
	}

	static function get_system_param($content) {
		preg_match_all ( "/<!--[^:]+:[^-]+-->/", $page->content, $res );
		$res = $res [0] [0];
		$key = substr ( $res, 4, stripos ( $res, ":" ) - 4 );
		$value = substr ( $res, stripos ( $res, ":" ) + 1, strlen ( $res ) - stripos ( $res, ":" ) - 4 );
		return array ("key" => $key, "value" => $value );
	}

	static function get_uri_by_id($id = false, $section_name = "post") {
		if (! $id) {
			$id = $this->id;
		}
		return Request::get_ht () . "/$section_name/" . id2pid ( $id ) . "/" . self::get_link ( $id );
	}

	static function get_uri($res, $row=0, $section_name = "post") {
		$pid = Post::get_pid(my_r($res, $row, 'id'));
		$link = my_r($res, $row, "link");
		return Request::get_ht () . "/$section_name/" . $pid . "/" . $link;
	}

	static function get_link($id) {
		$id = h2i ( $id );
		if (! $id) {
			$id = $this->id;
		}
		return my_fst ( "select link from post where id=$id", "link" );
	}

	static function delete_comp($post_id) {
		my_q ( "delete from post where id=$post_id" );
	}

	static function get_new_post_rate($user_id) {
		$ucnt = get_users_cnt ();
		return User::get_rate ( $user_id ) * my_sqrt_z ( $ucnt + 1 ) / 10;
	}

	public function submit() {
		if(!$this->ftext&&!$this->processed){
			$this->ftext = $this->content;
		}
		if ($_COOKIE [stack_new] == true) {
			$this->stack_new = true;
		}
		$post = &$this;
		if (! $post->ftext) {
			$post->ftext = $post->text;
		}
		if ($post->link === null) {
			$post->link = s2link::translit ( $post->name );
		}
		$post->link = my_s ( $post->link );
		$post->author_id = my_s ( $post->author_id );
		$post->author_id = $post->author_id?$post->author_id:st_vars::$system_id;
		//cho $post->author_id==false;
		//$post->subscribers_ar[]=$post->author_id;
		$post->pub = my_s ( $post->pub );
		$post->parent_post_id = my_s ( $post->parent_post_id );
		$post->type_id = my_s ( $post->type_id );
		if (! $post->type_id) {
			$post->type_id = 1;
		}
		$post->issafe = my_s ( $post->issafe );
		if(is_array($post->vars)){
			$post->vars = serialize ( $post->vars );
		}
		$post->rate += get_type_rate ( $post->type_id );
		//$post->subscribers=join(" ", $post->subscribers_ar);
		//cho 1;
		if ($this->stack_new) {
			$this->stack_pub = $this->pub;
			$this->pub = PUB_NOWHERE;
		}
		$main_type_id=Type::get_main_type_id($this->type_id);
		$query = "insert into post (content_id,author_id, date, rate, link, pub, type_id, parent, safe, daily_rate,a,allow_rem,vars,main_type_id) values ('0',
		 '$post->author_id', '$post->date', '$post->rate', '$post->link', '$post->pub',
		 '$post->type_id', '$post->parent_post_id', '$post->issafe', $post->rate, 0,0,
		 '$post->vars', $main_type_id)";
		//cho $query;
		my_q ( $query );
		$post->id = mysql_insert_id ();
		$cont_id = Content::submit ( $post );
		if ($this->stack_new) {
			$this->stack_post ();
		}
		my_q ( "insert into subscribe(post_id, user_id) values($post->id, $post->author_id)" );
		my_q ( "update post set content_id='$cont_id' where id='$post->id'" );
		$pid = Post::get_pid ( $post->id );
		vote ( $post->id, 2, true );
		$post->pid = $pid;
		if ($post->parent_post_id) {
			$ppid = $post->parent_post_id;
			$paid = Post::get_author_id ( $ppid );
			if ($paid != $post->author_id) {
				$subscribers = my_q ( "select * from subscribe where post_id=$ppid" );
				for($i = 0; $i < my_n ( $subscribers ); $i ++) {
					$nuid = my_r ( $subscribers, $i, "user_id" );
					if ($nuid == User::get_c_id ()) {
						continue;
					}
					my_q ( "insert into notification(post_id, user_id) values($post->id, $nuid)" );
				}
			}
			if (! my_qn ( "select * from subscribe where post_id=$ppid and user_id=$post->author_id" ))
				my_q ( "insert into subscribe(post_id, user_id) values($ppid, $post->author_id)" );
		}
		return $post->id;
	}

	static function link_signature($uri) {
		$host = get_host ( $uri, true );
		$path = Request::get_path ( $uri );
		if ($host) {
			return "<br /><br />Источник: <a href='http://$host$path'>$host</a>";
		} else {
			return false;
		}
	}

	public function process() {
		$this->processed = true;
		$post = &$this;
		$crate = User::get_rate ();
		if ($post->source !== "" || $post->link !== "" || $post->name !== "") {
			$ucnt = get_users_cnt ();
				
			//cho htmlspecialchars($text);
			//$post[params][brreplace]=$post[brreplace];
			$source = $post->source;
			//loc($s)
			{
				$source = preg_replace ( '/<pre([^>]*)>(.*)<\/pre>/e', "'<pre$1>'.htmlspecialchars('$2').'</pre>'", $source );
			}
			$textar = preg_split ( "/<break([^>]*)>/", $source );
			if (count ( $textar ) > 1) {
				$post->vars [fexist] = true;
			}
			$post->text = $this->process_post_text ( $textar [0], $post->params );
			//rint_r($post->params);
			if (! is_array ( $post->params )) {
				//cho "params:$post->params";
				print_br ( debug_backtrace () );
			}
			$post->ftext = $this->process_post_text ( join ( "", $textar ), array_merge ( array ("nohide" => 1 ), $post->params ) );
			if ($post->params [sourcelink]) {
				//cho Post::link_signature($post->params["from"]);
				$post->ftext = $post->ftext . Post::link_signature ( $post->params ["from"] );
			}
			//cho htmlspecialchars($text);
			if (strlen_u ( $post->text ) > st_vars::$max_post_len) {
				return "Демо-часть вашего поста слишком длинная. Используйте тег \"&lt;break/&gt;\" чтобы отделить демо от всего.(" . st_vars::$max_post_len . ", у вас - " . strlen_u ( $post->text ) . ")";
				exit ();
			}
				
			if (strlen ( iconv ( "UTF-8", "cp1251", $post->ftext ) ) > st_vars::$max_fpost_len) {
				return "Ваш пост слишком длинный(максимальное количество символов - " . st_vars::$max_fpost_len . ", у вас - " . strlen_u ( $post->ftext ) . ")";
				exit ();
			}
				
			if (strlen_u ( $post->name ) > st_vars::$max_n_len) {
				return "$post->name Название слишком длинное(максимальное количество символов - " . st_vars::$max_n_len . ", у вас - " . strlen_u ( $post->name ) . ")";
				exit ();
			}
				
			if (strlen ( strlen ( $post->link ) ) > st_vars::$max_n_len) {
				return "Ссылка слишком длинная(максимальное количество символов - " . st_vars::$max_n_len . ", у вас - " . strlen ( $post->link ) . ")";
				exit ();
			}
				
			if ($post->text != "") {
				$post->date = time ();
				if ($post->parent_post_id != 0) {
					$pub2 = - 1;
					if (my_qn ( "select * from post where id=" . $post->parent_post_id ) < 1) {
						l404 ();
					}
				} else {
					if ($crate < st_vars::$rate_new_post) {
						$pub2 = - 2;
						print "У вас слишком низкий рейтинг...";
						exit ();
					} else {
						$pub2 = 2;
					}
				}
				$post->pub = min ( $pub2, h2i ( $post->pub ) );
				$post->issafe = 0;
				return true;
			} else if ($post->text == "") {
				return "Ошибка, введите текст поста!";
				exit ();
			}
		} else {
			return "Ошибка, введите текст поста!";
			exit ();
		}
	}

	private function stack_post() {
		$main_type_id = Type::get_main_type_id ($this->type_id);
		my_q ( "insert into stack_post(post_id, pub, main_type_id) values($this->id, $this->stack_pub, $main_type_id)" );
		return true;
	}


	public static function process_post_text($s, $params = array()) {
		if(!$params[htmlon]==true){
			$s=h2s($s);
			$params[brreplace]=true;
		}
		if($params[text_format]==true){
			$s=format_text($s);
		}else{
			if($params[htmlon]){
				return $s;
			}
		}
		$s = edittext ( $s, array ("brreplace" => $params ["brreplace"] ) );
		if(!$params[nohtml]){
			return $s;
		}

		$s = "<div class=\"root\">$s</div>";
		$params ["brreplace"] = false;
		$s = tidy_repair_string ( $s, array (wrap => 0, "wrap-sections" => 0 ), "UTF8" );
		$s = substr ( $s, stripos ( $s, "<div class=\"root\">" ) + strlen ( "<div class=\"root\">" ) );
		$s = substr ( $s, 0, strripos ( $s, "</div>" ) );
		$s = preg_replace ( '/<param([^>]*)name=\"([^\"]*)\"([^>]*)>/e', "'<param$1name=\"'.strtolower($2).'\"$3\">'", $s );
		$s = purify ( $s );
		//cho h2s($s);
		$params ["final"] = true;
		$s = edittext ( $s, $params );
		//cho h2s($s);
		$s = post_xml_edit ( $s, $params );
		//$s=my_s($s);
		$s = cerrarTags ( $s, array ("iframe", "li", "p", "s", "div", "ul", "ol", "table", "tr", "td" ) );
		$s = sclose_tags ( $s, array ("img" ) );
		if ($params [upload]) {
			$s = load_urls ( $s );
		}
		return $s;
	}

	static function get_pid($id = false) {
		if (! $id) {
			$id = $this->id;
		}

		return base_convert ( $id, 10, 36 );
	}

	public function update() {
		$post = $this;
		if ($post->id) {
			Content::submit ( $post );
			my_q ( "update post set last_version=last_version+1 where id='$post->id'" );
		} else {
			error ( "trying update post without id" );
		}
	}

	public function newp() {
		$post = $this;
		$post_id;
		if ($post) {
			if (! $post->params [preview]) {
				$post_id = $post->submit ();
			} else {
				$_SESSION [lpost] = serialize ( $post );
					
				//cho serialize($post);
			}
		} else {
			error ( "Ошибка1" );
			exit ();
		}

		if ($post) {
			if ($post->params [preview] == true) {
				loc ( "/lpreview", true );
			} else if ($_POST [iframe]) {
				close_topframe ();
			} else if ($post->parent_post_id == 0) {
				if (! $this->stack_new) {
					$pid = $post->pid;
					$link = $post->link;
					loc ( "/post/$pid/$link", true );
					unset ( $_SESSION [lpost] );
					$post = false;
				} else {
					$main_type_id = Type::get_main_type_id ();
					$stack_cnt = my_qn ( "select 8 from stack_post where main_type_id = $main_type_id" );
					print ("stat=ok&msg=Сообщение успешно добавлено в очередь под номером $stack_cnt") ;
				}
			} else {
				$parlink = get_post_link ( $post->parent_post_id );
				loc ( "/post/" . id2pid ( $post->parent_post_id ) . "/$parlink", true );
				unset ( $_SESSION [lpost] );
				$post = false;
			}
		} else {
			print $res;
		}
	}
	
	public static function get_type_id($post_id){
		return my_fst("select * from post where id = $post_id", "type_id");
	}
}

?>