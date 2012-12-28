<?php
function get_post_link($id) {
	$id = h2s ( $id );
	return my_fst ( "select link from post where id=$id", "link" );
}

function get_post_name($id) {
	return Post::get_name ( $id );
}

function get_comments_num($id) {
	$id = h2s ( $id );
	return my_qn ( "select id from post where parent='$id' and pub>=-1" );
}

function get_post_parent($id) {
	return my_fst ( "select parent from post where id='$id'", "parent" );
}

function get_post_author($id) {
	return my_fst ( "select author_id from post where id='$id'", "author_id" );
}

function get_post_type($id) {
	return my_fst ( "select type_id from post where id='$id'", "type_id" );
}

function get_type_parent($id) {
	return my_fst ( "select parent from type where id='$id'", "parent" );
}

function get_type_rate($id) {
	return my_fst ( "select rate from type where id='$id'", "rate" );
}

function get_type_name($id) {
	return my_fst ( "select name from type where id='$id'", "name" );
}

function get_type_full_name($id) {
	return my_fst ( "select full_name from type where id='$id'", "full_name" );
}

function destroy_post($id, $why) {
	my_q ( "update post set pub=-2 where id=$id" );
	my_q ( "insert into log(msg) value('post $id was removed by reason \"$why\"')" );
}

function change_post_url($post_id, $old, $new) {
	if ($post_id == - 1) {
		$post &= $_SESSION ["lpost"];
		$post_content = $post [text];
		$npost_content = str_replace ( "$old", $new, $post_content );
		$post [text] = $npost_content;
	
	} else {
		$post_content = Post::get_post_content ( $post_id );
		$npost_content = str_replace ( "$old", $new, $post_content );
		mysql_query ( "update post set content='$npost_content' where id='$post_id'" );
	}
}
;



class Content {
	static function get_content_rate($content_id) {
		return my_fst ( "select rate from post_content where id=$content_id", "rate" );
	}
	static function delete_comp($content_id) {
		my_q ( "delete from post_content where id=$content_id" );
	}
	
	static function submit(&$post) {
		//cho $post->author_id==false;
		if ($post->id) {
			$v = Post::get_last_version ( $post->id );
		} else {
			$v = 0;
		}
		if (get_class ( $post ) != Post) {
			print_br ( debug_backtrace () );
		}
		$post->version = $v + 1;
		if ($post->id) {
			$sad = ", post_id";
			$aad = ", '$post->id'";
		}
		$post->ftext = my_s ( $post->ftext );
		$post->text = my_s ( $post->text );
		$source = clone $post;
		$source->ftext = "";
		$source->text = "";
		$source = serialize ( $source );
		$source = my_s ( $source );
		$post->source = my_s ( $post->source );
		$post->name = my_s ( $post->name );
		$post->rate = h2d ( $post->rate );
		
		//$post->link=my_s($post->link);
		my_q ( "insert into post_content(text, ftext, source, author_id, version, name, rate, a, daily_rate$sad) values ('$post->text', '$post->ftext', '$source', '$post->author_id', '$post->version', '$post->name', '$post->rate', 0, '$post->rate'$aad)" );
		$cont_id = mysql_insert_id ();
		return $cont_id;
	}
}

class Post_const {
	static $post_select = "(select  main_type_id, version, post_content.source,post_content.rate as content_rate,content_id,post.id,post.a,safe,post.vars,type_id,link,name,post.author_id, post.rate, post.date, pub, parent, post.daily_rate, post_content.text as content, post_content.ftext as fcontent from post_content,post where post_content.id=content_id)post";
	static $content_select = "(select version, post_content.rate,post_content.id as content_id,post.id,post.a,safe,post_content.vars,type_id,link,name,post_content.author_id, post.rate as post_rate, post.date, pub, parent, post.daily_rate, post_content.text as content, post_content.ftext as fcontent from post_content,post where post_content.post_id=post.id)content";
	static function rate_comp($a, $b) {
		if ($a->rate == $b->rate) {
			return 0;
		}
		return $a->rate < $b->rate ? - 1 : 1;
	}
}

function check_contents($post_id) {
	$post_rate = Post::get_rate ( $post_id );
	$ccontent_id = my_fst ( "select content_id from post where id=$post_id", "content_id" );
	$ccontent_rate = my_fst ( "select rate from post_content where id=$ccontent_id", "rate" );
	$npost = my_q ( "select * from post_content where post_id=$post_id order by rate desc limit 0,1" );
	$max_rate = my_fst ( $npost, "rate" );
	if ($max_rate > $ccontent_rate + my_sqrt_z ( $post_rate / 10 ) + 0.05) {
		$max_id = my_fst ( $npost, "id" );
		my_q ( "update post set content_id=$max_id where id=$post_id" );
		$author_id = Post::get_author_id ( $post_id );
		//cho h2s("delete from $from where id=$post_id and author_id=1 and content like '<!--cid:$max_id-->%'");
		delete_sys_cid_comments ( $post_id, $max_id );
		dec_contents_rate ( $post_id );
		return true;
	} else {
		return false;
	}
}

function dec_contents_rate($post_id) {
	my_q ( "update post_content set rate=rate*0.7 where post_id=$post_id" );
}

function delete_sys_cid_comments($post_id, $cid) {
	$from = Post_const::$post_select;
	$r = my_q ( "select * from $from where parent=$post_id and author_id=1 and content like '<!--cid:$cid-->%'" );
	for($i = 0; $i < my_n ( $r ); $i ++) {
		$pcid = my_r ( $r, $i, "content_id" );
		$post_id = my_r ( $r, $i, "id" );
		Post::delete_comp ( $post_id );
		Content::delete_comp ( $pcid );
		$d = true;
	}
	return $d;
}
?>