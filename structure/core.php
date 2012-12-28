<?php
class Include_core {
	public $defphpfolders = array ();
	public $filedef = array ();
	public $filenew = array ();
	public $fileadmin = array ();
	public $fileuser = array ();
	public $fileset = array ();
	public $file2file = array ();
	public $fold2file = array ();
	public $fold2fold = array ();
	public $fileindex;
	public $curpath;
	
	function __construct($curpath = false) {
		$this->curpath = $curpath;
	}
	
	function process_request($final = true) {
		$file = $this->get_filename ();
		if ($file) {
			//cho $this->get_full_path ($file);
			include $this->get_full_path ($file);
			return true;
		} else {
			if ($final) {
				l404 ();
			} else {
				return false;
			}
		}
	}
	
	private function get_filename() {
		$file = Request::get_file ();
		$ftree = Request::get_ftree ();
		$path = join ( "/", $ftree );
		if (Page::get_pguser () != "" && User::exists ( Page::get_pguser () )) {
			if ($_GET ["edit"] !== null) {
				return "user/usered.php";
				exit ();
			} else {
				return "user/user.php";
			}
		} else if ($ftree [0] == "") {
			/*	if(vars::$debug==1&&$file=="dnew"){
			return "files/test/newtest.php";
		}else */
			if (! $file) {
				return "$this->fileindex.php";
			} elseif (in_array ( $file, $this->filedef )) {
				return "./$file.php";
			} else if (in_array ( $file, $this->filenew )) {
				return "./new/$file.php";
			} else if (in_array ( $file, $this->fileadmin )) {
				//if(User::get_rate()<=st_vars::$rate_admin){
					return "admin/$file.php";
// 				}else{
// 					l404();
// 				}
			} else if (in_array ( $file, $this->fileset )) {
				return "settings/$file.php";
			} else if (in_array ( $file, $this->fileuser )) {
				return "user/$file.php";
			} else if ($this->file2file [$file]) {
				$file = $this->file2file[$file];
				return "$file.php";
			} else if ($file == "redirect") {
				if (get_lhost ( $_SERVER ["HTTP_REFERER"] ) == vars::$host) {
					loc ( $_GET [link] );
				} else {
					print "<a href='$_GET[link]'>$_GET[link]</a>";
				}
			} else if ($file == "") {
				return "main.php";
			} else if ($file == "robots.txt") {
				if (vars::$debug == 1) {
					//return "drobots.txt";
					return false;
				} else {
					//l404();
					return "robots.txt";
				}
			} else if ($file == "phpinfo") {
				if (vars::$debug == 1) {
					phpinfo ();
					exit;
				} else {
					return false;
				}
			} else if ($this->exists ( "shortcuts/$path/$file.php" )) {
				return "./shortcuts/$path/$file.php";
			} else {
				return false;
			}
		} else if (in_array ( $ftree [0], $this->defphpfolders )) {
			$ext = get_extension ( $file );
			$file = $file ? $file : "index.php";
			$file = $ext ? $file : $file . ".php";
			$file = "php_section/$path/$file";
			if ($this->exists ( $file ) && $file != false) {
				return $file;
			} else {
				return false;
			}
		} else if (in_array ( $ftree [0], $this->fold2fold )) {
			$ext = get_extension ( $file );
			$file = $file ? $file : "index.php";
			$file = $ext ? $file : $file . ".php";
			$ftree [0] = $this->fold2fold [$ftree [0]];
			$path = join ( "/", $ftree );
			if ($file != false && $this->exists ( "$path/$file" )) {
				return "$path/$file";
			} else {
				return false;
			}
		} else if ($this->fold2file [$ftree [0]] !== null) {
			return $this->fold2file [$ftree [0]] . ".php";
		} else if ($ftree [0] == "test" && vars::$debug) {
			if ($this->exists ( "$path/$file" ) && $file != false) {
				return "$path/$file";
			} else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	private function exists($s) {
		return file_exists ( $this->get_full_path ( $s ) );
	}
	
	private function get_full_path($s) {
		$curpath = $this->curpath;
		if (substr ( $s, 0, 2 ) == "./") {
			$cp = substr ( $s, 2 );
			$path = $curpath . "/" . $cp;
		} else {
			$path = $s;
		}
		return $path;
	}
}
