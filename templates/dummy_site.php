<?php
$template=st_vars::$def_template;
function process_page(Page $page=null){
	if(!$page){
		$page=new Page();
	}
	$ht=Request::get_ht();
	$headad=<<<EOQ
EOQ;
	$page->menu=<<<EOQ
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
	
	$page->ready();
	render_page($page);
}

include_once "templates/$template.php";