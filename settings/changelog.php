<?php 
if(!User::get_c_id()){
	l404();
}

if(!$_POST[login]){
	ret("Введите новый ник.");
}
?>