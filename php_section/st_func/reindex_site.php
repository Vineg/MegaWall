<?php
$reindex=BOT_INDEX;
$host=$_GET[host];
$host_id=Host::get_id($host);
if(Host::get_author_id($host_id)!=User::get_c_id()){
	ret("<span class=err>Это не ваш сайт.</span>");
}
$host_id=Host::get_id($host);
if(my_qn("select * from bot_task_host where task='$reindex' and host_id='$host_id'")){
	ret("<span class=err>Заявка на переиндексацию уже подана.</span>");
}
Bot::add_task($reindex, $host_id);
ret("<span class=ok>Сайт отправлен на переиндексацию.</span>");
?>