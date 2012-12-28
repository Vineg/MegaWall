<?php
class Request{
	static $ftree=null;
	
	static function get_path($uri){
		if(stripos($uri, "/")===false){
			return "/";
		}else{
			return substr($uri, stripos($uri, "/"));
		}
	}
	
	static function get_ht($host=false){
		if(!$host){$host=vars::$host;}
		return "http://".vars::$host;
	}
	
	static function get_pht(){
		return "http://".(vars::$parent_host?vars::$parent_host:vars::$host);
	}
	
	static function get_uri(){
		return "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
	}
	
	static function get_alias($subdomen){
		return st_vars::$request_aliases[$subdomen];
	}

	static function get_subdomen($domen=false){
		if(!$domen){
			$domen=$_SERVER["HTTP_HOST"];
		}
		$domen=explode(".", $domen);
		$subhost=array_slice($domen, 0, count($domen)-vars::$host_level);
		$subhost=join(".", $subhost);
		return $subhost;
		// 		if(strlen($_SERVER["HTTP_HOST"])>strlen(vars::$host)){
		// 			return substr($_SERVER["HTTP_HOST"], 0, strlen($_SERVER["HTTP_HOST"])-strlen(vars::$host)-1);
		// 		}else{
		// 			return false;
		// 		}
	}

	static function get_ftree($url=false){
		global $var;
		if($var[ftree]){
			$ftree = $var[ftree];
			return $ftree;
		}
		$ftree=self::get_ftree_nocache($url);
		return $ftree;
	}
	
	static function get_ftree_nocache($urli=false){
		$ftree=array();
		$url=$urli;
		if(!$url){
			$path=get_url_path($_SERVER["REQUEST_URI"]);
			$host=$_SERVER["HTTP_HOST"];
		}else{
			$path=get_url_path($url);
			$host=get_host($url);
		}
		if(!$path){
			return $ftree;
		}
		$lpos=1;
		$alias=false;
		if($host){
			$sd=Request::get_subdomen($host);
			if($sd){
				$alias=Request::get_alias($sd);
			}
		}
		if($alias){
			$ftree=get_ftree($alias);
		}
		while(strpos($path, "/", $lpos)!==false){
			$pos=strpos($path, "/", $lpos);
			$ftree[]=s2file(substr($path, $lpos, $pos-$lpos));
			$lpos=$pos+1;
			//cho("|$i !! $ftree[$i]|");
		}
		return $ftree;
	}

	static function get_file($path=false){
		if(!$path){
			$path=get_url_path();
		}
		$lpos=strripos($path, "/");
		$file=substr($path, $lpos+1, strlen($path)-$lpos+-1);
		$file=s2file($file);
		return $file;
	}

	static function GET_exist($url=false){
		if(!$url){
			$url=$_SERVER["REQUEST_URI"];
		}
		if(stripos($url, "?")!==false){
			return true;
		}else{
			return false;
		}
	}

	static function ar2GET($ar){
		return "?".ar2s($ar, "&", "=");
	}

	static function addGETparams($ar, $req=false){
		//rint_r(array_merge(self::GET(), $ar));
		if(!$req){
			$req=Request::request_URI();
		}
		$getar=self::GET($req);
		return Request::addr($req).self::ar2GET(array_merge($getar, $ar));
	}

	static function GET($req=false){
		$GET=array();
		$parstr=self::GETs($req);

		$parar=explode("&", $parstr);
		for($i=0; $i<count($parar); $i++){
			$ar0=explode("=", $parar[$i]);
			if($ar0[0]){
				$GET[$ar0[0]]=$ar0[1]?$ar0[1]:false;
			}
		}

		//	if(strlen($_SERVER["HTTP_HOST"])>strlen(vars::$host)){
		//		$_GET["u"]=substr($_SERVER["HTTP_HOST"], 0, strlen($_SERVER["HTTP_HOST"])-strlen(vars::$host)-1);
		//	};
		return $GET;
	}

	static function GETs($req=false){
		if(!$req){
			$req=$_SERVER["REQUEST_URI"];
		}
		$GET=array();
		$_GETex=stripos($req, "?",  0)?true:false;
		$adrend=(strpos($req, "?",  0)===false)?strlen($req):strpos($req, "?",  0);
		$parstr=substr($req, $adrend+1, strlen($req));
		return $parstr;
	}

	static function request_URI(){
		return $_SERVER["REQUEST_URI"];
	}

	static function addr($req){
		return get_addr($req);
	}
	
	static function get_host(){
		global $_var;
		if($_var[host]){
			return $_var[host];
		}else{
			return $_SERVER[HTTP_HOST];
		}
	}
}
?>