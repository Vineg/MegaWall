<?php 
$page = new Page();
$ht = Request::get_ht();
$post_content = <<<EOQ
<form class=async action=$ht/func/stack_new>
Добавление постов в очередь:<br />
<button name=set class=submit>Включить</button><button name=unset class=submit>Выключить</button>
<span class="msg hidden"><br />
	<span class='body err'>
	</span>
</span>
</form>
EOQ;
$post = simple_post($post_content);
$page->content = $post;
process_page($page);
?>