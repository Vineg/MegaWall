<?php
$k=Request::get_file();
$plist = fr("admin/proxy_list.txt");
$plist = explode("\r\n", $plist);
$proxy_cnt = count($plist);
for($i=0; $i<$proxy_cnt; $i++){
	$plist[$i]="PROXY ".$plist[($i+$k)%$proxy_cnt];
}
$plist = join(";", $plist);
print<<<EOQ
function FindProxyForURL(url, host)
{
	return "$plist";
}
EOQ;
?>