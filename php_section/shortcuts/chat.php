<?php

require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once 'phpscr/functions.php';

$page=new Page();

$ftree[count($ftree)]=$file;

$page->content=<<<EOQ
    <center><img width="0" height="0" border="0" src="http://c.gigcount.com/wildfire/IMP/CXNID=2000002.0NXC/bT*xJmx*PTEzMDA4ODc*NjYwNjQmcHQ9MTMwMDg4ODA4MDA*NCZwPTUzMTUxJmQ9Jmc9MiZvPTA3NmZjZTU4N2Y4ZTQxMTM4NGFi/M2YyMDBhODM4ZDEyJm9mPTA=.gif" style="visibility: hidden; width: 0px; height: 0px;"><embed width="540" height="405" align="middle" pluginspage="http://xat.com/update_flash.shtml" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" flashvars="id=137364322" name="chat" bgcolor="#000000" quality="high" src="http://www.xatech.com/web_gear/chat/chat.swf"></center>
EOQ;

//build main post
$page->content=simple_post($page->content);
//end

$sv=st_vars::$script_version;

$page->title="Чат";
$page->head=<<<EOQ



<script type="text/javascript" src="$ht/files/jscripts/post.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/new.js?$sv"></script>
EOQ;

process_page($page);
?>