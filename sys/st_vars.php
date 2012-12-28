<?php
class st_vars{
	static $uscript_last_version="1.3.2";
	//static $css_location="/files/templates/ultimate/style.css?2";
	static $style_version=1.4;
	static $script_version=1.6;

	static $proj="megawall";
	static $console=false;
	static $ya_user="Vineg", $ya_key="03.25898649:edc7349286c85f73add10d28ffb6f9ae", $ya_urls_per_req=100, $max_ya_groups_per_request=100;
	static $empthy_pages = array("yandex_6a82c3ae4cacda97.txt","yandex_4eb1cc59f7fb43f1.txt");
	static $max_link_value=10, $check_code_string="mw_link_place", $links_per_day=10, $hosts_per_day=3;
	static $def_values=array(vk_user=>array(email=>"", pass=>"", remixsid=>""));
	static $def_template="ultimate";
	static $minlog=4, $maxlog=50, $minpass=5, $maxpass=50;
	static $keyword_array=array("как", "зачем");
	static $rate_authenticate_fine=-0.1;
	static $rate_user_posts_remove=-0.1;
	static $rate_post_auto_remove=0.2;
	static $max_width=633;
	static $system_id=1;
	static $rate_delete=2;
	static $rate_add_type=2;
	static $rate_edit_type=2;
	static $rate_upload=2;
	static $rate_ref=0.04;
	static $aadd=2;
	static $rate_safe_sites=2;
	static $rate_new_post=0;
	static $rate_new_comment=0;
	static $rate_edit_post=0.003;
	static $rate_new_user=0.005;
	static $rate_invited_user=0.8;
	static $rate_bad_invite_fine=-1;
	static $rate_per_invited_user=1, $rate_admin=4;
	static $host="localhost";
	static $rate_no_captcha=0.7;
	static $standalone=0;
	static $max_post_len=20000;
	static $max_fpost_len=500000;
	static $max_votes=30;
	static $max_n_len=100;
	static $ud="/files/ud";
	static $rate_safe_src=0.5;
	static $ug_users=false;
	static $request_aliases=array();///seonuke=>"/mysites/"
	static $def_page=12;
	static $type_main=1;
	static $def_proj = "megawall";
	
	static $def_user_page=<<<EOQ
<table class=w100><tr><td style="width:200px;">
<img style="width:100%;" src="{\$photo}" onerror="brokenimage(this);"/>
</td><td>
<p><b>{\$username}</b></p>
<hr/><p>Рейтинг:{\$rate}</p>
<hr/><p>Постов:{\$posts}</p>
<hr/><p>На сайте {\$intime}</p>
</td>
</tr>
</table>
EOQ;
	static $templates_ar=array("ultimate", "punta");
	static $fields=array("vk_user"=>array("vk_id", "first_name", "last_name", "user_id", "photo", "photo_rec", "remixsid", "pass", "email","last_vk_friends_list_update", "vk_login_fail"));
	static $ya_files=array("yandex_4eb1cc59f7fb43f1.txt", "yandex_7c49de6b6fe11190.txt");
}

//pub::
//2 - all parent types
//1 - only type where submited
//0 - user page
//-1 - comment
//-2 - post page
//-3 - ofunc
//-4 - nowhere
?>