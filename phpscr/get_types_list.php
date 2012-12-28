<?php

class types_list{
	private $get_childs, $get_author;
	private $get_childs_cnt;
	private $get_name;
	private $get_link;
	private $get_parent;
	private $ad;
	private $selectedar=array();
	private $ctype;
	private $get_full_name;
	public $selected=1, $selection=0, $display_ids=0, $full_name_edit=0, $pguser_id, $table="type";
	public $list_exists = true;
	
	function types_list($main_type_id=null){
		require_once 'shortcuts.php';
		require_once 'sys/vars.php';
		require_once 'settings/mysql_connect.php';
		if($main_type_id===null){
			$main_type_id=Type::get_main_type_id(false, $this->table);
		}
		$this->start_id=$main_type_id;
		if(Page::get_page_user_id()){
			$this->pguser_id=Page::get_page_user_id();
		}
	}
	public function get_types_list(){
		if(!$this->selected){
			$this->selected=$this->get_ctype_id();
		}
		$this->selectedar=$this->get_all_parents($this->selected);
		$this->ctype=$this->selected;
		$this->full_name_edit=$this->full_name_edit;
		$this->ad=$this->display_ids;
		$ht=Request::get_ht();
		if($this->selection==1){
			$tree_cont=$this->build_sel($this->start_id);
			return   $tree_cont?"<div class='ttree'>$tree_cont</div>":false;
		}else{
			$tree_cont=$this->build_childs($this->start_id, "$ht/type/");
			return  $tree_cont?"<ul class='ttree'>$tree_cont</ul>":false;
		}
	}

	private function get_all_parents($id){
		$res=array();
		$cid=h2i($id);
		$res[]=$cid;
		while (!$this->get_start($cid)){
			$cid=$this->get_parent($cid);
			$res[]=$cid;
		}
		return $res;
	}
	

	private function get_childs($id, $cnt=false){
		$id=h2i($id);
		$get_childs=&$this->get_childs[$id][$this->selection];
		if((!$cnt||$this->get_childs_cnt[$id][$this->selection]<$cnt)&&$this->get_childs_cnt[$id]!==false){
			$pub=1;
			if($this->pguser_id){
				$userfil="and user_id=".$this->pguser_id;
				$pub=0;
			}
			if($this->selection&&User::get_c_id()){
				$userfil="and user_id=".User::get_c_id();
				$pub=0;
			}
			$lim=$cnt?"limit 0,$cnt":"";
			$result=my_q("select id from $this->table where parent=$id and ((pub>=$pub $userfil) or pub=1) order by rate desc, name $lim");
			//cho "select id from type where parent=$id and ((pub>=$pub $userfil) or pub=1)";
			for($i=0; $i<my_n($result); $i++){
				$get_childs[$i]=my_r($result, $i, "id");
			}
		}
		if(count($get_childs)<$cnt){$this->get_childs_cnt[$id][$this->selection]=false;}
		
		return $get_childs;
	}

	private function build_selection($id){
		$childs=$this->get_childs($id);
		$res.=<<<EOQ
				<option value="-1">нет
EOQ;
		if(count($childs)<1){return false;}
		for($i=0; $i<count($childs); $i++){
			$cid=$childs[$i];
			$name=$this->get_name($cid);
			$sld=in_array($cid, $this->selectedar)?"selected='true'":"";
			$res.=<<<EOQ
				<option $sld $sld value="$cid">$name
EOQ;
		}
		return $res;
	}

	private function get_name($id){
		$get_name=&$this->get_name[$id];
		if($get_name==null){
			$get_name=my_fst("select name from $this->table where id=$id", "name");
		}
		return $get_name;
	}

	private function get_full_name($id){
//		$get_full_name=&$this->get_full_name[$id];
//		if($get_name==null){
			$get_name=my_fst("select full_name from $this->table where id=$id", "full_name");
//		}
		return $get_name;
	}

	private function get_link($id){
		$get_link=&$this->get_link[$id];
		if($get_link==null){
			$get_link=my_fst("select link from $this->table where id=$id", "link");
		}
		return $get_link;
	}
	


	private function get_parent($id){
		$id=h2i($id);
		$get_parent=&$this->get_parent[$id];
		if($get_parent==null){
			$get_parent=my_fst("select parent from $this->table where id=$id", "parent");
			if(!$get_parent&&vars::$debug){
				print "select parent from $this->table where id=$id";
				print_br(debug_backtrace());
				exit;
			}
		}
		return $get_parent;
	}

	private function build_sel($id){
		$nchilds=count($this->get_childs($id));
		$selection=$this->build_selection($id);
		if(!$selection){
			$this->list_exists = false;
		}else{
			$selection=<<<EOQ
<select class="tobj" name="s$id">
	$selection
</select>
EOQ;
		}
		$childdivs=$this->build_childs($id, "");
		$hidden=in_array($id, $this->selectedar)?"":"hidden";
		$res.=<<<EOQ
			<div class="$hidden par">
			<input type=hidden name=s0 value="$id" />
			$selection
			$childdivs
			</div>
EOQ;
			return $res;

	}
	
	private function have_childs($id){
		//cho $id;
		if(count($this->get_childs($id, 1))||User::get_rate()>=st_vars::$rate_add_type||User::is_page_admin()){return true;}else{return false;}
		
	}

	public function build_childs($id, $plk=""){
		$se=$this->selection;
		if($se==1){
			$childs=$this->get_childs($id);
			if(count($childs)<1){
				return false;
			}
			for($i=0; $i<count($childs); $i++){
				$cid=$childs[$i];
				$cchildsn=count($this->get_childs($cid));
				if($cchildsn<1){continue;}
				$selection=$this->build_selection($cid);
				$childdivs=$this->build_childs($cid, "");

				$hidden=in_array($cid, $this->selectedar)?"":"hidden";

				$res.=<<<EOQ
<div class="t$cid $hidden par">
<select class="tobj" name="s$cid">
$selection
</select>
$childdivs
</div>
EOQ;
			}
		}else{
			require_once 'phpscr/user.php';
			require_once 'phpscr/functions.php';
			$childs=$this->get_childs($id);
			$childsn=count($childs);
			$crate=User::get_rate();
			$pguser_id=$this->pguser_id;
			if($crate>st_vars::$rate_add_type||($pguser_id&&$pguser_id==User::get_id())){
				$cpp=1;
				$addb=$this->add_button($id, $pguser_id);
			}
			$childsn+=$cpp;
			if($childsn<1){return false;}


			for($i=0; $i<count($childs); $i++){
				$cid=$childs[$i];

				$author=$this->get_author($cid);
				$author_name=User::get_login($author);
				$pub=$this->get_pub($cid);


				$ab=($this->ad==1)?" id:$cid":"";
				$parent=$this->get_parent($cid);
				$name=$this->get_name($cid);
				$full_name=$this->get_full_name($cid);
				$link=$this->get_link($cid);
				$cplk=$plk.$link."/";
				if($author_name&&!$pub){$cplk=User::make_link($cplk, $author_name);}

				//$cchilds=$this->get_childs($cid);
				//$cchildsn=count($cchilds)+$cpp;
				//$childdivs=$this->build_childs($cid, $cplk);
				
				$childdivs=false;
				if(in_array($cid, $this->selectedar)){
					$bulopened=true;
					$hidden="";
					$childdivs=$this->build_childs($cid, $cplk);
				}else{
					$bulopened=false;
					$hidden="hidden";
					$mw_load="mw_load='/ofunc/get_type_childs.php?t=$cid&l=$cplk&pguid=$this->pguser_id&display_ids=$this->display_ids'";
				}
				$ht = Request::get_ht();
				$loading=<<<EOQ
<td>
	<img class="loading inline" src="$ht/files/templates/ultimate/images/loading.gif" />
</td>				
EOQ;
				if($cid==$this->ctype){
					$typelink="<a class=curent href='$cplk'>$name</a>";
				}else{
					$typelink="<a href='$cplk'>$name</a>";
				}
				
				$obul=($this->have_childs($cid))?Page::get_obul($bulopened):"";
				
				$type_user_id=$this->get_author($cid);
				if($this->full_name_edit){$full_name_ed=<<<EOQ
				<td>
                    <input name=full_name value='$full_name' class=small placeholder='full name' />
                </td>
EOQ;
				}
				//				cho "$crate>=".st_vars::$rate_edit_type;
				//			cho User::get_id()."==$type_user_id";
				if($crate>=st_vars::$rate_edit_type||(User::get_id()&&User::get_id()==$type_user_id)){
					$sb="<button type=button class='switch edit-s small'></button>";
					
					$sw=<<<EOQ
<tr class="openobj olist hidden sw">
    <td>
        <form action='$ht/func/typeedit.php' method=POST class=typeed>
            <table class=comp>
                <tr>
                    <td>
                        <input name=name value='$name' class=small placeholder='name' />
                    </td>
                    $full_name_ed
                    <td>
                        <input name=link value='$link' class=small />
                    </td>
                    <td>
                        <input name=parent value='$parent' class=small />
                    </td>
                    <td>
                        <input name=page_user_id value='$pguser_id' type=hidden />
                        <input name=id value='$cid' type=hidden />
                        <input name=table value='$this->table' type=hidden />
                        <input type=submit value=Go class=small />
                    </td>
                </tr>
                <tr>
                    <td colspan=4>
                        <span class=err></span>
                    </td>
                </tr>
            </table>
        </form>
    </td>
    <td>
    $sb
    </td>
    <td>
    </td>
</tr>
EOQ;
				}
				$res0=<<<EOQ
<li class="olist swp">
<table class=comp>
	<tr class="openobj olist sw">
		<td>$obul</td>
		<td>$typelink$ab
		$sb
		<ul $mw_load class="openobj $hidden">
		$childdivs
		</ul>
		</td>
		$loading
	</tr>
	$sw
</table>
</li>
EOQ;
	if(!$this->have_childs($cid)){$resd.=$res0;}else{$resu.=$res0;}

			}
			$res=$resu.$resd;
			$res.=$addb;
		}
		return $res;
	}

	private function add_button($id, $pguser_id=false){
		$fullnameed=$this->full_name_edit?"<td><input placeholder='fullname' name='full_name' class='small'></input></td>":"";
		return <<<EOQ
<li class='olist'>
		<table class=comp>
			<tr>
				<td>
					<button class='plus open'></button></td>
					</td>
					<td class='openobj hidden'>
					<form action='/func/newtype.php' method='POST' class='newt inline'>
					<table>
						<tbody>
						<tr class='conp'>
							<td><input placeholder='name' name='name' class='conf small'></input></td>
							<td><input placeholder='link' name='link' class='cont link small'></input></td>
							$fullnameed
							<td>
								<input name=page_user_id value='$pguser_id' type=hidden />
								<input name=table value='$this->table' type=hidden />
								<input type='hidden' value='$id' name='parent'/>
								<input type='submit' value='Go'></input>
							</td>
						</tr>
						<tr colspan=0><td colspan=5><span class=err></span></td></tr>
						</tbody>
					</table>
					</form>
				</td>
			</tr>
		</table>
</li>
EOQ;
	}
	
	
	
	
	public function get_author($id){
//		if($get_author==null){
			$get_author=my_fst("select user_id from $this->table where id=$id", "user_id");
//		}
		return $get_author;
	}
	public function get_pub($id){
//		if($get_pub==null){
			$get_pub=my_fst("select pub from $this->table where id=$id", "pub");
//		}
		return $get_pub;
	}
	
	public function get_start($id){
		$id=h2i($id);
//		if($get_start==null){
			$get_start=my_fst("select start from $this->table where id=$id", "start");
//		}
		return $get_start;
	}
	
	public function get_all_childs($id=false){
//		if(!$res){
			$res=array();
			$ctree=get_ttree($id);
			if(Page::get_page_user_id()){
				$pguser_id=Page::get_page_user_id();
				$ufil="and user_id=$pguser_id";
				$pub=0;
			}else{
				$pub=1;
			}
			//cho h2s("select * from type where pub>=$pub $ufil and tree like '%$ctree' order by tree asc");
			$q=my_q("select * from $this->table where pub>=$pub $ufil and tree like '%$ctree' order by tree asc");
			for($i=0; $i<my_n($q); $i++){
				$res[$i]=my_r($q, $i, "id");
			}
			//rint_r($res);
			return $res;
//		}
	}
	
	public function get_ctype_id(){
		$ftree=Page::get_ftree();
		if($this->table!="type"){return false;}
		if($ftree[0]!="type"){
			return 1;
		}
		$cid=1;
		$parent=0;
		$ftree=get_ftree();
		$file=get_file();
		$ftree[count($ftree)]=$file;
		for($i=1; $i<count($ftree); $i++){
			//cho $cid."q";
			$cname=$ftree[$i];
			$cname=h2s($cname);
			if(!$cname){
				continue;
			}
			$pub=1;
			if(Page::get_page_user_id()){
				$pub=0;
				$ufil="and user_id=".Page::get_page_user_id();
			}
			if(!$cid){
				l404();
			}
			$cid=my_fst("select id from $this->table where pub>=$pub $ufil and link='$cname' AND parent=$cid", "id");
		}
		$file=h2s($file);
		$cid=h2s($cid);
		return $cid?$cid:self::$start_id;
		//		$tree=get_ttree($cid);
		//		$tres=my_q("select * from type where pub>=$pub and tree like '%$tree'");
		//
		//		for($i=0; $i<my_n($tres); $i++){
		//			$types_ar[$i]=my_r($tres, $i, "id");
		//		}
	}
}
?>