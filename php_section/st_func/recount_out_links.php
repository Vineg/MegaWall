<?php
$host=$_GET[host];
$host_id=Host::get_id($host);
if(Host::get_author_id($host_id)!=User::get_c_id()){
	ret("<span class=err>Это не ваш сайт.</span>");
}
$host_id=Host::get_id($host);
$recount=BOT_RECOUNT_OLINKS_ALL;
if(my_qn("select * from bot_task_host where task=$recount and host_id='$host_id'")){
	ret("<span class=err>Заявка на пересчёт внешних ссылок уже подана.</span>");
}
my_in("bot_task_host:task=$recount,host_id=$host_id,rate=0");
ret("<span class=ok>Сайт отправлен на пересчёт внешних ссылок.</span>");
?>