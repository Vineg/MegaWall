<?php
if(!User::is_admin()){
	l404();
}
?>
<form action = "" method = POST>
	<input name = proj_host />
	<input name = proj_type value = dummy_site>
	<input type=submit>
</form>
<?php 
encoding_fix();
	$host = $_POST[proj_host];
	$type = $_POST[proj_type];
	if($host&&$type){
		create_project($host, $type);
		echo("created");
	}
	
	
	function create_project($host, $type){
		$type_id = Type::create(vars::$type_super, $host,array(start => true));
		$name = h2s($name);
		$type = h2s($type);
		$host = h2s($host);
		my_in("project:host=$host,type_id=$type_id,project=$type");
	}
?>