<?php
$primary_dns = "ns.megawall.ru";
$secondary_dns = "ns.secondary.net.ua";
$serv_ip = "93.100.152.70";
$domain_name = $argv[1];
$sitesdir = "/etc/bind/sites/$domain_name";
$named_file = "/etc/bind/named/etc/named.conf";



include_once 'phpscr/files/functions.php';
mkdir($sitesdir);
$date=date("Ymdh");
$conf = <<<EOQ
\$TTL 3D; Forward zone DNS->IP
@	IN	SOA	$domain_name.	hostmaster.$domain_name. (
			$date	;
			8H		;
			2H		;
			1W		;
			1D )		;
	IN	NS	$primary_dns.
	IN	NS	$secondary_dns.
$domain_name.	IN	A	$serv_ip
*.$domain_name.	IN	A	93.100.152.70
EOQ;
fp("$sitesdir/forward.zone", $conf);
$namedap = <<<EOQ
zone "$domain_name" IN {
	type master;
	file "sites/$domain_name/forward.zone";
	allow-transfer { 127.0.0.1;193.201.116.2; };
	allow-update { none; };
	allow-query { any; };
	zone-statistics yes;
	notify yes;
	also-notify { 193.201.116.2; };
};

EOQ;
fp($named_file, $namedap, true);