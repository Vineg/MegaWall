<?php
$page=new Page();
$page->title="Личный Кабинет";
$uname=User::get_login();
if(!$uname){
	l404();
}
$page->content=simple_post(<<<EOQ
<h3>Личные настройки</h3>
<ul class=simple>
	<li>
		<ul class=olist>
			<a class=open>Сменить ник</a>
			<li class="openobj hidden">
				<form action=changelog class=async>
					Введите новый ник: <input placeholder="Ник" name=login value="$uname"/>
					<button class="submit inline">Применить</button>
				</form>
			</li>
		</ul>
	</li>
	<li>
		<ul class=olist>
			<a class=open>Сменить пароль</a>
			<li class="openobj hidden">
				<form>
				<table>
				<tr>
					<td>Старый пароль:</td><td> <input name=oldpass type=password></input></td>
				</tr>
				<tr>
					<td>Новый пароль:</td><td> <input type=password name=newpass></input></td>
				</tr>
				<tr>
					<td>Повторите пароль:</td><td> <input type=password name=newpass1></input></td>
				<tr>
				</table>
				</form>
			</li>
		</ul>
	</li>
	<li>
		<a href=infoedit>Редактировать мою информацию</a>
	</li>
</ul>
EOQ
);
process_page($page);
?>