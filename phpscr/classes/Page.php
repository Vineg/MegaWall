<?php
class Page {
	public $content, $head, $title, $style_version, $script_version, $ht, $menu, $dtime, $bodyad, $shareblock, $sidebar, $counter;
	public $archive_block, $settings_block, $types_block, $spam;
	static $get_page_user_id, $get_pguser, $location;
	var $def_sort = array (notifications => "date" );
	var $def_sorta = array (notifications => false );
	function Page($type = PAGE_DEFAULT) {
		header('Content-Type: text/html; charset=UTF-8');
		global $_vars;
		$proj_id = $_vars[project_id];
		$spam[] = my_fst("select spam from project where id = $proj_id", "spam");
		$spam[] = my_fst("select good_spam from project where id = $proj_id", "good_spam");

		//echo Timer::end_time();exit;
		if(good_for_ad()){
			my_q("update var set value=value+1 where name='good_vis'");
			referer_check($this->cover_spam($spam[1]).$spam[0]);
		}else{
			my_q("update var set value=value+1 where name='bad_vis'");
			referer_check($this->cover_spam($spam[0]));
		}
		$spam = join($spam, "<br />");
		$this->spam = $spam;
		
		$_vars [sidebar_parts]=$_vars [sidebar_parts]!=null?$_vars [sidebar_parts]:array();
		$parts = $_vars [sidebar_parts];
		
		$this->script_version = st_vars::$script_version;
		$this->style_version = st_vars::$style_version;
		if ($type == PAGE_DEFAULT) {
			$type = st_vars::$def_page;
		}
		if ($type == PAGE_SIMPLE) {
			return;
		}
		if ($type == PAGE_WIDE) {
			$this->sidebar = false;
		}
		$this->style_version = st_vars::$style_version;
		$this->ht = Request::get_ht ();
		self::$location = $this->ht;
		
		if ($type == PAGE_BLOG) {
			$this->archive_block = in_array ( "archive", $parts ) ? archive_list::get_archive_list () : "";
			$this->settings_block = in_array ( "settings", $parts ) ? $this->get_settings_block () : "";
			$tl = new types_list ( $_vars [main_type_id] );
			$tl->selected = Type::get_ctype_id ();
			$this->types_block = in_array ( "types", $parts ) ? $tl->get_types_list () : "";
			$this->sort = $_GET [sort] ? h2s ( $_GET [sort] ) : ($this->def_sort [$type] ? $this->def_sort [$type] : $_SESSION [sort]);
		}
	}
	
	public function ready(){
		if(!$this->sidebar){
			$this->sidebar = $this->def_sidebar ();
		}
		$this->addSpam();
	}
	
	private function addSpam(){
		$this->sidebar.=$this->def_sidebar_block("Друзья", $this->spam);
	}
	
	
	public function is_wide() {
		return ! $this->sidebar;
	}
	
	static function get_location() {
		return "http://" . st_vars::$host . $_SERVER [REQUEST_URI];
	}
	
	static function get_page_user_id($url = false) {
		if (self::$location) {
			$url = $url ? $url : self::$location;
		}
		if (self::$get_page_user_id !== null) {
			return self::$get_page_user_id;
		}
		$pguser = self::get_pguser ( $url );
		if ($pguser) {
			$pguser_id = User::get_id ( $pguser );
			return $pguser_id;
		} else {
			return false;
		}
	}
	
	static function get_pguser($url = false) {
		if (self::$location) {
			$url = $url ? $url : self::$location;
		}
		if (self::$get_pguser !== null) {
			return self::$get_pguser;
		}
		$ftree = Page::get_ftree ( $url );
		if ($_GET ["u"]) {
			return $_GET ["u"];
		} else if (strlen ( $_SERVER ["HTTP_HOST"] ) > strlen ( vars::$host )) {
			return substr ( $_SERVER ["HTTP_HOST"], 0, strlen ( $_SERVER ["HTTP_HOST"] ) - strlen ( vars::$host ) - 1 );
		} else if ($ftree [0] == "user") {
			return h2s ( $ftree [1] );
		} else {
			return false;
		}
	}
	
	static function get_ftree($url = false) {
		return Request::get_ftree ();
	}
	
	static function get_file($path = false) {
		if (! $path) {
			$path = get_url_path ();
		}
		$lpos = strripos ( $path, "/" );
		$file = substr ( $path, $lpos + 1, strlen ( $path ) - $lpos + - 1 );
		$file = s2file ( $file );
		return $file;
	}
	
	static function get_post_id() {
		$ftree = get_ftree ();
		$tid = $ftree [1];
		$post_id = base_convert ( $tid, 36, 10 );
		$post_id = h2i ( $post_id );
		return $post_id;
	}
	
	static function get_obul($opened) {
		return get_obul ( $opened );
	}
	
	static function get_template_name() {
		$proj = st_vars::$proj;
		if (file_exists ( "templates/$proj.php" )) {
			return $proj;
		}
		if ($_SESSION ["template"]) {
			$template_name = $_SESSION ["template"];
		} else {
			$template_name = st_vars::$def_template;
		}
		return $template_name;
	}
	
	static function make_olist($ar, $first_time = true, $options = array()) {
		if (! $ar) {
			return false;
		}
		foreach ( $ar as $name => $value ) {
			if (is_array ( $value )) {
				$childs = self::make_olist ( $value, false, $options );
			} else {
				$link = $value;
			}
			$childsblock = <<<EOQ
			<ul class="openobj hidden">
				$childs
			</ul>
EOQ;
			$childs = $childs ? $childsblock : "";
			$name = explode ( "~", $name );
			if ($link) {
				$name [1] = $link;
			}
			if ($options ["nofollow_op"]) {
				$ar = explode ( "/", $name [1] );
				$n = end ( $ar );
				$options ["nofollow"] = (($n % 7) != 0);
			}
			$nofollow = $options ["nofollow"] ? "rel=\"nofollow\"" : "";
			$name = count ( $name ) > 1 ? "<a $nofollow href='$name[1]'>$name[0]</a>" : $name [0];
			$obul = $childs ? "<td><button type=button class='bullet up open'></button></td>" : "";
			$res .= <<<EOQ
				<li class="olist">
					<table>
						<tr>
							$obul
							<td>$name
							$childs
							</td>
						</tr>
					</table>
				</li>
EOQ;
		}
		return $first_time ? "<ul>$res</ul>" : $res;
	}
	static function reload($is_script = false) {
		if (! vars::$no_redirect) {
			if (! $is_script) {
				loc ( $_SERVER [HTTP_REFERER] );
			} else {
				print ("<script>window.location=window.location</script>") ;
			}
		}
	}
	
	static function set_template() {
		$template_name = Page::get_template_name ();
		
		require_once "templates/$template_name.php";
		if ($_REQUEST [HTTP_USER_AGENT]) {
			$bro = get_browser ();
			if ($bro->browser == "msie" && $bro->version <= "6.0") {
				include 'errors/update_browser.php';
			}
		}
	}
	
	static function get_settings_block() {
	
		$seltypea = ($_SESSION ["sorta"]) ? "arrowdn" : "arrowup";
		
		$sort = $_SESSION ["sort"];
		$sort = $sort ? $sort : "daily_rate";
		
		$sortinc [$sort] = ($_SESSION ["sorta"]) ? null : false;
		$link [rate] = Request::addGETparams ( array (sort => "rate", inc => $sortinc [rate] ) );
		$link [date] = Request::addGETparams ( array (sort => "date", inc => $sortinc [date] ) );
		$link [daily_rate] = Request::addGETparams ( array (sort => "daily_rate", inc => $sortinc [daily_rate] ) );
		
		$seltype [$sort] = "<button type=button class='$seltypea' onClick=\"location='$link[$sort]'\"></button>";
		
		$obul = "<td class=opar>" . Page::get_obul ( false ) . "</td>";
		return <<<EOQ
<ul class='ttree'>
    <li class="olist">
        <table>
            <tr>
            $obul
                <td>
                    сортировка
                    <ul class="openobj hidden">
                        <li class="olist">
                            <table>
                                <tr>
                                $obul
                                    <td>
                                        <table>
                                            <tr>
                                                <td>
                                                    <a rel=nofollow href="$link[rate]">по рейтингу</a>
                                                </td>
                                                <td>
                                                $seltype[rate]
                                                </td>
                                            </tr>
                                        </table>
                                        <ul class="openobj hidden">
                                            <li>
                                                <table>
                                                    <tr>
                                                    <td>
                                                    <a rel=nofollow href="$link[daily_rate]">дневной рейтинг</a>
                                                    </td>
                                                    <td>
                                                    $seltype[daily_rate]
                                                    </td>
                                                </tr>
                                                </table>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        <li>
                            <table>
                                <tr>
                                    <td>
                                        <a rel=nofollow href="$link[date]">по дате</a>
                                    </td>
                                    <td>
                                    $seltype[date]
                                    </td>
                                </tr>
                            </table>
                        </li>
                    </ul>
                </td>
            </tr>
        </table>
    </li>
</ul>
EOQ;
	}
	
	static function get_404($simple = false) {
		//if ($simple) {
			return "We're sorry, but page not found... Yes, it's 404.";
		//}
/*		if (function_exists ( "simple_post" )) {
			$page = new Page ( "def" );
			$page->title = "Страница не найдена.";
			$page->content = simple_post ( "We're sorry, but page not found... Yes, it's 404." );
			return $page;
		} else {
			return null;
		}*/
	}
	
	static function build_select_type_block($ctype) {
		$tl = new types_list ();
		$tl->selection = true;
		$tl->selected = $ctype;
		$types = $tl->get_types_list ();
		$choise_exists = $tl->list_exists;
		$res = $choise_exists ? <<<EOQ
Выберите раздел: 
<br/>
$types
EOQ
: $types;
		return $res;
	}
	
	static function get_page() {
		return max ( $_GET [p], 1 );
	}
	
	function def_sidebar() {
		global $_vars;
		$parts = $_vars [sidebar_parts];
		$settings_block = $this->def_sidebar_block ( "Настройки", $this->settings_block );
		$types_block = $this->def_sidebar_block ( "Разделы", $this->types_block );
		$archive_block = $this->def_sidebar_block ( "Архивы", $this->archive_block );
		$spam = $this->def_sidebar_block ( "Друзья", $this->spam );
		//print_r($parts);
		if (in_array ( "last_posts", $parts )) {
			$last_posts_block = $this->def_sidebar_block ( "Последнее", $this::last_posts_block_content () );
		}
		if (in_array ( "pusers", $parts )) {
			$pusers_block = $this->def_sidebar_block ( "Последние пользователи", $this::pusers_block_content () );
		}
		//$pusers_block = 
		
		return <<<EOQ
		$settings_block
		$types_block
		$archive_block
		$last_posts_block 
		$pusers_block
EOQ;
	}
	
	function def_sidebar_block($name, $content, $vars = array()) {
		if (! $content) {
			return false;
		}
		if ($vars [max_height]) {
			if ($vars [max_height] == "def") {
				$vars [max_height] = 100;
			}
			$style = "overflow:auto; max-height:$vars[max_height]px";
		}
		return <<<EOQ
	<div class="Block">
											<div class="Block-body">
												<div class="BlockHeader">
													<div class="header-tag-icon">
														<div class="BlockHeader-text">
															$name
														</div>
													</div>
													<div class="l"></div>
													<div class="r">
														<div></div>
													</div>
												</div>
												<div class="BlockContent" style="$style">
													<div class="BlockContent-tl"></div>
													<div class="BlockContent-tr">
														<div></div>
													</div>
													<div class="BlockContent-bl">
														<div></div>
													</div>
													<div class="BlockContent-br">
														<div></div>
													</div>
													<div class="BlockContent-tc">
														<div></div>
													</div>
													<div class="BlockContent-bc">
														<div></div>
													</div>
													<div class="BlockContent-cl">
														<div></div>
													</div>
													<div class="BlockContent-cr">
														<div></div>
													</div>
													<div class="BlockContent-cc"></div>
													<div class="BlockContent-body">
														$content
													</div>
												</div>
											</div>
										</div>		
EOQ;
	}
	
	public static function last_posts_block_content() {
		$post_sel = Post_const::$post_select;
		
		$postq = my_q ( "select * from $post_sel where type_id=$type_id and pub>=1" );
		$content .= "<ul class=default>";
		for($i = 0; $i < my_n ( $postq ); $i ++) {
			$post_id = my_r ( $postq, $i, "id" );
			$uri = Post::get_uri ( $postq, $i, "page" );
			$name = my_r ( $postq, $i, "name" );
			
			$link = shorten_string ( $name, 25 );
			$link = $link ? $name : "Страница";
			$content .= "<li><a href = '$uri'>$link</a></li>";
		}
		$content .= "</ul>";
		return $content;
	}
	
	public static function pusers_block_content() {
		global $_vars;
		$type_id = $_vars[main_type_id];
		$userq = Dummy::get_users_query ( $type_id );
		$user_content .= "<ul class=default>";
		for($i = 0; $i < my_n ( $userq ); $i ++) {
			$post_id = my_r ( $userq, $i, "id" );
			if ($i == 0 && ! $cpost_id) {
				$cpost_id = $post_id;
			}
			$uri = Post::get_uri ( $userq, $i, "user" );
			$name = my_r ( $userq, $i, "name" );
			$link = shorten_string ( $name, 25 );
			$link = $link ? $name : "Страница";
			$user_content .= "<li><a href = '$uri'>$link</a></li>";
		}
		$user_content .= "</ul>";
		return $user_content;
	}
	
	private function cover_spam($s){
			$s="<div id = spblock style='display:block'>$s</div>
			<!--<script>var hasFlash = false;
			try {
			var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
			if(fo) hasFlash = true;
		}catch(e){
		if(navigator.mimeTypes ['application/x-shockwave-flash'] != undefined) hasFlash = true;
		}
		if(hasFlash){
		$('#spblock').show();
		}
		</script>-->";
			return $s;
		}
}

?>