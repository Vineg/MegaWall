<?php
$template=st_vars::$def_template;
include_once "templates/$template.php";
function process_page(Page $page=null){
	if(!$page){
		$page=new Page();
	}
	$ht=Request::get_ht();
	$ul=User::get_link();
	$login=User::get_login();
	$userinfo=<<<EOQ
<li class="info_text">
		<span class="amtext"><span><span><a href="$ul" class="ok">$login
		</a></span></span></span>
</li>
EOQ;
	$headad=User::get_id()?<<<EOQ
	<li class="page_item">
		<a href="$ht/logout" title="Выход" class="amtext"><span><span>Выход</span></span></a>
	</li>
	$userinfo
EOQ
:<<<EOQ
	<li class="page_item">
		<a href="$ht/login_vk" title="Вход" class="amtext"><span><span>Вход</span></span></a>
	</li>
EOQ;
	$page->menu=<<<EOQ
<li>
   <a href="/" class="amtext"><span><span>Главная</span></span></a>
</li>
<li class="page_item">
		<a href="$ht/about" title="О сайте" class="amtext"><span><span>О 
сайте</span></span></a>
</li>
<li class="page_item">
		<a href="$ht/blog/" title="О сайте" class="amtext"><span><span>Блог</span></span></a>
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
	render_page($page);
}