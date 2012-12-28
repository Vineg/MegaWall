<?php
//chk

class megwall_client{
	var $request_uri;
	var $host;
	var $mw_user="Vinegbdf254ce2b7fcdb6bdc4386fe8c6587c";
	var $mw_secret="5042014f91ca17147a1c6211da746187";
	var $check_code=false;
	var $check_places=false;
	var $delimiter=" ";
	var $used_links=0;
	var $parent_host="";
	var $uri;
	var $turn_off_proxy=false;//not recommended to change.
	var $encoding="utf-8";
	var $mw_rand="";
	var $version="1.0";
	
	function megwall_client($options=array()){
		$this->construct($options);
	}
	
	function construct($options=array()){
		$this->host=$_SERVER["HTTP_HOST"];
		$this->request_uri=$_SERVER["REQUEST_URI"];
		$this->uri="http://".$this->host.$this->request_uri;
		$this->encoding=$options[encoding];
		if($_COOKIE[mw_key]==_MW_USER){
			$this->mw_rand=$_COOKIE[mw_rand];
			if($_COOKIE[update_links]==true){
				$this->update_links();
			}
			
			if($_COOKIE[index]==true){
				$this->check_places=true;
			}
			if($_COOKIE[check_code]==true){
				$this->ret("");
			}
			
			if($_COOKIE[request_proxy]){
				if($_COOKIE[mw_secret]){
					ret($this->request_proxy($_COOKIE[request_proxy]));
					exit;
				}
			}
			
		}
	}

	
	function return_links($num=1, $array=false){
		if($this->check_places){
			$rand=$_COOKIE[mw_random];
			return join($this->delimiter,array_fill(0, $num, "<!--mw_link_place_$rand-->"));
		}
		$links=$this->get_page_links($num);
		for($i=0; $i<count($links); $i++){
			$text=mb_convert_encoding($links[$i][text], $this->encoding, "UTF8");
			$uri=$links[$i][uri];
			if(preg_match("/#a#(.+)#\/a#/", "$text")){
				$res[]=preg_replace("/#a#(.+)#\/a#/", "<a href='$uri'>\\1</a>", "$text");
			}else{
				$res[]="<a href='$uri'>$text</a>";
			}
			if(!$array){
			return join($this->delimiter, $res);
			}else{
				return $res;
			}
		}
	}
	
	function update_links(){
		$f=fopen("$this->mw_user/$this->host.mwlinks.db", "w+");
		$cont=file_get_contents("http://$this->parent_host/linkproj/$this->mw_user/$this->host");
		$ar=unserialize($cont);
		if($ar[wcheck]=="done"){
			fwrite($f, $cont);
			fclose($f);
			$this->ret("host_updated&v=$this->version");
		}
		$this->ret("bad_source<br /><pre>$cont</pre>");
	}
	
	function get_page_links($num=1){
		$links=$this->get_links();
		$alllinks=$links[$this->uri]?$links[$this->uri]:array();
		$links=array_splice($alllinks, $this->used_links);
		$this->used_links+=$num;
		
		return $links;
	}
	
	function get_links(){
		$ar=unserialize($this->get_links_object());
		if($ar[wcheck]){
			return $ar;
		}else{
			return array();
		}
	}
	
	function get_links_object(){
		$file="$this->mw_user/$this->host.mwlinks.db";
		$fileh=fopen($file, "a+");
		if(filesize($file)){
			$res=fread($fileh, filesize($file));
		}
		fclose($fileh);
		return $res;
	}
	
	function request_proxy($uri){
		if(!$this->turn_off_proxy){
			ret(file_get_contents("$uri"));
		}else{
			return "";
		}
	}
	
	function ret($s){
		$rand=$this->mw_rand;
		print "<mw_responce_$rand>$s</mw_responce_$rand>";
		exit;
	}
}
//chk
?>