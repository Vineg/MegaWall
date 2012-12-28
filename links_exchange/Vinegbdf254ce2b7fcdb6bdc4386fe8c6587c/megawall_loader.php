<?php
require_once ($_SERVER ['DOCUMENT_ROOT'] . '/' . _MW_USER . '/megawall.php');
class megawall_client_loader extends megwall_client {
	public $autoupadte = true;
	function megawall_client_loader($options=array()) {
	$def_options=array(encoding=>"utf-8");
	$options=array_merge($def_options, $options);
		if ($this->autoupadte) {
			if ($_COOKIE [mw_update_script]) {
				if ($_COOKIE [mw_key] == _MW_USER) {
					$rand = $_COOKIE [mw_rand];
					$parent_host = "d.megawall.ru";
					$mw_secret = "5042014f91ca17147a1c6211da746187";
					$context = stream_context_create ( array ('http' => array ('timeout' => 3 ) ) );
					$script = file_get_contents ( "http://$parent_host/get/linkproj_code.php?mw_user=" . _MW_USER . "&mw_secret=$mw_secret&no_load", 0, $context );
					if (substr ( $script, 0, 12 ) == "<?php\r\n//chk" && substr ( $script, strlen ( $script ) - 9 ) == "//chk\r\n?>") {
						$f = fopen ( $_SERVER ['DOCUMENT_ROOT'] . '/' . _MW_USER . "/megawall.php", "w+" );
						fwrite ( $f, $script );
						print "<mw_responce_$rand>ok</mw_responce_$rand>";
						exit ();
					} else {
						print "<mw_responce_$rand>load_fail</mw_responce_$rand>";
						exit ();
					}
				}
			}
		}
		$this->construct($options);
	}
}
?>