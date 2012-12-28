<?php
class Link {
	public $uri, $host_id;
	function Link($id) {
		$r = my_q ( "select * from link where id=$id" );
		$this->donor_uri = my_fst ( $r, "uri" );
		$this->host_id = my_fst ( $r, "host_id" );
	}
	static function get_id($uri) {
		$uris = my_s ( $uri );
		$id = my_fst ( "select * from link where uri='$uris'", "id" );
		//$id=$uri_id
		if ($id) {
			return $id;
		} else {
			//$host_id=get_host_id(get_host($uri));
			$id = newurl ( $uri, 4 );
			//my_in("link:uri=$uris,level=4,out_links=-1,host_id=$host_id");
			return $id;
		}
	}
	
	static function check_tolink($id) {
		$tolink_req = Host::$tolink_req;
		$req = my_q ( "select * from $tolink_req where id=$id" );
		$donor_uri = my_fst ( $req, "donor_uri" );
		$s = request ( $donor_uri, array (return_info => true ) );
		
		if ($s [info] [status] != "200") {
			return false;
		}
		$s = $s [body];
		
		$acceptor_uri = my_fst ( $req, "acceptor_uri" );
		$text = my_fst ( $req, "text" );
		if (preg_match ( "/#a#(.+)#\/a#/", "$text" )) {
			$link = preg_replace ( "/#a#(.+)#\/a#/", "<a href='$acceptor_uri'>\\1</a>", "$text" );
		} else {
			$link = "<a href='$acceptor_uri'>$text</a>";
		}
		if (stripos ( $s, $link )) {
			$time = time ();
			my_up ( "tolink:placed=1,check_time=$time:id=$id" );
			return true;
		} else {
			$time = time ();
			my_up ( "tolink:placed=0,time=$time:id=$id" );
			return false;
		}
	}
	
	static function get_uri($id) {
		return my_fst ( "select * from link where id=$id", "uri" );
	}
	static function add_text($uri_id, $text) {
		$text = my_s ( $text );
		if (! my_qn ( "select * from link_text where link_id=$uri_id and text='$text'" )) {
			my_in ( "link_text:text=$text,link_id=$uri_id" );
			return true;
		} else {
			return "Текст уже существует.";
		}
		;
	}
	
	static function rem_text($uri_id, $text) {
		$text = my_s ( $text );
		if (my_qn ( "select * from link_text where link_id=$uri_id and text='$text'" )) {
			my_q ( "delete from link_text where text='$text' and link_id=$uri_id" );
			return true;
		} else {
			return "Текст не найден.";
		}
		;
	}
	
	static function get_weight($uri_id) {
		//$uris=my_s($uri);
		return (my_fst ( "select * from link where id=$uri_id", "weight" ));
	}
	
	static function get_host_id($uri_id) {
		return (my_fst ( my_q ( "select * from link where id=$uri_id" ), "host_id" ));
	}
	static $textreq = "(SELECT link.host_id, link_text.text, link.id as link_id FROM `link_text`  left join link on link.id=link_text.link_id)text1";
}

?>