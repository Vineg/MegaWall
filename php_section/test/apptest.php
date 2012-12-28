<?php

def_page(<<<EOQ
<script>
	bg=$("<div class='mw_iframe_klsdjfla' style=\"z-index:10000;position:fixed; width:100%; height:100%; background:black; opacity:0.6; marginpadding:0; top:0; left:0;\"></div>");
	cl=$("<div class='mw_iframe_klsdjfla' style=\"overflow:scroll;z-index:10001;position:fixed; width:100%; height:100%; marginpadding:0; top:0; left:0;\">");
	div=$("<div id=\"mw_pd_jhbfkcl\" style=\"z-index:10002;width:900px; height:620px; background:white; opacity:1; margin-top:30px;margin:0 auto; position:relative;\"></div>");
	obj=$("<div style='width:800px; margin:0 auto; margin-top:40px; padding-top:40px;'><embed width=\"800\" height=\"500\" align=\"middle\" pluginspage=\"http://www.adobe.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" flashvars=\"\" allowfullscreen=\"false\" name=\"gamefile\" quality=\"high\" src=\"http://armorgames.com/files/games/solarmax-11965.swf\"></embed></div>");
	cbut=$("<a id=\"bottomNavClose\" style='position:absolute; right:10px; bottom:10px;'><img src=\"/files/templates/ultimate/images/closelabel.gif\"></a>");
	$("body").append(bg).append(cl.append(div.append(obj).append(cbut)));

</script>
EOQ
,"",true);
?>