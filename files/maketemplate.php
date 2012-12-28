<?php
if(!vars::$debug){exit;}
$str=fr("/files/templates/ultimate.html");
$s=preg_replace("/{\\\$(\w+)}/e", "'<?php print \$page->\\1; ?>'", $str);
fp("/files/templates/ultimatep.html", $s);